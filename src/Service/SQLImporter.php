<?php

namespace App\Service;

use App\Helper\StopwatchProgressBar;
use App\Model\Importer\EntityImporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SQLImporter
{
    /** @var EntityImporterInterface[] */
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
        $this->warmup();
        foreach ($this->sqlImporters as $importer) {
            $this->importOne($importer, $io);
        }
    }

    private function importOne(EntityImporterInterface $importer, SymfonyStyle $io): void
    {
        $total = $importer->getTotal();
        $key = $importer->getKey();
        $progressBar = new StopwatchProgressBar($io, $key, $total);

        $count = 0;
        $stored = [];
        foreach ($importer->getEntities() as $key => $entity) {
            $stored[] = $entity;
            ++$count;
            if ($count >= 200) {
                $progressBar->setProgress($key);
                $this->flushEntities($stored);
                $count = 0;
                $stored = [];
            }
        }
        $this->flushEntities($stored);
    }

    private function flushEntities(array $entities): void
    {
        if (empty($entities)) {
            return;
        }
        foreach ($entities as $entity) {
            $this->em->persist($entity);
        }
        $this->em->flush();
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
}
