<?php

namespace App\Command;

use App\Entity\Manufacturer;
use App\Entity\Product;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportSmallCommand extends AbstractImportCommand
{
    protected static $defaultName = 'app:import:small';

    protected function configure(): void
    {
        $this
            ->setDescription('Import small amount of data')
//            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->truncateDb();
        $this->bulkInsert($io);
    }

    private function bulkInsert(SymfonyStyle $io): void
    {
        foreach ($this->getBuilders() as $config) {
            [$limit, $message, $builder] = $config;
            $progressBar = $io->createProgressBar($limit);
            $this->persistFromCallable($progressBar, $limit, $builder);
            $io->success($message);
        }
    }

    private function getBuilders()
    {
        yield [1000000, 'Manufacturers created', function (int $iteration) {
            $manufacturer = new Manufacturer();
            $manufacturer->setName(sprintf('Manufacturer_%d', $iteration));

            return $manufacturer;
        }];

        yield [10, 'Products created', function (int $iteration) {
            $product = new Product();
            $product->setName(sprintf('Product_%d', $iteration));

            return $product;
        }];
    }

    private function persistFromCallable(ProgressBar $progressBar, int $limit, callable $builder): void
    {
        $count = 0;
        $em = $this->em;
        $results = $this->createGenerator($limit, $builder);
        foreach ($results as $manufacturer) {
            $em->persist($manufacturer);
            $count++;
            if (($count % $this->batchSize) === 0) {
                $em->flush();
                $em->clear();
                $progressBar->advance($this->batchSize);
            }
        }

        $em->flush();
        $em->clear();
        $progressBar->finish();
    }

    private function createGenerator(int $limit, callable $builder): \Generator
    {
        for ($i = 1; $i <= $limit; $i++) {
            yield $builder($i);
        }
    }
}

