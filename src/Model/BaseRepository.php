<?php

namespace App\Model;

use App\DependencyInjection\Compiler\RepositoriesPass;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Common\Collections\Expr\Expression;
use Knp\Component\Pager\Pagination\PaginationInterface;

abstract class BaseRepository extends ServiceEntityRepository
{
    /** @var PaginatorInterface */
    protected $paginator;

    /**
     * @see RepositoriesPass
     */
    public function setPaginator(PaginatorInterface $paginator): void
    {
        $this->paginator = $paginator;
    }

    /**
     * @return PaginationInterface|SlidingPagination
     */
    public function paginate($page, $limit, ?Expression ...$expressions): PaginationInterface
    {
        $page = $page ?: 1;
        $limit = $limit ?: 10;
        $criteria = Criteria::create();
        $expressions = $this->cleanExpressions(...$expressions);
        $criteria->andWhere(Criteria::expr()->andX(...$expressions));
        $qb = $this->createQueryBuilder('o')->addCriteria($criteria);

        return $this->paginator->paginate($qb, $page, $limit);
    }

    public function orX(?Expression ...$expressions): ?Expression
    {
        $clean = $this->cleanExpressions(...$expressions);

        return Criteria::expr()->orX(...$clean);
    }

    public function andX(?Expression ...$expressions): ?Expression
    {
        $clean = $this->cleanExpressions(...$expressions);

        return Criteria::expr()->andX(...$clean);
    }

    private function cleanExpressions(?Expression ...$expressions): array
    {
        return array_filter($expressions, function (?Expression $expression) {
            return (bool) $expression;
        });
    }
}
