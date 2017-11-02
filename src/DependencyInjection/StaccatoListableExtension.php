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
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class StaccatoListableExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $doctrineRepositoryFactoryName = 'staccato_listable.repository_factory.doctrine';
        $doctrineRepositoryFactoryDefition = $container->getDefinition($doctrineRepositoryFactoryName);

        if (!class_exists($doctrineRepositoryFactoryDefition->getClass())) {
            $container->removeDefinition($doctrineRepositoryFactoryName);

            if ($config['repository']['factories']['doctrine']['service'] === $doctrineRepositoryFactoryName) {
                unset($config['repository']['factories']['doctrine']);
            }
        }

        // Set parameters
        $container->setParameter('staccato_listable.config', $config);

        // Set aliases
        $container->setAlias('st.list', 'staccato_listable.list');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        $this->prependDefaultRepositoryFactories($container);
    }

    /**
     * Pre-configure repository factories.
     *
     * @param ContainerBuilder $container
     */
    protected function prependDefaultRepositoryFactories(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig($this->getAlias(), array(
            'repository' => array(
                'factories' => array(
                    'class' => array(
                        'service' => 'staccato_listable.repository_factory.class',
                    ),
                    'doctrine' => array(
                        'service' => 'staccato_listable.repository_factory.doctrine',
                    ),
                ),
            ),
        ));
    }
}
