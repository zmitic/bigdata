<?php

namespace App\Registry\Admin;

use App\Entity\Category;
use App\Form\Type\Category\ProductSelect2Type;
use App\Model\AdminInterface;
use App\Model\FilterFormModel;
use App\Repository\CategoryRepository;
use App\Service\FiltersHandler;
use App\Service\Paginator\Pager;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotNull;

class CategoriesAdmin implements AdminInterface
{
    /** @var CategoryRepository */
    private $repository;

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

    public function getPager(int $page, array $filters): Pager
    {
        return $this->repository->paginate([$page], null, $this->repository->applyFilters($filters));
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
