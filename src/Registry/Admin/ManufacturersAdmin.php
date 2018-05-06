<?php

namespace App\Registry\Admin;

use App\Model\AdminInterface;
use App\Repository\ManufacturerRepository;
use App\Service\Paginator\Pager;

class ManufacturersAdmin implements AdminInterface
{
    /** @var ManufacturerRepository */
    private $repository;

    public function __construct(ManufacturerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getName(): string
    {
        return 'manufacturers';
    }

    public function getColumnsList(): array
    {
        return ['name'];
    }

    public function getPager(int $page): Pager
    {
        return $this->repository->paginate($page, null);
    }
}