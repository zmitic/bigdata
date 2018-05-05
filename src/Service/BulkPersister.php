<?php

namespace App\Service;

use App\Model\IdentifiableTrait;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class BulkPersister
{
    /** @var ObjectManager|EntityManager */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function stream(QueryBuilder $qb, callable $callable, int $batchSize = 2000): \Generator
    {
        $count = 0;
        $cloned = $this->cloneQb($qb, $batchSize);
        /* @var IdentifiableTrait[] $results */
        while (true) {
            $results = $cloned->getQuery()->getResult();
            if (empty($results)) {
                return;
            }

            foreach ($results as $result) {
                yield $result;
            }
            $count += \count($results);
            $cloned = $this->cloneQb($qb, $batchSize);
            $callable($cloned, end($results), $count);
        }
    }

    /**
     * @param \Generator|IdentifiableTrait[] $entities
     */
    public function persist(iterable $entities, int $batchSize, callable $onFlush = null): void
    {
        $flushTrigger = 0;
        $count = 0;
        $em = $this->em;
        $flushed = [];

        foreach ($entities as $entity) {
            ++$count;
            $this->persistEntity($entity, $flushed, $flushTrigger);

            if (0 === ($count % 50)) {
                $this->callOnFlush($onFlush, $flushed, $count);
            }
            if ($flushTrigger >= $batchSize) {
                $em->flush();
                $em->clear();
                $flushed = [];
                $flushTrigger = 0;
            }
        }

        $em->flush();
        $em->clear();
        $this->callOnFlush($onFlush, $flushed, $count);
    }

    private function persistEntity($entity, array &$flushed, int &$flushTrigger): void
    {
        if (is_iterable($entity)) {
            foreach ($entity as $item) {
                $this->em->persist($item);
                $flushed[] = $item;
                ++$flushTrigger;
            }
        } else {
            $this->em->persist($entity);
            $flushed[] = $entity;
            ++$flushTrigger;
        }
    }

    private function cloneQb(QueryBuilder $qb, int $batchSize): QueryBuilder
    {
        $clone = clone $qb;
        $clone->setMaxResults($batchSize);

        return $clone;
    }

    private function callOnFlush(?callable $onFlush, array $flushed, int $count): void
    {
        if ($onFlush) {
            $onFlush($flushed, $count);
        }
    }
}
