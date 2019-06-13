<?php

declare(strict_types=1);

namespace App\Model;

use App\Service\FiltersHandler;
use App\Service\Paginator\Pager;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

interface AdminInterface
{
    public const TAG = 'app.admin';

    public function getName(): string;

    public function getColumnsList(): array;

    public function getPager(int $page, array $filters): Pager;

    public function getFilterForm(FiltersHandler $filtersHandler): FilterFormModel;

    public function findOne(string $id): ?object;

    public function delete(object $entity): void;

    public function persist(object $entity): void;

    public function create(Request $request): ?object;

    public function setFormBuilder(FormBuilderInterface $formBuilder): void;
}
