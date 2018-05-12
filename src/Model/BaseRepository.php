<?php

namespace App\Model;

use App\DependencyInjection\Compiler\RepositoriesPass;
use App\Service\Paginator\Pager;
use App\Service\Paginator\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\ORM\Query\Expr;
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

    public function paginate(?int $page, ?int $limit, ...$generators): Pager
    {
        $page = $page ?? 1;
        $limit = $limit ?? 10;

        $qb = $this->createQueryBuilder('o');
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

        return $this->paginator->paginate($qb, $page, $limit);
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
    private function convertGeneratorsToExpressions(?Generator ...$generators): array
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
