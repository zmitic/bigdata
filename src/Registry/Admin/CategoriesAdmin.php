<?php

namespace App\Registry\Admin;

use App\Model\AdminInterface;
use App\Model\FilterFormModel;
use App\Repository\CategoryRepository;
use App\Service\FiltersHandler;
use App\Service\Paginator\Pager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CategoriesAdmin implements AdminInterface
{
    /** @var CategoryRepository */
    private $repository;

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(CategoryRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    public function getName(): string
    {
        return 'categories';
    }

    public function getColumnsList(): array
    {
        return ['name'];
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

    public function deleteOne(object $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    public function updateOne(object $entity): void
    {
        $this->em->flush();
    }

    public function setFormBuilder(FormBuilderInterface $formBuilder): void
    {
        $formBuilder->add('name', TextType::class);
    }
}
