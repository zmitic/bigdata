<?php

namespace App\Model;

use App\Service\Paginator\Pager;

interface AdminInterface
{
    public const TAG = 'app.admin';

    public function getName(): string;

    public function getColumnsList(): array;

    public function getPager(int $page): Pager;
}
