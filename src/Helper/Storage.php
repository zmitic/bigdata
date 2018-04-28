<?php

namespace App\Helper;

class Storage
{
    /** @var Store[] */
    private $storage = [];

    public function store(string $key, string $id): void
    {
        $store = $this->storage[$key];
        $store->put($id);
    }

    public function getAll(string $key): array
    {
        return $this->getStore($key)->getAll();
    }

    public function getRandom(string $key): string
    {
        return $this->getStore($key)->getRandom();
    }

    public function create(string $key, int $limit): void
    {
        $this->storage[$key] = new Store($limit);
    }

    private function getStore(string $key): Store
    {
        return $this->storage[$key];
    }
}
