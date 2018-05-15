<?php

namespace App\Registry\Admin;

use App\Entity\Manufacturer;
use App\Entity\Product;
use App\Form\Type\Category\CategorySelect2Type;
use App\Model\AdminInterface;
use App\Model\FilterFormModel;
use App\Repository\ProductRepository;
use App\Service\FiltersHandler;
use App\Service\Paginator\Pager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotNull;

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

        return $repo->paginate([$page], null, $repo->applyFilters($filters));
    }

    public function findOne(string $id): ?object
    {
        return $this->repository->find($id);
    }

    public function delete(object $entity): void
    {
        $this->repository->remove($entity, true);
    }

    public function persist(object $entity): void
    {
        $this->repository->persist($entity);
        $this->repository->flush();
    }

    public function create(Request $request): ?object
    {
        return new Product();
    }

    public function setFormBuilder(FormBuilderInterface $formBuilder): void
    {
        $formBuilder
            ->add('name', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Name',
                ],
                'constraints' => [
                    new NotNull(),
                ],
            ])
            ->add('basePrice', MoneyType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Price',
                ],
                'constraints' => [
                    new NotNull(),
                ],
            ])
            ->add('categories', CategorySelect2Type::class)
        ;
    }

    public function getFilterForm(FiltersHandler $filtersHandler): FilterFormModel
    {
        return $filtersHandler->begin([])
            ->add('categories', CategorySelect2Type::class)
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
