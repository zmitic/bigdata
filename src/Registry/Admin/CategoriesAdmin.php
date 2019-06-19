<?php

declare(strict_types=1);

namespace App\Registry\Admin;

use App\Entity\Category;
use App\Form\Type\Category\ProductSelect2Type;
use App\Model\FilterFormModel;
use App\Repository\CategoryRepository;
use App\Service\FiltersHandler;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotNull;

class CategoriesAdmin extends AbstractAdmin
{
    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getName(): string
    {
        return 'categories';
    }

    public function getColumnsList(): array
    {
        return ['name', 'nrOfProducts'];
    }

    public function getFilterForm(FiltersHandler $filtersHandler): FilterFormModel
    {
        return $filtersHandler->begin([])
            ->add('min_nr_of_products', IntegerType::class, [
                'attr' => [
                    'placeholder' => 'Min number of products',
                ],
            ]);
    }

    public function create(Request $request): ?object
    {
        return new Category();
    }

    public function setFormBuilder(FormBuilderInterface $formBuilder): void
    {
        $formBuilder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotNull(),
                ],
            ])
            ->add('products', ProductSelect2Type::class);
    }
}
