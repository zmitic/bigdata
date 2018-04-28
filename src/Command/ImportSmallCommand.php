<?php

namespace App\Command;

use App\Entity\Manufacturer;
use App\Entity\Product;
use App\Helper\Storage;
use App\Model\IdentifiableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;

class ImportSmallCommand extends AbstractImportCommand
{
    protected static $defaultName = 'app:import:small';

    protected function configure(): void
    {
        $this
            ->setDescription('Import small amount of data')
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
        $storage = new Storage();
        foreach ($this->getBuilders() as $config) {
            $this->persistFromCallable($io, $config, $storage);
        }
        $io->success('All done');
    }

    private function persistFromCallable(SymfonyStyle $io, array $config, Storage $storage): void
    {
        [$limit, $key, $builder] = $config;
        $batchSize = $this->batchSize;
        $storage->create($key, 1000);

        $stopWatch = new Stopwatch();
        $stopWatch->start($key);

        $progressBar = $this->createProgressBar($io, $key, $limit);
        $count = 0;
        $em = $this->em;

        /** @var IdentifiableTrait[] $entities */
        $entities = $this->createGenerator($limit, $builder, $storage);
        foreach ($entities as $entity) {
            $em->persist($entity);
            $storage->store($key, (string)$entity->getId());
            $count++;
            if (($count % $batchSize) === 0) {
                $em->flush();
                $em->clear();
                $event = $stopWatch->lap($key);
                $duration = $event->getDuration();
                $speed = round($count / $duration * 1000);
                $progressBar->advance($batchSize);
                $progressBar->setMessage(sprintf('Speed %d/sec', $speed), 'speed');
            }
        }

        $em->flush();
        $em->clear();
        $progressBar->finish();
        $progressBar->clear();
    }

    private function createGenerator(int $limit, callable $builder, Storage $storage): \Generator
    {
        for ($i = 1; $i <= $limit; $i++) {
            yield $builder($i, $storage);
        }
    }

    private function getBuilders()
    {
        yield [100, 'manufacturers', function (int $iteration) {
            $manufacturer = new Manufacturer();
            $manufacturer->setName(sprintf('Manufacturer_%d', $iteration));

            return $manufacturer;
        }];

        yield [100000000, 'products', function (int $iteration, Storage $storage) {
            $randomId = $storage->getRandom('manufacturers');
            /** @var Manufacturer $manufacturer */
            $manufacturer = $this->em->getReference(Manufacturer::class, $randomId);
            $product = new Product();
            $product->setName(sprintf('Product_%d', $iteration));
            $product->setManufacturer($manufacturer);

            return $product;
        }];
    }
}

