<?php

/*
 * This file is part of staccato listable bundle
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Bundle\ListableBundle\Service;

use Staccato\Component\Listable\ListBuilder;
use Staccato\Component\Listable\ListBuilderInterface;
use Staccato\Component\Listable\ListRequest;
use Staccato\Component\Listable\ListRequestInterface;
use Staccato\Component\Listable\Repository\AbstractRepository;
use Staccato\Component\Listable\Repository\Exception\InvalidRepositoryFactoryException;
use Staccato\Component\Listable\Repository\RepositoryFactory;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RequestStack;

class ListableService
{
    /**
     * @var ListRequestInterface
     */
    private $listRequest;

    /**
     * @var RepositoryFactory
     */
    private $repositoryFactory;

    /**
     * @var ParameterBag
     */
    private $config;

    public function __construct(RepositoryFactory $repositoryFactory, ListRequestInterface $listRequest, array $config)
    {
        $this->repositoryFactory = $repositoryFactory;
        $this->listRequest = $listRequest;
        $this->config = new ParameterBag($config);
    }

    /**
     * Create pre-configured list builder.
     *
     * @param string $name repository factory name or repository class
     * @param mixed  $data
     *
     * @throws InvalidRepositoryFactoryException
     *
     * @return ListBuilderInterface
     */
    public function use(string $name, $data = null): ListBuilderInterface
    {
        $name = $this->resolveClass($name);

        if ($this->repositoryFactory->has($name)) {
            $repository = $this->repositoryFactory->create($name, $data);
        } elseif ($this->repositoryFactory->has('class') && is_subclass_of($name, AbstractRepository::class)) {
            $repository = $this->repositoryFactory->create('class', array($name, $data));
        } else {
            throw new InvalidRepositoryFactoryException(sprintf(
                'Repository factory `%s` does not exists.', $name
            ));
        }

        $listBuilder = new ListBuilder($this->listRequest);
        $listBuilder->setRepository($repository);

        $this->applyDefaultValues($listBuilder);

        return $listBuilder;
    }

    /**
     * Try to resolve class by name.
     *
     * @param string $name
     *
     * @return string Resolved class or name if not resolved
     */
    protected function resolveClass(string $name): string
    {
        $classes = $this->config->get('repository', array('classes' => array()));
        $classes = $classes['classes'];

        if (isset($classes[$name]['class'])) {
            $name = $classes[$name]['class'];
        }

        return $name;
    }

    /**
     * Apply default values.
     *
     * @param ListBuilder $listBuilder
     */
    protected function applyDefaultValues(ListBuilder $listBuilder): void
    {
        $defaultValues = $this->config->get('builder', array('default_values' => array()));
        $defaultValues = $defaultValues['default_values'];

        $methods = preg_grep('/^set/', get_class_methods($listBuilder));

        foreach ($defaultValues as $method => $value) {
            $method = 'set'.$this->toCamelCase($method);

            if (in_array($method, $methods)) {
                $listBuilder->$method($value);
            }
        }
    }

    /**
     * Convert dashes to camel case.
     *
     * @param string $string
     *
     * @return string
     */
    protected function toCamelCase(string $string): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }
}
