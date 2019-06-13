<?php

declare(strict_types=1);

namespace App\Registry\Admin;

use App\Model\AdminInterface;
use App\Model\FilterFormModel;
use App\Repository\OrderRepository;
use App\Service\FiltersHandler;
use App\Service\Paginator\Pager;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

class OrdersAdmin implements AdminInterface
{
    /** @var OrderRepository */
    private $repository;

    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getName(): string
    {
        return 'orders';
    }

    public function getColumnsList(): array
    {
        return ['id'];
    }

    public function getPager(int $page, array $filters): Pager
    {
        return $this->repository->paginate([$page], null);
    }

    public function getFilterForm(FiltersHandler $filtersHandler): FilterFormModel
    {
        return $filtersHandler->begin([]);
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
        return null;
    }

    public function setFormBuilder(FormBuilderInterface $formBuilder): void
    {
//        $formBuilder->add('name', TextType::class);
    }
}
