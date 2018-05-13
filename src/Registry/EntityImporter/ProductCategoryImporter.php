<?php

namespace App\Registry\EntityImporter;

use App\Entity\Category;
use App\Entity\ProductCategoryReference;
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

    public function getEntities(): iterable
    {
        $total = $this->getTotal();
        for ($i = 0; $i < $total; ++$i) {
            /** @var Product $product */
            $product = $this->em->getReference(Product::class, random_int(1, 10000000));
            /** @var Category $category */
            $category = $this->em->getReference(Category::class, random_int(1, 10000));
            $reference = new ProductCategoryReference($product, $category);
            yield $reference;
        }
    }
}
