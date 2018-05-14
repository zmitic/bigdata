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
}
