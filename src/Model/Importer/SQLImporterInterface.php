<?php

namespace App\Model\Importer;

interface SQLImporterInterface
{
    public function getTotal(): int;

    public function getKey(): string;

    public function getValues(): iterable;

    public function getEntityClass(): string;

    public function getColumnNames(): array;
}
