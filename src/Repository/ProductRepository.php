<?php

namespace App\Repository;

use App\Entity\Manufacturer;
use App\Entity\Product;
use App\Model\BaseRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ProductRepository extends BaseRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function applyFilters(array $filters): ?iterable
    {
        yield from $this->whereManufacturer($filters['manufacturer'] ?? null);
        yield from $this->whereMinPrice($filters['min_price'] ?? null);
        yield from $this->whereMaxPrice($filters['max_price'] ?? null);
    }

    private function whereMinPrice(?float $minPrice): ?iterable
    {
        return [
            $this->make($this->expr()->gte('o.basePrice', ':min'), ['min' => $minPrice], $minPrice),
        ];
    }

    private function whereMaxPrice(?float $maxPrice): ?iterable
    {
        yield $this->make($this->expr()->lte('o.basePrice', ':max'), ['max' => $maxPrice], $maxPrice);
    }

    private function whereManufacturer(?Manufacturer $manufacturer): ?iterable
    {
        if ($manufacturer) {
            yield [$this->expr()->eq('o.manufacturer', ':man'), 'man' => $manufacturer];
        }
    }

    public function optimizeJoinsOn(array $products): void
    {
        $this->createQueryBuilder('o')
            ->select('PARTIAL o.{id}')
            ->leftJoin('o.categoryReferences', 'product_references')->addSelect('product_references')
            ->leftJoin('product_references.category', 'category')->addSelect('category')
            ->where('o IN (:products)')->setParameter('products', $products)
            ->getQuery()->getResult();
    }
}
