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

/**
 * @covers \Staccato\Bundle\ListableBundle\DependencyInjection\Configuration
 * @covers \Staccato\Bundle\ListableBundle\DependencyInjection\StaccatoListableExtension
 */
class StaccatoListableExtensionTest extends TestCase
{
    /** @var ContainerBuilder|null */
    private $configuration;

    public function setUp(): void
    {
        $this->configuration = new ContainerBuilder();
    }

    /**
     * @covers \Staccato\Bundle\ListableBundle\DependencyInjection\StaccatoListableExtension::load
     */
    public function testDefaultConfig(): void
    {
        $extension = new StaccatoListableExtension();

        $configPrepend = $this->configuration->getExtensionConfig($extension->getAlias());
        $config = [];

        $extension->load(array_merge($configPrepend, [$config]), $this->configuration);

        $this->assertHasDefinition('staccato_listable.default.state_provider');
        $this->assertHasDefinition('staccato_listable.default.factory');
        $this->assertHasDefinition('staccato_listable.default.request');
        $this->assertHasDefinition('staccato_listable.default.registry');
        $this->assertHasDefinition('staccato_listable.helper.url_modifier');

        $this->assertHasParameter('staccato_listable.config');

        $listableConfig = $this->configuration->getParameter('staccato_listable.config');

        $this->assertIsArray($listableConfig['builder']['default_options']);
    }

    private function assertHasParameter(string $key): void
    {
        $this->assertTrue($this->configuration->hasParameter($key), sprintf('%s parameter is correct', $key));
    }

    private function assertHasDefinition(string $id): void
    {
        $this->assertTrue(($this->configuration->hasDefinition($id) ?: $this->configuration->hasAlias($id)));
    }
}
