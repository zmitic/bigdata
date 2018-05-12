<?php

namespace App\Service;

use App\DependencyInjection\Compiler\EntitiesImporterPass;
use App\Helper\StopwatchProgressBar;
use App\Helper\Storage;
use App\Model\IdentifiableEntityTrait;
use App\Model\Importer\EntityImporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @see EntitiesImporterPass
 */
class EntitiesImporter
{
    /** @var EntityImporterInterface[] */
    private $importers;

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(array $importers, EntityManagerInterface $em)
    {
        $this->importers = $importers;
        $this->em = $em;
    }

    public function __destruct()
    {
        $this->em->getConnection()->exec('SET autocommit=1;SET unique_checks=1;SET foreign_key_checks=1;');
    }

    public function import(SymfonyStyle $io): void
    {
        $storage = new Storage();
        foreach ($this->getImporters() as $importer) {
            $this->importOne($importer, $io, $storage);
        }
    }

    private function importOne(EntityImporterInterface $importer, SymfonyStyle $io, Storage $storage): void
    {
        $total = $importer->getTotal();
        $name = $importer->getName();
        $progressBar = new StopwatchProgressBar($io, $name, $total);
        $storage->create($name, 1000);

        $count = 0;
        $stored = [];
        /** @var IdentifiableEntityTrait[] $entities */
        $entities = $importer->getEntities($storage);
        foreach ($entities as $progress => $entity) {
            $stored[] = $entity;
            if (0 === $count % 3000) {
                $this->flushEntities($stored, $name, $storage);
                $stored = [];
                $progressBar->setProgress($progress);
            }
            ++$count;
        }
        $this->flushEntities($stored, $name, $storage);
    }

    /**
     * @param IdentifiableEntityTrait[] $entities
     */
    private function flushEntities(array $entities, string $key, Storage $storage): void
    {
        if (empty($entities)) {
            return;
        }
        $em = $this->em;

        $em->transactional(function () use ($entities) {
            foreach ($entities as $entity) {
                $this->em->persist($entity);
            }
        });

//        $this->em->flush();
        foreach ($entities as $entity) {
            $storage->store($key, (string) $entity->getId());
        }
        $em->clear();
    }

    private function getImporters(): array
    {
        $importers = $this->importers;
        usort($importers, function (EntityImporterInterface $a, EntityImporterInterface $b) {
            return $a->getOrder() <=> $b->getOrder();
        });

        return $importers;
    }
}
