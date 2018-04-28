<?php

namespace App\Command;

use App\Helper\Storage;
use App\Service\BulkPersister;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;

abstract class AbstractImportCommand extends Command
{
    protected $batchSize = 6000;

    /** @var EntityManagerInterface */
    protected $em;

    protected $storage;

    /** @var BulkPersister */
    protected $bulkPersister;

    public function __construct(EntityManagerInterface $em, BulkPersister $bulkPersister)
    {
        $this->em = $em;
        $this->storage = new Storage();
        parent::__construct();
        $this->bulkPersister = $bulkPersister;
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

        $statement = $this->em->getConnection()->prepare($rawSql);
        $statement->execute();
    }
}

