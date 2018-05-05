<?php

namespace App\Command\Config;

class SQLImportConfig
{
    private const NR_OF_CATEGORIES = 100000;
    private const NR_OF_MANUFACTURERS = 10000;
    private const NR_OF_ORDERS = 1000000;
    private const NR_OF_ITEMS_PER_ORDER = 10;

    public const BULK_VALUE = 50;

    public function getCallables(): \Generator
    {
        yield [
            'size' => self::NR_OF_CATEGORIES,
            'key' => 'categories',
            'callback' => [$this, 'getCategories'],
        ];
//
//        yield [
//            'size' => self::NR_OF_MANUFACTURERS,
//            'key' => 'manufacturers',
//            'callback' => [$this, 'getManufacturers'],
//        ];

//        yield [
//            'size' => self::NR_OF_ORDERS,
//            'key' => 'orders',
//            'callback' => [$this, 'getOrders'],
//        ];
//
//        yield [
//            'size' => 100000,
//            'key' => 'products',
//            'callback' => [$this, 'getProducts'],
//        ];
    }

    public function getCategories(int $size): \Generator
    {
        for ($i = 1; $i <= $size; ++$i) {
            $values = $this->createValues(function () use ($i) {
                return [
                    'Category_'.$i.'_'.random_int(1, 1000),
                ];
            });
            yield sprintf('INSERT INTO tbl_category (name) VALUES %s', $values);
        }
    }

    public function getManufacturers(int $size): \Generator
    {
        for ($id = 1, $limit = $size + 1; $id < $limit; ++$id) {
            yield sprintf('INSERT INTO tbl_manufacturer (id, name) VALUES ("%d", "%s_%04d")', $id, 'Manufacturer', $id);
        }
    }

    public function getOrders(int $size): \Generator
    {
        for ($i = 1; $i <= $size; ++$i) {
            $values = $this->createValues(function () {
                $date = new \DateTime();
                $date->modify(sprintf('-%d days', random_int(1, 4000)));
                $date->setTime(random_int(0, 23), random_int(0, 59), random_int(0, 59));
                $timeValues = $date->format('Y-m-d H:i:s');

                return [
                    $timeValues,
                    $timeValues,
                ];
            });

            yield sprintf('INSERT INTO tbl_order (created_at, updated_at) VALUES %s', $values);
        }
    }

    public function getProducts(int $id): \Generator
    {
        yield sprintf('INSERT INTO tbl_product (name) VALUES ("%s_%04d")', 'Product', $id);
    }

    private function createValues(callable $callable): string
    {
        $values = [];
        for ($i = 0; $i <= self::BULK_VALUE - 1; ++$i) {
            $value = $callable();
            $quoted = array_map(function (string $unquoted) {
                return sprintf('"%s"', $unquoted);
            }, $value);
            $values[] = sprintf('(%s)', implode(', ', $quoted));
        }

        return implode(', ', $values);
    }
}
