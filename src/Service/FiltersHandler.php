<?php

namespace App\Service;

use App\Model\FilterFormModel;
use Symfony\Component\Form\FormFactoryInterface;

class FiltersHandler
{
    /** @var FormFactoryInterface */
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function begin(array $defaultData = []): FilterFormModel
    {
        return new FilterFormModel($this->formFactory, array_merge(['page' => 1, 'size' => 25], $defaultData));
    }
}
