<?php

namespace App\Repository;

use App\Entity\User;
use App\Model\BaseRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserRepository extends BaseRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function applyFilters(array $filters): ?iterable
    {
        yield from $this->whereMinSpent($filters['min_spent'] ?? null);
        yield from $this->whereMaxSpent($filters['max_spent'] ?? null);
    }

    private function whereMinSpent(?float $spent): iterable
    {
        yield $this->make($this->expr()->gte('o.spent', ':min_spent'), ['min_spent' => $spent], $spent);
    }

    private function whereMaxSpent(?float $spent): iterable
    {
        yield $this->make($this->expr()->lte('o.spent', ':max_spent'), ['max_spent' => $spent], $spent);
    }
}
