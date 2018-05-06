<?php

namespace App\Registry\Admin;

use App\Model\AdminInterface;
use App\Repository\ProductRepository;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Pagination\PaginationInterface;

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
        return ['name'];
    }

    /** @return PaginationInterface|SlidingPagination */
    public function getPager(int $page): SlidingPagination
    {
        return $this->repository->paginate($page, null);
    }
}