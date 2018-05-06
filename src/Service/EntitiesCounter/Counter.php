<?php

namespace App\Service\EntitiesCounter;

use App\Repository\CounterRepository;

class Counter
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

    public function countForClassName(string $className): int
    {
        if (!$id = $this->storage->findIdForClassName($className)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" is not managed by counter. Use @Counted annotation and warmup the cache.', $className));
        }

        /** @var \App\Entity\Counter $counter */
        $counter = $this->repository->find($id);

        return $counter->getCount();
    }
}
