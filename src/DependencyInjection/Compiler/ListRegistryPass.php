<?php

/*
 * This file is part of staccato listable bundle
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Bundle\ListableBundle\DependencyInjection\Compiler;

use Staccato\Component\Listable\Field\AbstractField;
use Staccato\Component\Listable\Filter\AbstractFilter;
use Staccato\Component\Listable\ListStateProviderInterface;
use Staccato\Component\Listable\Repository\AbstractRepository;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ListRegistryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->configureListableServices($container);
        $this->configureListableExternalServices($container);
    }

    private function configureListableServices(ContainerBuilder $container)
    {
        foreach ($container->getServiceIds() as $id) {
            if (!$container->hasDefinition($id)) {
                continue;
            }
            $definition = $container->getDefinition($id);
            $class = $definition->getClass();
            if (is_subclass_of($class, AbstractField::class)) {
                $definition->addTag('staccato_listable.field')->setShared(false);
            } elseif (is_subclass_of($class, AbstractFilter::class)) {
                $definition->addTag('staccato_listable.filter')->setShared(false);
            } elseif (is_subclass_of($class, AbstractRepository::class)) {
                $definition->addTag('staccato_listable.repository');
            } elseif (is_subclass_of($class, ListStateProviderInterface::class)) {
                $definition->addTag('staccato_listable.state_provider');
            }
        }
    }

    private function configureListableExternalServices(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('doctrine.orm.default_entity_manager') ||
            !class_exists('Staccato\Component\Listable\Doctrine\Repository\DoctrineRepository')) {
            $container->removeDefinition('Staccato\Component\Listable\Doctrine\Repository\DoctrineRepository');
        }
    }
}
