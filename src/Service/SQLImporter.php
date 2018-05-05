<?php

namespace App\Service;

use App\Helper\StopwatchProgressBar;
use App\Helper\Storage;
use App\Model\IdentifiableTrait;
use App\Model\Importer\EntityImporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SQLImporter
{
    /** @var EntityImporterInterface[]|iterable */
    private $sqlImporters;

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(iterable $importers, EntityManagerInterface $em)
    {
        $this->sqlImporters = $importers;
        $this->em = $em;
    }

    /**
     * In case of early terminate, make sure DB is useful.
     */
    public function __destruct()
    {
        $this->cleanup();
    }

    public function import(SymfonyStyle $io): void
    {
        $storage = new Storage();
        $this->warmup();
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
        /** @var IdentifiableTrait[] $entities */
        $entities = $importer->getEntities($storage);
        foreach ($entities as $key => $entity) {
            $stored[] = $entity;
            ++$count;
            if ($count >= 200) {
                $progressBar->setProgress($key);
                $this->flushEntities($stored, $name, $storage);
                $count = 0;
                $stored = [];
            }
        }
        $this->flushEntities($stored, $name, $storage);
    }

    /**
     * @param IdentifiableTrait[] $entities
     */
    private function flushEntities(array $entities, string $key, Storage $storage): void
    {
        if (empty($entities)) {
            return;
        }
        foreach ($entities as $entity) {
            $this->em->persist($entity);
        }
        $this->em->flush();
        foreach ($entities as $entity) {
            $storage->store($key, (string)$entity->getId());
        }
        $this->em->clear();
    }

    protected function warmup(): void
    {
        $this->em->getConnection()->exec('SET FOREIGN_KEY_CHECKS = 0;SET unique_checks=0;SET autocommit=0;');
    }

    protected function cleanup(): void
    {
        $this->em->getConnection()->exec('SET FOREIGN_KEY_CHECKS = 1;SET unique_checks=1;SET autocommit=1;');
    }

    private function getImporters(): array
    {
        $importers = iterator_to_array($this->sqlImporters);
        usort($importers, function (EntityImporterInterface $a, EntityImporterInterface $b) {
            return $a->getOrder() <=> $b->getOrder();
        });

        return $importers;
    }
}
