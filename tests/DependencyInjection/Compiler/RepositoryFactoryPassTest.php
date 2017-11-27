<?php

/*
 * This file is part of staccato listable bundle
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Bundle\ListableBundle\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Staccato\Bundle\ListableBundle\DependencyInjection\Compiler\RepositoryFactoryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @covers \Staccato\Bundle\ListableBundle\DependencyInjection\Compiler\RepositoryFactoryPass
 */
class RepositoryFactoryPassTest extends TestCase
{
    /** @var ContainerBuilder */
    protected $configuration;

    public function setUp()
    {
        $this->configuration = new ContainerBuilder();
    }

    /**
     * @covers \Staccato\Bundle\ListableBundle\DependencyInjection\Compiler\RepositoryFactoryPass::process
     */
    public function testRepositoryFactoryCompilerPass()
    {
        $this->configuration
            ->register('staccato_listable.repository_factory')
            ->setPublic(true);

        $this->configuration
            ->register('staccato_listable.repository_factory.valid_factory')
            ->addTag('staccato_listable.repository_factory', array('alias' => 'valid_factory'))
            ->setPublic(false);

        $this->configuration
            ->register('staccato_listable.repository_factory.invalid_factory')
            ->addTag('staccato_listable.repository_factory', array())
            ->setPublic(false);

        $repositoryFactoryPass = new RepositoryFactoryPass();
        $repositoryFactoryPass->process($this->configuration);

        $repositoryFactoryDefinition = $this->configuration->findDefinition('staccato_listable.repository_factory');

        $this->assertTrue($repositoryFactoryDefinition->hasMethodCall('add'));
        $this->assertEquals(1, count($repositoryFactoryDefinition->getMethodCalls()));
    }
}
