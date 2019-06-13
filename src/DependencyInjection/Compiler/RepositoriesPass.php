<?php

declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Service\Paginator\Paginator;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\ServiceRepositoryCompilerPass;
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
            $definition->addMethodCall('setPaginator', array(new Reference(Paginator::class)));
        }
    }
}
