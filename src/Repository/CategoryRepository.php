<?php

namespace App\Repository;

use App\Entity\Category;
use App\Model\BaseRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Expression;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CategoryRepository extends BaseRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function whereName(string $name): ?Expression
    {
        return Criteria::expr()->eq('name', $name);
    }
}
