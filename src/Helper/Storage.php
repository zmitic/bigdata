<?php

namespace App\Helper;

class Storage
{
    /** @var Store[] */
    private $storage = [];

    public function store(string $key, $ids): void
    {
        $store = $this->getStore($key);
        foreach ((array) $ids as $id) {
            $store->put($id);
        }
    }

    public function getAll(string $key): array
    {
        return $this->getStore($key)->getAll();
    }

    public function getOneByRandom(string $key): string
    {
        return $this->getStore($key)->getOneByRandom();
    }

    public function getRandom(string $key, int $limit): array
    {
        return $this->getStore($key)->getRandom($limit);
    }

    public function create(string $key, int $limit): void
    {
        if (isset($this->storage[$key])) {
            throw new \InvalidArgumentException(sprintf('Storage "%s" already created.', $key));
        }
        $this->storage[$key] = new Store($limit);
    }

    private function getStore(string $key): Store
    {
        if (!isset($this->storage[$key])) {
            throw new \InvalidArgumentException(sprintf('Storage "%s" not found. Did you forget to create it?', $key));
        }

        return $this->storage[$key];
    }
}
