<?php

declare(strict_types=1);

namespace App\Model;

use App\Form\Type\FilterType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FilterFormModel
{
    private $fields = [];

    /** @var FormFactoryInterface */
    private $formFactory;

    private $defaultData;

    public function __construct(FormFactoryInterface $formFactory, array $defaultData = [])
    {
        $this->formFactory = $formFactory;
        $this->defaultData = $defaultData;
    }

    public function add($child, $type = null, array $options = []): self
    {
        $this->fields[] = [$child, $type, $options];

        return $this;
    }

    public function getForm(Request $request): FormInterface
    {
        $formBuilder = $this->formFactory->createBuilder(FilterType::class, [], [
            'csrf_protection' => false,
            'method' => 'GET',
            'allow_extra_fields' => true,
        ]);

        foreach ($this->fields as [$child, $type, $options]) {
            $formBuilder->add($child, $type, array_merge($options, [
                'label' => false,
            ]));
        }

        if (isset($this->defaultData['sort']) && false !== $this->defaultData['sort']) {
            $formBuilder->add('direction', HiddenType::class, [
                'empty_data' => $request->query->getAlnum('direction', 'asc'),
            ]);
            $formBuilder->add('sort', HiddenType::class, [
                'empty_data' => $request->query->getAlnum('sort', 'o.name'),
            ]);
        }

        $formBuilder->setMethod('GET');
        $form = $formBuilder->getForm();

        $clone = clone $request;
        foreach ($this->defaultData as $key => $value) {
            if (false !== $value && !$clone->query->has($key)) {
                $clone->query->set($key, $value);
            }
        }
        $form->handleRequest($clone);

        return $form;
    }
}
