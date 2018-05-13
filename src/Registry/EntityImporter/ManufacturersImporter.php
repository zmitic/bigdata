<?php

namespace App\Registry\EntityImporter;

use App\Entity\Manufacturer;
use App\Model\Importer\EntityImporterInterface;
use Faker\Factory;

class ManufacturersImporter implements EntityImporterInterface
{
    public function getOrder(): int
    {
        return 0;
    }

    public function getTotal(): int
    {
        return 10000;
    }

    public function getName(): string
    {
        return 'manufacturers';
    }

    public function getEntities(): iterable
    {
        $faker = Factory::create();
        $total = $this->getTotal();
        for ($i = 0; $i < $total; ++$i) {
            $manufacturer = new Manufacturer();
            $manufacturer->setName($faker->company);
            yield $manufacturer;
        }
    }
}
