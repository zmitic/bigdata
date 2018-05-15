<?php

namespace App\Repository;

use App\Entity\Category;
use App\Model\BaseRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CategoryRepository extends BaseRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function whereNameLike(?string $name): iterable
    {
        yield $this->make($this->expr()->like('o.name', ':name'), ['name' => $name.'%'], $name);
    }

    public function applyFilters($filters): ?iterable
    {
        yield from $this->whereMinNrOfProducts($filters['min_nr_of_products'] ?? null);
    }

    private function whereMinNrOfProducts($nrOfProducts): ?iterable
    {
        yield $this->make($this->expr()->gte('o.nrOfProducts', ':min_nr_of_products'), ['min_nr_of_products' => $nrOfProducts], $nrOfProducts);
    }
}
