<?php

namespace App\Model\Importer;

interface EntityImporterInterface
{
    public function getTotal(): int;

    public function getKey(): string;

    public function getEntities(): iterable;
}
