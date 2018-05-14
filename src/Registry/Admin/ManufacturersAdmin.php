<?php

namespace App\Registry\Admin;

use App\Model\AdminInterface;
use App\Model\FilterFormModel;
use App\Repository\ManufacturerRepository;
use App\Service\FiltersHandler;
use App\Service\Paginator\Pager;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ManufacturersAdmin implements AdminInterface
{
    /** @var ManufacturerRepository */
    private $repository;

    public function __construct(ManufacturerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getName(): string
    {
        return 'manufacturers';
    }

    public function getColumnsList(): array
    {
        return ['name'];
    }

    public function findOne(string $id): ?object
    {
        return $this->repository->find($id);
    }

    public function deleteOne(object $entity): void
    {
        $this->repository->remove($entity);
    }

    public function updateOne(object $entity): void
    {
        $this->repository->flush();
    }

    public function setFormBuilder(FormBuilderInterface $formBuilder): void
    {
        $formBuilder->add('name', TextType::class);
    }

    public function getPager(int $page, array $filters): Pager
    {
        return $this->repository->paginate([$page], null);
    }

    public function getFilterForm(FiltersHandler $filtersHandler): FilterFormModel
    {
        return $filtersHandler->begin([]);
    }
}
