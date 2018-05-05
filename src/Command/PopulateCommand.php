<?php

namespace App\Command;

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
        $this->bulkInsert($io);
    }

    public function bulkInsert(SymfonyStyle $io): void
    {
//        $this->warmup();
        $this->sqlImporter->import($io);
        $this->cleanup();
    }
}
