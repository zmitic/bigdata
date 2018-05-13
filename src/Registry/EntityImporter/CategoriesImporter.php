<?php

namespace App\Registry\EntityImporter;

use App\Entity\Category;
use App\Model\Importer\EntityImporterInterface;

class CategoriesImporter implements EntityImporterInterface
{
    public function getOrder(): int
    {
        return 0;
    }

    public function getTotal(): int
    {
        return 10000;
    }

    public function getName(): string
    {
        return 'categories';
    }

    public function getEntities(): iterable
    {
        $total = $this->getTotal();
        for ($i = 0; $i < $total; ++$i) {
            $category = new Category();
            $category->setName(sprintf('Category_%04d', random_int(1, $total)));
            yield $category;
        }
    }
}
