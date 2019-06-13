<?php

declare(strict_types=1);

namespace App\Registry\EntityImporter;

use App\Entity\Order;
use App\Entity\User;
use App\Model\Importer\EntityImporterInterface;
use Doctrine\ORM\EntityManagerInterface;

class OrderImporter implements EntityImporterInterface
{
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
        return ProductsImporter::LIMIT;
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
            yield $order;
        }
    }
}
