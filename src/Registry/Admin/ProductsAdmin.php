<?php

namespace App\Registry\Admin;

use App\Model\AdminInterface;
use App\Repository\ProductRepository;
use App\Service\Paginator\Pager;

class ProductsAdmin implements AdminInterface
{
    /** @var ProductRepository */
    private $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getName(): string
    {
        return 'products';
    }

    public function getColumnsList(): array
    {
        return ['name', 'manufacturer'];
    }

    public function getPager(int $page): Pager
    {
        return $this->repository->paginate($page, null);
    }
}
