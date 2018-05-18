<?php

namespace App\Service\EntitiesCounter;

use App\Entity\Counter;
use App\Repository\CounterRepository;

class EntitiesCounter
{
    /** @var CounterRepository */
    private $repository;

    /** @var Storage */
    private $storage;

    public function __construct(Storage $storage, CounterRepository $counterRepository)
    {
        $this->repository = $counterRepository;
        $this->storage = $storage;
    }

    public function countForClassName(string $className): ?int
    {
        if (!$id = $this->storage->findIdForClassName($className)) {
            return null;
        }

        /** @var Counter $counter */
        $counter = $this->repository->find($id);

        return $counter ? $counter->getCount() : 0;
    }
}
