<?php

namespace App\Command;

use App\Helper\Storage;
use App\Service\BulkPersister;
use App\Service\SQLImporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;

abstract class AbstractImportCommand extends Command
{
    protected $batchSize = 5000;

    /** @var EntityManagerInterface */
    protected $em;

    protected $storage;

    /** @var BulkPersister */
    protected $bulkPersister;

    /** @var SQLImporter */
    protected $sqlImporter;

    public function __construct(EntityManagerInterface $em, BulkPersister $bulkPersister, SQLImporter $importer)
    {
        $this->em = $em;
        $this->storage = new Storage();
        $this->bulkPersister = $bulkPersister;
        $this->sqlImporter = $importer;
        parent::__construct();
    }

    protected function warmup(): void
    {
        $this->executeSql('SET FOREIGN_KEY_CHECKS = 0;SET unique_checks=0;SET autocommit=0;');
    }

    protected function cleanup(): void
    {
        $this->executeSql('SET FOREIGN_KEY_CHECKS = 1;SET unique_checks=1;SET autocommit=1;');
    }

    protected function truncateDb(): void
    {
        $rawSql = '
SET FOREIGN_KEY_CHECKS = 0;
truncate table tbl_category;
truncate table tbl_category_product;
truncate table tbl_manufacturer;
truncate table tbl_order;
truncate table tbl_order_item;
truncate table tbl_product;
truncate table tbl_user;
SET FOREIGN_KEY_CHECKS = 1;
        ';
        $this->executeSql($rawSql);
    }

    protected function executeSql(string $sql): void
    {
        $statement = $this->em->getConnection()->prepare($sql);
        $statement->execute();
    }
}
