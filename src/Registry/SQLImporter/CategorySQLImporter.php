<?php

namespace App\Registry\SQLImporter;

use App\Entity\Category;
use App\Model\Importer\SQLImporterInterface;

class CategorySQLImporter implements SQLImporterInterface
{
    public function getTotal(): int
    {
        return 100000;
    }

    public function getKey(): string
    {
        return 'categories';
    }

    public function getValues(): iterable
    {
        for ($i = 0; $i < $this->getTotal(); $i++) {
            yield [random_int(1, 1000)];
        }
    }

    public function getEntityClass(): string
    {
        return Category::class;
    }

    public function getColumnNames(): array
    {
        return ['name'];
    }
}
