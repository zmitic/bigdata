<?php

namespace App\Model;

use App\DependencyInjection\Compiler\RepositoriesPass;
use App\Service\Paginator\Pager;
use App\Service\Paginator\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Generator;

abstract class BaseRepository extends ServiceEntityRepository
{
    /** @var Paginator */
    protected $paginator;

    /** @see RepositoriesPass */
    public function setPaginator(Paginator $paginator): void
    {
        $this->paginator = $paginator;
    }

    protected function expr(): Expr
    {
        return $this->_em->getExpressionBuilder();
    }

    protected function make($expression, array $parameters, $condition = true)
    {
        if (!$condition) {
            return null;
        }

        return array_merge([$expression], $parameters);
    }

    public function paginate(array $config, ?iterable ...$generators): Pager
    {
        $page = array_shift($config) ?? 1;
        $limit = array_shift($config) ?? 10;

        $qb = $this->createQueryBuilder('o');
        $this->appendGeneratorsToQB($qb, ...$generators);

        return $this->paginator->paginate($qb, $page, $limit);
    }

    private function appendGeneratorsToQB(QueryBuilder $qb, ?iterable ...$generators): void
    {
        $operators = [];
        foreach ($generators as $generator) {
            if ($generator) {
                foreach ($generator as $expression) {
                    if ($expression) {
                        $operators[] = array_shift($expression);
                        foreach ($expression as $parameter => $value) {
                            $qb->setParameter($parameter, $value);
                        }
                    }
                }
            }
        }

        if ($operators) {
            $qb->andWhere(...$operators);
        }
    }

    public function getResults(?Generator ...$generators): array
    {
        $criteria = Criteria::create();
        $expressions = $this->convertGeneratorsToExpressions(...$generators);
        if ($expressions) {
            $criteria->andWhere(Criteria::expr()->andX(...$expressions));
        }

        return $this->matching($criteria)->toArray();
    }

    public function orX(?Generator ...$generators): ?Generator
    {
        if ($expressions = $this->convertGeneratorsToExpressions(...$generators)) {
            yield Criteria::expr()->orX(...$expressions);
        }
    }

    public function andX(?Generator ...$generators): ?Generator
    {
        if ($expressions = $this->convertGeneratorsToExpressions(...$generators)) {
            yield Criteria::expr()->andX(...$expressions);
        }
    }

    public function whereId(string $id): ?Generator
    {
        yield Criteria::expr()->eq('id', $id);
    }

    public function whereIds(array $ids): ?Generator
    {
        yield Criteria::expr()->in('id', $ids);
    }

    /** @return Expression[] */
    private function convertGeneratorsToExpressions(?iterable ...$generators): array
    {
        $expressions = [];
        foreach ($generators as $generator) {
            if ($generator) {
                foreach ($generator as $expression) {
                    if ($expression) {
                        $expressions[] = $expression;
                    }
                }
            }
        }

        return $expressions;
    }
}
