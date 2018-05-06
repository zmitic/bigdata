<?php

namespace App\Registry\Admin;

use App\Model\AdminInterface;
use App\Repository\CategoryRepository;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Pagination\PaginationInterface;

class CategoriesAdmin implements AdminInterface
{
    /** @var CategoryRepository */
    private $repository;

    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getName(): string
    {
        return 'categories';
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