<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Manufacturer;
use App\Model\BaseRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ManufacturerRepository extends BaseRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Manufacturer::class);
    }
}
