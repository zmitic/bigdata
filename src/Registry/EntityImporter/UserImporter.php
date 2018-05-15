<?php

namespace App\Registry\EntityImporter;

use App\Entity\User;
use App\Model\Importer\EntityImporterInterface;

class UserImporter implements EntityImporterInterface
{
    public const LIMIT = 1000;

    public function getOrder(): int
    {
        return 5;
    }

    public function getProgressBarTotal(): int
    {
        return self::LIMIT;
    }

    public function getName(): string
    {
        return 'users';
    }

    public function getEntities(): iterable
    {
        $total = $this->getProgressBarTotal();
        for ($i = 0; $i < $total; ++$i) {
            $user = new User();
            $user->setUsername(sprintf('user_%04d', random_int(1, $total)));
            yield $user;
        }
    }
}
