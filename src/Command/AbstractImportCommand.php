<?php

namespace App\Command;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractImportCommand extends Command
{

    protected $batchSize = 20;

    /** @var EntityManagerInterface */
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function truncateDb()
    {
        $rawSql = '
SET FOREIGN_KEY_CHECKS = 0;
truncate table tbl_category;
truncate table tbl_category_product;
truncate table tbl_manufacturer;
truncate table tbl_order;
truncate table tbl_product;
truncate table tbl_product_order;
truncate table tbl_user;
SET FOREIGN_KEY_CHECKS = 1;
        ';

        $statement = $this->em->getConnection()->prepare($rawSql);
        $statement->execute();
    }

}

