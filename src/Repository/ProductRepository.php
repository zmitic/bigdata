<?php

namespace App\Repository;

use App\Entity\Product;
use App\Model\BaseRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ProductRepository extends BaseRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function whereName(string $name): ?\Generator
    {
        yield Criteria::expr()->eq('name', $name);
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
