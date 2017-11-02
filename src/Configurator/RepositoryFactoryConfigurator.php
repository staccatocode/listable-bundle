<?php

/*
 * This file is part of staccato listable bundle
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Bundle\ListableBundle\Configurator;

use Staccato\Component\Listable\Repository\RepositoryFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class RepositoryFactoryConfigurator
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ParameterBag
     */
    private $config;

    public function __construct(ContainerInterface $container, array $config)
    {
        $this->container = $container;
        $this->config = new ParameterBag($config);
    }

    /**
     * Configure repository factory.
     *
     * @param RepositoryFactory $repositoryFactory
     */
    public function configure(RepositoryFactory $repositoryFactory): void
    {
        $this->configureRepositoryFactories($repositoryFactory);
    }

    /**
     * Configure repository factories.
     *
     * @param RepositoryFactory $repositoryFactory
     */
    protected function configureRepositoryFactories(RepositoryFactory $repositoryFactory): void
    {
        $repositoryFactoryServices = $this->config->get('repository', array('factories' => array()));
        $repositoryFactoryServices = $repositoryFactoryServices['factories'];

        foreach ($repositoryFactoryServices as $name => $factory) {
            if ($this->container->has($factory['service'])) {
                $repositoryFactory->add($name, $this->container->get($factory['service']));
            }
        }
    }
}
