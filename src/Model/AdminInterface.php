<?php

namespace App\Model;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Pagination\PaginationInterface;

interface AdminInterface
{
    public const TAG = 'app.admin';

    public function getName(): string;

    public function getColumnsList(): array;

    /** @return PaginationInterface|SlidingPagination */
    public function getPager(int $page): SlidingPagination;
}
