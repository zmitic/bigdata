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

    /** @return PaginationInterface|SlidingPagination */
    public function paginate($page, $limit, ?Expression ...$expressions): PaginationInterface
    {
        $page = $page ?: 1;
        $limit = $limit ?: 10;
        $criteria = Criteria::create();
        $expressions = $this->cleanExpressions(...$expressions);
        if ($expressions) {
            $criteria->andWhere(Criteria::expr()->andX(...$expressions));
        }
        $qb = $this->createQueryBuilder('o')->addCriteria($criteria)->getQuery()->setMaxResults(100);

        return $this->paginator->paginate($qb, $page, $limit);
    }

    public function getResults(?Expression ...$expressions): array
    {
        $criteria = Criteria::create();
        $expressions = $this->cleanExpressions(...$expressions);
        if ($expressions) {
            $criteria->andWhere(Criteria::expr()->andX(...$expressions));
        }

        return $this->matching($criteria)->toArray();
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

    public function whereId(string $id): ?Expression
    {
        return Criteria::expr()->eq('id', $id);
    }

    public function whereIds(array $ids): ?Expression
    {
        return Criteria::expr()->in('id', $ids);
    }

    private function cleanExpressions(?Expression ...$expressions): array
    {
        return array_filter($expressions, function (?Expression $expression) {
            return (bool) $expression;
        });
    }
}
