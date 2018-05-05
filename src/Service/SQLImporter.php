<?php

namespace App\Service;

use App\Model\Importer\SQLImporterInterface;

class SQLImporter
{
    /** @var SQLImporterInterface[] */
    private $sqlImporters;

    public function __construct(iterable $importers)
    {
        $this->sqlImporters = $importers;
    }
}
