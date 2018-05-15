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

    protected function make($expression, array $parameters = [], $condition = true)
    {
        if (!$condition) {
            return null;
        }

        return array_merge([$expression], $parameters);
    }

    public function paginateQb(QueryBuilder $qb, array $config, ?iterable ...$generators): Pager
    {
        $page = array_shift($config) ?? 1;
        $limit = array_shift($config) ?? 10;

        $this->appendGeneratorsToQB($qb, ...$generators);

        return $this->paginator->paginate($qb, $page, $limit);
    }

    public function paginate(array $config, ?iterable ...$generators): Pager
    {
        $page = array_shift($config) ?? 1;
        $limit = array_shift($config) ?? 10;

        $qb = $this->createQueryBuilder('o');
        $this->appendGeneratorsToQB($qb, ...$generators);

        return $this->paginator->paginate($qb, $page, $limit);
    }

    public function getResults(?iterable ...$generators): array
    {
        $qb = $this->createQueryBuilder('o');
        $this->appendGeneratorsToQB($qb, ...$generators);

        return $qb->getQuery()->getResult();
    }

    public function setMaxResults(int $maxResults)
    {
        yield Criteria::create()->setMaxResults($maxResults);
    }

    private function appendGeneratorsToQB(QueryBuilder $qb, ?iterable ...$generators): void
    {
        $operators = [];
        foreach ($generators as $generator) {
            if ($generator) {
                foreach ($generator as $expression) {
                    if ($expression) {
                        if ($expression instanceof Criteria) {
                            $qb->addCriteria($expression);
                        } else {
                            $operators[] = array_shift($expression);
                            foreach ($expression as $parameter => $value) {
                                $qb->setParameter($parameter, $value);
                            }
                        }
                    }
                }
            }
        }

        if ($operators) {
            $qb->andWhere(...$operators);
        }
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

    public function remove(object $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function persist(object $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
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
