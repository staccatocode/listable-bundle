<?php

/*
 * This file is part of staccato listable bundle
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Bundle\ListableBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class StaccatoListableExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        foreach (array('services', 'factories') as $file) {
            $loader->load($file.'.xml');
        }

        $doctrineRepositoryFactoryName = 'staccato_listable.repository_factory.doctrine';
        $doctrineRepositoryFactoryDefition = $container->getDefinition($doctrineRepositoryFactoryName);

        if (!class_exists($doctrineRepositoryFactoryDefition->getClass())) {
            $container->removeDefinition($doctrineRepositoryFactoryName);
        }

        // Set parameters
        $container->setParameter('staccato_listable.config', $config);

        // Set aliases
        $container->setAlias('st.list', 'staccato_listable.list');
    }
}
