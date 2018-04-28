<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Manufacturer;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Helper\Storage;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportSmallCommand extends AbstractImportCommand
{
    private const NR_OF_ORDERS = 10000;
    private const NR_OF_ITEMS_PER_ORDER = 10;

    protected static $defaultName = 'app:import:small';

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

        $entities = $this->generateEntities($limit, $builder, $storage);

        $progressBar = new StopwatchProgressBar($io, $key, $limit);
        $this->bulkPersister->persist($entities, $batchSize, function ($entities, $count) use ($progressBar, $key, $storage) {
            $progressBar->setProgress($count);
            foreach ((array)$entities as $entity) {
                $storage->store($key, (string)$entity->getId());
            }
        });
        $progressBar->clear();
    }

    private function generateEntities(int $limit, callable $builder, Storage $storage): \Generator
    {
        for ($i = 1; $i <= $limit; $i++) {
            $result = $builder($i, $storage);
            yield $result;
        }
    }

    private function getBuilders()
    {
        yield [1000, 'categories', function (int $iteration) {
            $category = new Category();
            $category->setName(sprintf('Category_%d', $iteration));

            return $category;
        }];

        yield [10000, 'manufacturers', function (int $iteration) {
            $manufacturer = new Manufacturer();
            $manufacturer->setName(sprintf('Manufacturer_%d', $iteration));

            return $manufacturer;
        }];

        yield [100000, 'products', function (int $iteration, Storage $storage) {
            $randomId = $storage->getOneByRandom('manufacturers');
            /** @var Manufacturer $manufacturer */
            $manufacturer = $this->em->getReference(Manufacturer::class, $randomId);

            $product = new Product();
            $product->setName(sprintf('Product_%d', $iteration));
            $product->setManufacturer($manufacturer);

            $randomCategoryIds = $storage->getRandom('categories', random_int(1, 10));
            foreach ($randomCategoryIds as $randomCategoryId) {
                /** @var Category $category */
                $category = $this->em->getReference(Category::class, $randomCategoryId);
                $product->addCategory($category);
            }

            return $product;
        }];

        yield [100000000, 'orders', function (int $iteration, Storage $storage) {
            return new Order();
        }];

        yield [self::NR_OF_ORDERS, 'order_items', function (int $iteration, Storage $storage) {
            /** @var Order $order */
            $order = $this->em->getReference(Order::class, $storage->getOneByRandom('orders'));
            $productIds = $storage->getRandom('products', self::NR_OF_ITEMS_PER_ORDER);
            foreach ($productIds as $productId) {
                /** @var Product $product */
                $product = $this->em->getReference(Product::class, $productId);
                $item = new OrderItem($order, $product, random_int(1, 10));
                $createdAt = (new \DateTime())->modify(sprintf('-%d days', random_int(1, 3000)))
                    ->setTime(random_int(0, 23), random_int(0, 59), random_int(0, 59));
                $item->setCreatedAt($createdAt);

                yield $item;
            }
        }];
    }
}

