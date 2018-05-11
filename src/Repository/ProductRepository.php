<?php

namespace App\Repository;

use App\Entity\Manufacturer;
use App\Entity\Product;
use App\Model\BaseRepository;
use Doctrine\Common\Collections\Criteria;
use Generator;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ProductRepository extends BaseRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function whereName(string $name): ?Generator
    {
        yield Criteria::expr()->eq('name', $name);
    }

    public function applyFilters(array $filters)
    {
        yield from $this->whereMinPrice($filters['min_price']);
        yield from $this->whereMaxPrice($filters['max_price']);
        yield from $this->whereManufacturer($filters['manufacturer']);
    }

    private function whereMinPrice($price)
    {
        yield Criteria::expr()->gt('basePrice', (float)$price);
    }

    private function whereMaxPrice($price)
    {
        if ($price) {
            yield Criteria::expr()->lt('basePrice', (float)$price);
        }
    }

    private function whereManufacturer(?Manufacturer $manufacturer)
    {
        if ($manufacturer) {
            yield Criteria::expr()->eq('manufacturer', $manufacturer);
        }
    }

    public function optimizeJoinsOn(array $products): void
    {
        $this->_em->getRepository(Product::class)->createQueryBuilder('o')
            ->select('PARTIAL o.{id}')
            ->leftJoin('o.categoryReferences', 'product_references')->addSelect('product_references')
                ->leftJoin('product_references.category', 'category')->addSelect('category')
            ->where('o IN (:products)')->setParameter('products', $products)
            ->getQuery()->getResult();
    }
}
