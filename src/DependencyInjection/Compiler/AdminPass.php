<?php

declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Model\AdminInterface;
use App\Service\Admin;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AdminPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public function process(ContainerBuilder $container): void
    {
        $definition = $container->findDefinition(Admin::class);
        $tagged = $this->findAndSortTaggedServices(AdminInterface::TAG, $container);
        $definition->setArgument(0, $tagged);
    }
}
