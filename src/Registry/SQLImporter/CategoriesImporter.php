<?php

namespace App\Registry\SQLImporter;

use App\Entity\Category;
use App\Model\Importer\EntityImporterInterface;

class CategoriesImporter implements EntityImporterInterface
{
    public function getTotal(): int
    {
        return 100000;
    }

    public function getKey(): string
    {
        return 'categories';
    }

    public function getEntities(): iterable
    {
        for ($i = 0; $i < $this->getTotal(); ++$i) {
            $category = new Category();
            $category->setName(random_int(1, 10000));
            yield $category;
        }
    }
}
