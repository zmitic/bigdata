<?php

namespace App\Model;

use App\Service\FiltersHandler;
use App\Service\Paginator\Pager;
use Symfony\Component\Form\FormBuilderInterface;

interface AdminInterface
{
    public const TAG = 'app.admin';

    public function getName(): string;

    public function getColumnsList(): array;

    public function getPager(int $page, array $filters): Pager;

    public function getFilterForm(FiltersHandler $filtersHandler): FilterFormModel;

    public function findOne(string $id): ?object;

    public function deleteOne(object $entity): void;

    public function updateOne(object $entity): void;

    public function setFormBuilder(FormBuilderInterface $formBuilder): void;
}
