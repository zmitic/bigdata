<?php

declare(strict_types=1);

namespace App\Model\Importer;

interface EntityImporterInterface
{
    public const TAG = 'app.entity_importer';

    public function getOrder(): int;

    public function getProgressBarTotal(): int;

    public function getName(): string;

    public function getEntities(): iterable;
}
