<?php

declare(strict_types=1);

namespace App\Registry\Admin;

use App\Model\AdminInterface;
use App\Model\BaseRepository;
use App\Service\Paginator\Pager;

abstract class AbstractAdmin implements AdminInterface
{
    /** @var BaseRepository */
    protected $repository;

    public function findOne(string $id): ?object
    {
        return $this->repository->find($id);
    }

    public function delete(object $entity): void
    {
        $this->repository->remove($entity, true);
    }

    public function persist(object $entity): void
    {
        $this->repository->persist($entity);
        $this->repository->flush();
    }

    public function getPager(int $page, array $filters): Pager
    {
        return $this->repository->paginate([$page], null, $this->repository->applyFilters($filters));
    }
}
