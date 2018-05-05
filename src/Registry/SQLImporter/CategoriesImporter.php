<?php

namespace App\Registry\SQLImporter;

use App\Entity\Category;
use App\Model\Importer\EntityImporterInterface;

class CategoriesImporter implements EntityImporterInterface
{
    public function getTotal(): int
    {
        return 1000000;
    }

    public function getKey(): string
    {
        return 'categories';
    }

    public function getEntities(): iterable
    {
        $total = $this->getTotal();
        for ($i = 0; $i < $total; ++$i) {
            $category = new Category();
            $category->setName('Categoy_'.random_int(1, $total));
            yield $category;
        }
    }
}
