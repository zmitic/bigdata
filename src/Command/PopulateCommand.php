<?php

namespace App\Command;

use App\Command\Config\SQLImportConfig;
use App\Helper\Storage;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PopulateCommand extends AbstractImportCommand
{
    protected static $defaultName = 'app:populate';

    protected function configure(): void
    {
        $this
            ->setDescription('Import small amount of data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->truncateDb();
        $this->bulkInsert2($io);
    }

    public function bulkInsert2(SymfonyStyle $io): void
    {
        $this->warmup();
        $this->sqlImporter->import($io);
        $this->cleanup();
    }

    private function bulkInsert(SymfonyStyle $io): void
    {
        $storage = new Storage();
        $config = new SQLImportConfig();
        $callables = $config->getCallables();

        $this->warmup();
        foreach ($callables as $config) {
            $this->executeFromConfig($io, $config, $storage);
        }
        $this->cleanup();
    }

    private function executeFromConfig(SymfonyStyle $io, array $config, Storage $storage): void
    {
        ['size' => $size, 'key' => $key, 'callback' => $callable] = $config;
        if ($size < SQLImportConfig::BULK_VALUE) {
            throw new \InvalidArgumentException(sprintf('Size must be greater than %d, %d given.', SQLImportConfig::BULK_VALUE, $size));
        }
        $progressBar = new StopwatchProgressBar($io, $key, $size);
        /** @var iterable $statements */
        $statements = $callable($size / SQLImportConfig::BULK_VALUE);
        $count = 0;
        $combined = 'START TRANSACTION; SET autocommit=0;';
        foreach ($statements as $progress => $sqlStatement) {
            ++$count;
            $combined .= $sqlStatement.';';
            if ($count >= 200) {
                $combined .= 'COMMIT;';
                $progressBar->setProgress($progress * SQLImportConfig::BULK_VALUE);
                $this->executeSql($combined);
                $combined = 'START TRANSACTION; SET autocommit=0;';
                $count = 0;
            }
        }
        // left-overs
        if ($combined) {
            $this->executeSql($combined);
        }
    }
}
