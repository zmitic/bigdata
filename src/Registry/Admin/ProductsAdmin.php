<?php

declare(strict_types=1);

namespace App\Registry\Admin;

use App\Entity\Manufacturer;
use App\Entity\Product;
use App\Form\Type\Category\CategorySelect2Type;
use App\Model\FilterFormModel;
use App\Repository\ProductRepository;
use App\Service\FiltersHandler;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotNull;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class ProductsAdmin extends AbstractAdmin
{
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

    public function create(Request $request): ?object
    {
        return new Product();
    }

    public function setFormBuilder(FormBuilderInterface $formBuilder): void
    {
        $formBuilder
            ->add('name', TextType::class, [
                'attr' => [
                    'placeholder' => 'Name',
                ],
                'constraints' => [
                    new NotNull(),
                ],
            ])
            ->add('basePrice', MoneyType::class, [
                'attr' => [
                    'placeholder' => 'Price',
                ],
                'constraints' => [
                    new NotNull(),
                ],
            ])
            ->add('manufacturer', Select2EntityType::class, [
                'remote_route' => 'suggestions_manufacturers',
                'multiple' => false,
                'minimum_input_length' => 0,
                'allow_clear' => true,
                'delay' => 250,
                'cache' => true,
                'cache_timeout' => 60000,
                'class' => Manufacturer::class,
            ])
            ->add('categories', CategorySelect2Type::class)
        ;
    }

    public function getFilterForm(FiltersHandler $filtersHandler): FilterFormModel
    {
        return $filtersHandler->begin([])
            ->add('categories', CategorySelect2Type::class)
            ->add('manufacturer', EntityType::class, [
                'required' => false,
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
