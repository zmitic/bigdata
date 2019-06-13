<?php

declare(strict_types=1);

namespace App\Registry\EntityImporter;

use App\Entity\Manufacturer;
use App\Model\Importer\EntityImporterInterface;
use Faker\Factory;

class ManufacturersImporter implements EntityImporterInterface
{
    public const LIMIT = 10000;

    public function getOrder(): int
    {
        return 0;
    }

    public function getProgressBarTotal(): int
    {
        return self::LIMIT;
    }

    public function getName(): string
    {
        return 'manufacturers';
    }

    public function getEntities(): iterable
    {
        $faker = Factory::create();
        $total = $this->getProgressBarTotal();
        for ($i = 0; $i < $total; ++$i) {
            $manufacturer = new Manufacturer();
            $manufacturer->setName($faker->company);
            yield $manufacturer;
        }
    }
}
