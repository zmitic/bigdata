<?php

declare(strict_types=1);

namespace App\Service\Paginator;

use Pagerfanta\Pagerfanta;

class Pager extends Pagerfanta
{
    private $total;

    public function getTotal(): int
    {
        return (int) $this->total;
    }

    public function setTotal(?int $total): void
    {
        $this->total = $total;
    }

    public function isBeyondRange(): bool
    {
        if (0 === $this->getNbResults()) {
            return false;
        }
        $count = $this->count();

        return $count >= 1000;
    }
}
