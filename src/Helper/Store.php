<?php

namespace App\Helper;

use Ramsey\Uuid\Uuid;

class Store
{
    private $data = [];

    private $limit;

    private $size = 0;

    public function __construct(int $limit)
    {
        $this->limit = $limit;
    }

    public function put(string $id): void
    {
        if ($this->isFull()) {
            return;
        }
        $this->data[] = $id;
        $this->size++;
    }

    /** @return Uuid[] */
    public function getAll(): array
    {
        return $this->data;
    }

    private function isFull(): bool
    {
        return $this->size > $this->limit;
    }

    public function getRandom(): string
    {
        $randomKey = random_int(0, $this->size - 1);

        return $this->data[$randomKey];
    }
}
