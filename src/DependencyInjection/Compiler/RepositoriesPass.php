<?php

namespace App\DependencyInjection\Compiler;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\ServiceRepositoryCompilerPass;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RepositoriesPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public function process(ContainerBuilder $container): void
    {
        $tagged = $container->findTaggedServiceIds(ServiceRepositoryCompilerPass::REPOSITORY_SERVICE_TAG);
        foreach (array_keys($tagged) as $id) {
            $definition = $container->getDefinition($id);
            $definition->addMethodCall('setPaginator', array(new Reference(PaginatorInterface::class)));
        }
    }
}
