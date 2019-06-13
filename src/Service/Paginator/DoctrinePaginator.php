<?php

declare(strict_types=1);

namespace App\Service\Paginator;

use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Tools\Pagination\CountOutputWalker;
use Doctrine\ORM\Tools\Pagination\CountWalker;
use Doctrine\ORM\Tools\Pagination\LimitSubqueryOutputWalker;
use Doctrine\ORM\Tools\Pagination\LimitSubqueryWalker;
use Doctrine\ORM\Tools\Pagination\WhereInWalker;
use function count;

class DoctrinePaginator implements \Countable, \IteratorAggregate
{
    /** @var Query */
    private $query;

    /** @var bool */
    private $fetchJoinCollection;

    /** @var bool|null */
    private $useOutputWalkers;

    /** @var int */
    private $count;

    public function __construct($query, $fetchJoinCollection = true)
    {
        if ($query instanceof QueryBuilder) {
            $query = $query->getQuery();
        }

        $this->query = $query;
        $this->fetchJoinCollection = (bool) $fetchJoinCollection;
    }

    public function getQuery(): Query
    {
        return $this->query;
    }

    public function getFetchJoinCollection(): bool
    {
        return $this->fetchJoinCollection;
    }

    public function getUseOutputWalkers(): ?bool
    {
        return $this->useOutputWalkers;
    }

    public function setUseOutputWalkers($useOutputWalkers): self
    {
        $this->useOutputWalkers = $useOutputWalkers;

        return $this;
    }

    public function count(): int
    {
        if (null === $this->count) {
            try {
                $this->count = array_sum(array_map('current', $this->getCountQuery()->getScalarResult()));
            } catch (\Exception $e) {
                $this->count = 0;
            }
        }

        return $this->count;
    }

    public function getIterator()
    {
        $offset = $this->query->getFirstResult();
        $length = $this->query->getMaxResults();

        if ($this->fetchJoinCollection) {
            $subQuery = $this->cloneQuery($this->query);

            if ($this->useOutputWalker($subQuery)) {
                $subQuery->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, LimitSubqueryOutputWalker::class);
            } else {
                $this->appendTreeWalker($subQuery, LimitSubqueryWalker::class);
            }

            $subQuery->setFirstResult($offset)->setMaxResults($length);

            $ids = array_map('current', $subQuery->getScalarResult());

            $whereInQuery = $this->cloneQuery($this->query);
            // don't do this for an empty id array
            if (0 === count($ids)) {
                return new \ArrayIterator([]);
            }

            $this->appendTreeWalker($whereInQuery, WhereInWalker::class);
            $whereInQuery->setHint(WhereInWalker::HINT_PAGINATOR_ID_COUNT, count($ids));
            $whereInQuery->setFirstResult(null)->setMaxResults(null);
            $whereInQuery->setParameter(WhereInWalker::PAGINATOR_ID_ALIAS, $ids);
            $whereInQuery->setCacheable($this->query->isCacheable());

            $result = $whereInQuery->getResult($this->query->getHydrationMode());
        } else {
            $result = $this->cloneQuery($this->query)
                ->setMaxResults($length)
                ->setFirstResult($offset)
                ->setCacheable($this->query->isCacheable())
                ->getResult($this->query->getHydrationMode())
            ;
        }

        return new \ArrayIterator($result);
    }

    private function cloneQuery(Query $query): Query
    {
        /* @var $cloneQuery Query */
        $cloneQuery = clone $query;

        $cloneQuery->setParameters(clone $query->getParameters());
        $cloneQuery->setCacheable(false);

        foreach ($query->getHints() as $name => $value) {
            $cloneQuery->setHint($name, $value);
        }

        return $cloneQuery;
    }

    private function useOutputWalker(Query $query): bool
    {
        if (null === $this->useOutputWalkers) {
            return false === (bool) $query->getHint(Query::HINT_CUSTOM_OUTPUT_WALKER);
        }

        return $this->useOutputWalkers;
    }

    private function appendTreeWalker(Query $query, $walkerClass): void
    {
        $hints = $query->getHint(Query::HINT_CUSTOM_TREE_WALKERS);

        if (false === $hints) {
            $hints = [];
        }

        $hints[] = $walkerClass;
        $query->setHint(Query::HINT_CUSTOM_TREE_WALKERS, $hints);
    }

    private function getCountQuery(): Query
    {
        /* @var $countQuery Query */
        $countQuery = $this->cloneQuery($this->query);

        if (!$countQuery->hasHint(CountWalker::HINT_DISTINCT)) {
            $countQuery->setHint(CountWalker::HINT_DISTINCT, true);
        }

        if ($this->useOutputWalker($countQuery)) {
            $platform = $countQuery->getEntityManager()->getConnection()->getDatabasePlatform(); // law of demeter win

            $rsm = new ResultSetMapping();
            $rsm->addScalarResult($platform->getSQLResultCasing('dctrn_count'), 'count');

            $countQuery->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, CountOutputWalker::class);
            $countQuery->setResultSetMapping($rsm);
        } else {
            $this->appendTreeWalker($countQuery, CountWalker::class);
        }

        $countQuery->setFirstResult(null)->setMaxResults(1000);

        $parser = new Parser($countQuery);
        $parameterMappings = $parser->parse()->getParameterMappings();
        /* @var $parameters \Doctrine\Common\Collections\Collection|\Doctrine\ORM\Query\Parameter[] */
        $parameters = $countQuery->getParameters();

        foreach ($parameters as $key => $parameter) {
            $parameterName = $parameter->getName();

            if (!(isset($parameterMappings[$parameterName]) || array_key_exists($parameterName, $parameterMappings))) {
                unset($parameters[$key]);
            }
        }

        $countQuery->setParameters($parameters);

        return $countQuery;
    }
}
