<?php

namespace App\Service\Paginator;

use Pagerfanta\Pagerfanta;

class Pager extends Pagerfanta
{
    private $total;

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(?int $total): void
    {
        $this->total = $total;
    }

    public function isBeyondRange(): bool
    {
        if (null === $this->total) {
            return false;
        }
        $count = $this->count();

        return $this->total > $count;
    }
}
