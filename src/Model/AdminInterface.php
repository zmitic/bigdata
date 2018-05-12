<?php

namespace App\Model;

use App\Service\FiltersHandler;
use App\Service\Paginator\Pager;

interface AdminInterface
{
    public const TAG = 'app.admin';

    public function getName(): string;

    public function getColumnsList(): array;

    public function getPager(int $page, array $filters): Pager;

    public function getFilterForm(FiltersHandler $filtersHandler): FilterFormModel;
}
