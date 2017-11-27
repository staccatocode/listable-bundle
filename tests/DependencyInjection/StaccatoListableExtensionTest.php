<?php

/*
 * This file is part of staccato listable bundle
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Bundle\ListableBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Staccato\Bundle\ListableBundle\DependencyInjection\StaccatoListableExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

/**
 * @covers \Staccato\Bundle\ListableBundle\DependencyInjection\Configuration
 * @covers \Staccato\Bundle\ListableBundle\DependencyInjection\StaccatoListableExtension
 */
class StaccatoListableExtensionTest extends TestCase
{
    /** @var ContainerBuilder */
    protected $configuration;

    public function setUp()
    {
        $this->extension = new StaccatoListableExtension();
        $this->configuration = new ContainerBuilder();
    }

    /**
     * @covers \Staccato\Bundle\ListableBundle\DependencyInjection\StaccatoListableExtension::load
     * @covers \Staccato\Bundle\ListableBundle\DependencyInjection\StaccatoListableExtension::prepend
     */
    public function testDefaultConfig()
    {
        $this->extension->prepend($this->configuration);

        $configPrepend = $this->configuration->getExtensionConfig($this->extension->getAlias());
        $config = array();

        $this->extension->load(array_merge($configPrepend, array($config)), $this->configuration);

        $this->assertHasDefinition('staccato_listable.list_request');
        $this->assertHasDefinition('staccato_listable.event_listener.session_filters');
        $this->assertHasDefinition('staccato_listable.repository_factory');
        $this->assertHasDefinition('staccato_listable.repository_factory.class');
        $this->assertHasDefinition('staccato_listable.list');
        $this->assertHasDefinition('staccato_listable.helper.url_modifier');
        $this->assertHasDefinition('staccato_listable.twig.listable');
        $this->assertHasDefinition('staccato_listable.twig.listable_helpers');
        $this->assertHasDefinition('staccato_listable.twig.renderer.table');
        $this->assertHasDefinition('staccato_listable.twig.renderer.pagination');
        $this->assertHasDefinition('staccato_listable.twig.renderer.box');
        $this->assertHasDefinition('staccato_listable.twig.renderer.filters');
        $this->assertAlias('staccato_listable.list', 'st.list');
        $this->assertHasParameter('staccato_listable.config');

        $listableConfig = $this->configuration->getParameter('staccato_listable.config');

        $this->assertInternalType('array', $listableConfig['builder']['default_values']);
        $this->assertTrue($listableConfig['listener']['session_filters']['enabled']);
        $this->assertInternalType('array', $listableConfig['repository']['classes']);
        $this->assertInternalType('array', $listableConfig['twig']['listable_box']['default_values']);
        $this->assertInternalType('array', $listableConfig['twig']['listable_table']['default_values']);
        $this->assertInternalType('array', $listableConfig['twig']['listable_filters']['default_values']);
        $this->assertInternalType('array', $listableConfig['twig']['listable_pagination']['default_values']);
    }

    /**
     * @covers \Staccato\Bundle\ListableBundle\DependencyInjection\StaccatoListableExtension::load
     * @covers \Staccato\Bundle\ListableBundle\DependencyInjection\StaccatoListableExtension::prepend
     */
    public function testCustomConfig()
    {
        $this->extension->prepend($this->configuration);

        $configPrepend = $this->configuration->getExtensionConfig($this->extension->getAlias());
        $config = $this->getFullConfig();
        $config['listener']['session_filters']['enabled'] = false;

        $this->extension->load(array_merge($configPrepend, array($config)), $this->configuration);
        $this->assertNotHasDefinition('staccato_listable.event_listener.session_filters');
    }

    private function getFullConfig()
    {
        $yaml = <<<EOF
builder:
    default_values:
        limit: 10

listener:
    session_filters:
        enabled: true

repository:
    classes: ~

twig:
    listable_box:
        default_values:
            class: 'test'
            custom: 'value'

    listable_table:
        default_values:
            class: 'table'
            custom: 'value'

    listable_filters:
        default_values:
            class: 'filters'
            columns: 1
            custom: 'value'

    listable_pagination:
        default_values:
            class: 'pagination'
            sides: 1
            custom: 'value'
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    private function assertAlias($value, $key)
    {
        $this->assertSame($value, (string) $this->configuration->getAlias($key), sprintf('%s alias is correct', $key));
    }

    private function assertParameter($value, $key)
    {
        $this->assertSame($value, $this->configuration->getParameter($key), sprintf('%s parameter is correct', $key));
    }

    private function assertHasParameter($key)
    {
        $this->assertTrue($this->configuration->hasParameter($key), sprintf('%s parameter is correct', $key));
    }

    private function assertHasDefinition($id)
    {
        $this->assertTrue(($this->configuration->hasDefinition($id) ?: $this->configuration->hasAlias($id)));
    }

    private function assertNotHasDefinition($id)
    {
        $this->assertFalse(($this->configuration->hasDefinition($id) ?: $this->configuration->hasAlias($id)));
    }
}
