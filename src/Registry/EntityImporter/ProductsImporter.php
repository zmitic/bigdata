<?php

namespace App\Registry\EntityImporter;

use App\Entity\Manufacturer;
use App\Entity\Product;
use App\Model\Importer\EntityImporterInterface;
use Doctrine\ORM\EntityManagerInterface;

class ProductsImporter implements EntityImporterInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getOrder(): int
    {
        return 3;
    }

    public function getTotal(): int
    {
        return 10000000;
    }

    public function getName(): string
    {
        return 'products';
    }

    public function getEntities(): iterable
    {
        $total = $this->getTotal();
        for ($i = 0; $i < $total; ++$i) {
            /** @var Manufacturer $manufacturer */
            $manufacturer = $this->em->getReference(Manufacturer::class, random_int(1, 10000));
            $product = new Product();
            $product->setBasePrice((float) random_int(1, 10000));
            $product->setManufacturer($manufacturer);
            $product->setName(sprintf('Product_%07d', random_int(1, $total)));
            yield $product;
        }
    }
}
