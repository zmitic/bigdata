<?php

namespace App\Service\Paginator;

use Pagerfanta\Adapter\AdapterInterface;

class LimitedDoctrineORMAdapter implements AdapterInterface
{
    private $paginator;

    public function __construct($query, $fetchJoinCollection = true, $useOutputWalkers = null)
    {
        $this->paginator = new DoctrinePaginator($query, $fetchJoinCollection);
        $this->paginator->setUseOutputWalkers($useOutputWalkers);
    }

    public function getQuery()
    {
        return $this->paginator->getQuery();
    }

    public function getFetchJoinCollection(): bool
    {
        return $this->paginator->getFetchJoinCollection();
    }

    public function getNbResults(): int
    {
        return \count($this->paginator);
    }

    public function getSlice($offset, $length)
    {
        $this->paginator
            ->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($length);

        return $this->paginator->getIterator();
    }
}
