<?php

declare(strict_types=1);

namespace App\Service\Paginator;

use App\Service\EntitiesCounter\EntitiesCounter;
use Doctrine\ORM\QueryBuilder;

class Paginator
{
    /** @var EntitiesCounter */
    private $counter;

    public function __construct(EntitiesCounter $counter)
    {
        $this->counter = $counter;
    }

    public function paginate(QueryBuilder $qb, $page, $limit): Pager
    {
        $adapter = new LimitedDoctrineORMAdapter($qb);

        $className = $qb->getRootEntities()[0];
        $total = $this->counter->countForClassName($className);

        $pager = new Pager($adapter);
        $pager->setCurrentPage($page);
        $pager->setMaxPerPage($limit);
        $pager->setTotal($total);

        return $pager;
    }
}
