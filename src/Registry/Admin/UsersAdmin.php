<?php

namespace App\Registry\Admin;

use App\Entity\User;
use App\Model\AdminInterface;
use App\Model\FilterFormModel;
use App\Repository\UserRepository;
use App\Service\FiltersHandler;
use App\Service\Paginator\Pager;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

class UsersAdmin implements AdminInterface
{
    /** @var UserRepository */
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getName(): string
    {
        return 'users';
    }

    public function getColumnsList(): array
    {
        return ['username', 'spent'];
    }

    public function getPager(int $page, array $filters): Pager
    {
        return $this->repository->paginate([$page], null, $this->repository->applyFilters($filters));
    }

    public function getFilterForm(FiltersHandler $filtersHandler): FilterFormModel
    {
        return $filtersHandler->begin([])
            ->add('min_spent', MoneyType::class, [
                'attr' => [
                    'placeholder' => 'Min spent',
                ],
            ])
            ->add('max_spent', MoneyType::class, [
                'attr' => [
                    'placeholder' => 'Max spent',
                ],
            ])
            ;
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
        return new User();
    }

    public function setFormBuilder(FormBuilderInterface $formBuilder): void
    {
        $formBuilder->add('spent', MoneyType::class);
    }
}
