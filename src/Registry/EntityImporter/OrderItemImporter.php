<?php

namespace App\Registry\EntityImporter;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Model\Importer\EntityImporterInterface;
use Doctrine\ORM\EntityManagerInterface;

class OrderItemImporter implements EntityImporterInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getOrder(): int
    {
        return 7;
    }

    public function getProgressBarTotal(): int
    {
        return ProductsImporter::LIMIT;
    }

    public function getName(): string
    {
        return 'order_item';
    }

    public function getEntities(): iterable
    {
        $total = $this->getProgressBarTotal();
        for ($i = 0; $i < $total; ++$i) {
            /** @var Order $order */
            $order = $this->em->getReference(Order::class, random_int(1, ProductsImporter::LIMIT));

            /** @var Product $product */
            $product = $this->em->getReference(Product::class, random_int(1, ProductsImporter::LIMIT));
            $orderItem = new OrderItem($order, $product, random_int(1, 10));

            yield $orderItem;
        }
    }
}
