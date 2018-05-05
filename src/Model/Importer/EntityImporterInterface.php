<?php

namespace App\Model\Importer;

use App\Helper\Storage;

interface EntityImporterInterface
{
    public function getOrder(): int;

    public function getTotal(): int;

    public function getName(): string;

    public function getEntities(Storage $storage): iterable;
}
