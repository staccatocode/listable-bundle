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

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('staccato_listable');

        $this->addBuilderSection($rootNode);
        $this->addListenerSection($rootNode);
        $this->addRepositorySection($rootNode);
        $this->addTwigSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addListenerSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('listener')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('session_filters')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')
                                    ->defaultTrue()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addRepositorySection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('repository')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('classes')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('class')
                                        ->isRequired()
                                        ->cannotBeEmpty()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addBuilderSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('builder')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('default_values')
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addTwigSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('twig')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('listable_box')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('default_values')
                                    ->useAttributeAsKey('name')
                                    ->prototype('variable')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('listable_table')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('default_values')
                                    ->useAttributeAsKey('name')
                                    ->prototype('variable')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('listable_filters')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('default_values')
                                    ->useAttributeAsKey('name')
                                    ->prototype('variable')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('listable_pagination')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('default_values')
                                    ->useAttributeAsKey('name')
                                    ->prototype('variable')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
