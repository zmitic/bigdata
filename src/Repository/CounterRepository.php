<?php

namespace App\Repository;

use App\Entity\Counter;
use App\Model\BaseRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CounterRepository extends BaseRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Counter::class);
    }
}
