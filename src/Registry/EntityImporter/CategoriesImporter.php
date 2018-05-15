<?php

namespace App\Registry\EntityImporter;

use App\Entity\Category;
use App\Model\Importer\EntityImporterInterface;

class CategoriesImporter implements EntityImporterInterface
{
    public const LIMIT = 10000;

    public function getOrder(): int
    {
        return 0;
    }

    public function getProgressBarTotal(): int
    {
        return self::LIMIT;
    }

    public function getName(): string
    {
        return 'categories';
    }

    public function getEntities(): iterable
    {
        $total = $this->getProgressBarTotal();
        for ($i = 0; $i < $total; ++$i) {
            $category = new Category();
            $category->setName(sprintf('Category_%04d', random_int(1, $total)));
            yield $category;
        }
    }
}
