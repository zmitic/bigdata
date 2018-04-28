<?php

namespace App\Command;

use App\Entity\OrderItem;
use App\Service\BulkPersister;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ExportCommand extends Command
{
    protected static $defaultName = 'app:export';

    /** @var BulkPersister */
    private $bulkPersister;

    /** @var EntityManager */
    private $em;

    public function __construct(BulkPersister $bulkPersister, EntityManagerInterface $em)
    {
        parent::__construct();
        $this->bulkPersister = $bulkPersister;
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Export data to CSV')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $qb = $this->em->getRepository(OrderItem::class)->createQueryBuilder('o')->orderBy('o.createdAt');
        $entities = $this->bulkPersister->stream($qb, function (QueryBuilder $qb, OrderItem $last) {
            $qb->andWhere('o.createdAt > :created_at')->setParameter('created_at', $last->getCreatedAt());
        });

        $ids = array_map(function (OrderItem $item) {
            return $item->getId();
        }, iterator_to_array($entities));
    }
}
