<?php

namespace App\Service;

use App\Command\StopwatchProgressBar;
use App\Model\Importer\SQLImporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SQLImporter
{
    /** @var SQLImporterInterface[] */
    private $sqlImporters;

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(iterable $importers, EntityManagerInterface $em)
    {
        $this->sqlImporters = $importers;
        $this->em = $em;
    }

    public function import(SymfonyStyle $io): void
    {
        foreach ($this->sqlImporters as $importer) {
            $this->importOne($importer, $io);
        }
    }

    private function importOne(SQLImporterInterface $importer, SymfonyStyle $io): void
    {
        $total = $importer->getTotal();
        $key = $importer->getKey();
        $progressBar = new StopwatchProgressBar($io, $key, $total);

        $entityClass = $importer->getEntityClass();
        $metaData = $this->em->getClassMetadata($entityClass);
        $tableName = $metaData->table['name'];

        $sql = sprintf('INSERT INTO %s (%s) VALUES ', $tableName, implode(', ', $importer->getColumnNames()));

        $count = 0;
        $processed = [];
        foreach ($importer->getValues() as $key => $columnValues) {
            $progressBar->setProgress($key);
            $processed[] = $this->valueToString($columnValues);
            $count++;
            if ($count >= 200) {
                $this->insert($sql, $processed);
                $count = 0;
                $processed = [];
            }
        }
        $this->insert($sql, $processed);
    }

    private function insert(string $sql, array $processed): void
    {
        if (empty($processed)) {
            return;
        }
        $statement = sprintf('SET FOREIGN_KEY_CHECKS = 0;SET unique_checks=0;SET autocommit=0;%s %s;', $sql, implode(', ', $processed));
        $this->em->getConnection()->exec($statement);
    }

    private function valueToString(array $values): string
    {
        $processed = array_map(function (string $field) {
            return sprintf('"%s"', $field);
        }, $values);

        return sprintf('(%s)', implode(', ', $processed));
    }
}
