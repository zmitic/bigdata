<?php

declare(strict_types=1);

namespace App\Form\Type\Category;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class ProductSelect2Type extends AbstractType
{
    public function getParent(): string
    {
        return Select2EntityType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'remote_route' => 'suggestions_products',
            'multiple' => true,
            'minimum_input_length' => 0,
            'allow_clear' => true,
            'delay' => 250,
            'cache' => true,
            'cache_timeout' => 60000,
            'class' => Product::class,
        ]);
    }
}
