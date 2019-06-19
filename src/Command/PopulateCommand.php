<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\EntitiesImporter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PopulateCommand extends Command
{
    protected static $defaultName = 'app:populate';

    /** @var EntityManagerInterface */
    private $em;

    /** @var EntitiesImporter */
    private $sqlImporter;

    public function __construct(EntityManagerInterface $em, EntitiesImporter $sqlImporter)
    {
        parent::__construct();
        $this->em = $em;
        $this->sqlImporter = $sqlImporter;
    }

    public function __destruct()
    {
        $this->em->getConnection()->exec('SET autocommit=1;SET unique_checks=1;SET foreign_key_checks=1;');
    }

    protected function configure(): void
    {
        $this->setDescription('Import small amount of data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->write(sprintf("\033\143"));
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $io->caution('Truncating all tables...');
        $io->write(sprintf("\033\143"));
        $this->truncateAllTables();
        $this->sqlImporter->import($io);
    }

    private function truncateAllTables(): void
    {
        $tables = [];
        /** @var ClassMetadata[] $allMetadata */
        $allMetadata = $this->em->getMetadataFactory()->getAllMetadata();
        foreach ($allMetadata as $metadata) {
            $tables[] = $metadata->table['name'];
        }

        $sql = 'SET FOREIGN_KEY_CHECKS = 0;
        SET unique_checks=0;
SET unique_checks=0;';
        foreach ($tables as $table) {
            $sql .= sprintf('TRUNCATE TABLE %s;', $table);
        }
        $sql .= 'SET FOREIGN_KEY_CHECKS = 1;';

        $this->em->getConnection()->exec($sql);
    }
}
