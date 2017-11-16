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

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RepositoryFactoryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $repositoryFactoryId = 'staccato_listable.repository_factory';

        if (!$container->has($repositoryFactoryId)) {
            return;
        }

        $repositoryFactoryDefinition = $container->findDefinition($repositoryFactoryId);
        $repositoryFactoriesTags = $container->findTaggedServiceIds('staccato_listable.repository_factory');

        foreach ($repositoryFactoriesTags as $id => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['alias'])) {
                    continue;
                }

                $repositoryFactoryDefinition->addMethodCall('add', array($tag['alias'], new Reference($id)));
            }
        }
    }
}
