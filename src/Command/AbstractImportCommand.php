<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractImportCommand extends Command
{
    protected $batchSize = 2000;

    /** @var EntityManagerInterface */
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function truncateDb(): void
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

    protected function createProgressBar(SymfonyStyle $io, string $key, int $limit): ProgressBar
    {
        $progressBar = $io->createProgressBar($limit);
        $progressBar->setBarCharacter('<fg=green>⚬</>');
        $progressBar->setEmptyBarCharacter('<fg=red>⚬</>');
        $progressBar->setProgressCharacter('<fg=green>➤</>');

        $formats = [
            "<fg=white;bg=cyan> Importing $key </>",
            '',
            '[%bar%]%current%/%max% ',
            '',
            '%speed: -21s% ETA: %estimated% %memory:21s%',
        ];

        $progressBar->setFormat(implode("\n", $formats));

        return $progressBar;
    }
}

