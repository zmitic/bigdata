<?php

namespace App\Registry\EntityImporter;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use App\Model\Importer\EntityImporterInterface;
use Doctrine\ORM\EntityManagerInterface;

class OrderImporter implements EntityImporterInterface
{
    public const LIMIT = 1000;

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getOrder(): int
    {
        return 6;
    }

    public function getProgressBarTotal(): int
    {
        return self::LIMIT;
    }

    public function getName(): string
    {
        return 'orders';
    }

    public function getEntities(): iterable
    {
        $total = $this->getProgressBarTotal();
        for ($i = 0; $i < $total; ++$i) {
            /** @var User $user */
            $user = $this->em->getReference(User::class, random_int(1, UserImporter::LIMIT));
            $order = new Order($user);

            $random = random_int(1, 6);
            for ($j = 0; $j <= $random; ++$j) {
                /** @var Product $product */
                $product = $this->em->getReference(Product::class, random_int(1, ProductsImporter::LIMIT));
                $order->addProduct($product, random_int(1, 5));
            }
            yield $order;
        }
    }
}
