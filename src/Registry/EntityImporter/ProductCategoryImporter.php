<?php

namespace App\Registry\EntityImporter;

use App\Entity\Category;
use App\Entity\ProductCategoryReference;
use App\Helper\Storage;
use App\Entity\Product;
use App\Model\Importer\EntityImporterInterface;
use Doctrine\ORM\EntityManagerInterface;

class ProductCategoryImporter implements EntityImporterInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getOrder(): int
    {
        return 5;
    }

    public function getTotal(): int
    {
        return 1000;
    }

    public function getName(): string
    {
        return 'products_categories';
    }

    public function getEntities(Storage $storage): iterable
    {
        $total = $this->getTotal();
        for ($i = 0; $i < $total; ++$i) {
            /** @var Product $product */
            $product = $this->em->getReference(Product::class, $storage->getOneByRandom('products'));
            /** @var Category $category */
            $category = $this->em->getReference(Category::class, $storage->getOneByRandom('categories'));
            $reference = new ProductCategoryReference($product, $category);
            yield $reference;
        }
    }
}
