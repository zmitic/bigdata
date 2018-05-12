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

    public function applyFilters(array $filters): ?Generator
    {
        yield from $this->whereManufacturer($filters['manufacturer'] ?? null);
        yield from $this->whereMinPrice($filters['min_price'] ?? null);
        yield from $this->whereMaxPrice($filters['max_price'] ?? null);
    }

    private function whereMinPrice(?float $minPrice): ?Generator
    {
        yield $minPrice ? [$this->expr()->gte('o.basePrice', ':min'), 'min' => $minPrice] : null;
    }

    private function whereMaxPrice(?float $maxPrice): ?Generator
    {
        yield $maxPrice ? [$this->expr()->lte('o.basePrice', ':max'), 'max' => $maxPrice] : null;
    }

    private function whereManufacturer(?Manufacturer $manufacturer): ?Generator
    {
        yield $manufacturer ? [$this->expr()->eq('o.manufacturer', ':man'), 'man' => $manufacturer] : null;
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
