<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Product;
use App\Helper\BulkExporter;
use App\Helper\StopwatchProgressBar;
use App\Service\EntitiesCounter\EntitiesCounter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ExportCommand extends Command
{
    protected static $defaultName = 'app:export';

    /** @var EntityManagerInterface */
    private $em;
    /**
     * @var EntitiesCounter
     */
    private $entitiesCounter;

    public function __construct(EntityManagerInterface $em, EntitiesCounter $entitiesCounter)
    {
        parent::__construct();
        $this->em = $em;
        $this->entitiesCounter = $entitiesCounter;
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
        $io->write(sprintf("\033\143"));

        foreach ($this->getEntityClassNames() as $className) {
            $count = $this->entitiesCounter->countForClassName($className);
            $stopWatch = new StopwatchProgressBar($io, $className, $count);
            /** @var EntityRepository $repository */
            $repository = $this->em->getRepository($className);
            $qb = $repository->createQueryBuilder('o')->orderBy('o.id');

            $exporter = new BulkExporter($qb);
            /** @var Product[]|Generator $results */
            $results = $exporter->export(function (QueryBuilder $qb, Product $last) {
                $qb->getEntityManager()->clear();

                $qb->andWhere('o.id > :last_id')->setParameter('last_id', $last->getId());
            });

            foreach ($results as $i => $product) {
                $stopWatch->setProgress($i);
            }
        }
    }

    private function getEntityClassNames(): iterable
    {
        return [
            Product::class,
        ];
    }
}
