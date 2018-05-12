<?php

namespace App\Registry\Admin;

use App\Entity\Manufacturer;
use App\Model\AdminInterface;
use App\Model\FilterFormModel;
use App\Repository\ProductRepository;
use App\Service\FiltersHandler;
use App\Service\Paginator\Pager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class ProductsAdmin implements AdminInterface
{
    /** @var ProductRepository */
    private $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getName(): string
    {
        return 'products';
    }

    public function getColumnsList(): array
    {
        return ['name', 'manufacturer', 'categories', 'basePrice'];
    }

    public function getPager(int $page, array $filters): Pager
    {
        $repo = $this->repository;

        return $repo->paginate($page, null, $repo->applyFilters($filters));
    }

    public function getFilterForm(FiltersHandler $filtersHandler): FilterFormModel
    {
        return $filtersHandler->begin([])
            ->add('manufacturer', EntityType::class, [
                'class' => Manufacturer::class,
                'placeholder' => '-- Select manufacturer --',
                'query_builder' => function (EntityRepository $repo) {
                    return $repo->createQueryBuilder('o')->orderBy('o.name')->setMaxResults(50);
                },
            ])
            ->add('min_price', MoneyType::class, [
                'attr' => [
                    'placeholder' => 'Min price',
                ],
            ])
            ->add('max_price', MoneyType::class, [
                'attr' => [
                    'placeholder' => 'Max price',
                ],
            ]);
    }
}
