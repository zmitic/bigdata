<?php

namespace App\Registry\EntityImporter;

use App\Entity\Category;
use App\Helper\Storage;
use App\Model\Importer\EntityImporterInterface;

class CategoriesImporter implements EntityImporterInterface
{
    public function getOrder(): int
    {
        return 0;
    }

    public function getTotal(): int
    {
        return 1000;
    }

    public function getName(): string
    {
        return 'categories';
    }

    public function getEntities(Storage $storage): iterable
    {
        $total = $this->getTotal();
        for ($i = 0; $i < $total; ++$i) {
            $category = new Category();
            $category->setName(sprintf('Category_%07d', random_int(1, $total)));
            yield $category;
        }
    }
}
