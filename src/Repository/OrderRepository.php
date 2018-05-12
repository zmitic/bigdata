<?php

namespace App\Repository;

use App\Entity\Order;
use App\Model\BaseRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class OrderRepository extends BaseRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Order::class);
    }
}
