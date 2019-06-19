<?php

declare(strict_types=1);

namespace App\Helper;

use App\Model\IdentifiableEntityTrait;
use Doctrine\ORM\QueryBuilder;
use Generator;

class BulkExporter
{
    private $qb;

    public function __construct(QueryBuilder $qb)
    {
        $this->qb = $qb;
    }

    /**
     * @param callable $next
     *
     * @return Generator|IdentifiableEntityTrait[]
     */
    public function export(callable $next): Generator
    {
        $qb = $this->qb;
        $cloned = clone $qb;
        $count = 0;

        do {
            $results = $this->getResults($cloned);
            if (0 === count($results)) {
                return;
            }
            foreach ($results as $result) {
                yield $count => $result;
                ++$count;
            }
            $last = array_pop($results);
            $cloned = clone $qb;
            $next($cloned, $last);
        } while (null !== $last);
    }

    private function getResults(QueryBuilder $qb)
    {
        $qb->setMaxResults(1000);

        return $qb->getQuery()->getResult();
    }
}
