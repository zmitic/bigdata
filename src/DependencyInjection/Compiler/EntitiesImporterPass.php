<?php

declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Model\Importer\EntityImporterInterface;
use App\Service\EntitiesImporter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EntitiesImporterPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public function process(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition(EntitiesImporter::class);
        $tagged = $this->findAndSortTaggedServices(EntityImporterInterface::TAG, $container);
        $definition->setArgument(0, $tagged);
    }
}
