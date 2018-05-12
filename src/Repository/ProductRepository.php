<?php

namespace App\Repository;

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
        yield !empty($filters['min_price']) ? Criteria::expr()->gte('basePrice', (float)$filters['min_price']) : null;
        yield !empty($filters['max_price']) ? Criteria::expr()->lte('basePrice', (float)$filters['max_price']) : null;
        yield !empty($filters['manufacturer']) ? Criteria::expr()->eq('manufacturer', $filters['manufacturer']) : null;
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
