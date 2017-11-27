<?php

/*
 * This file is part of staccato listable bundle
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Bundle\ListableBundle\Tests;

use PHPUnit\Framework\TestCase;
use Staccato\Bundle\ListableBundle\StaccatoListableBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @covers \Staccato\Bundle\ListableBundle\StaccatoListableBundle
 */
class StaccatoListableBundleTest extends TestCase
{
    /**
     * @covers \Staccato\Bundle\ListableBundle\StaccatoListableBundle::build
     */
    public function testBuild()
    {
        $configuration = $this->getMockBuilder(ContainerBuilder::class)->getMock();
        $configuration
            ->expects($this->once())
            ->method('addCompilerPass');

        $bundle = new StaccatoListableBundle();
        $bundle->build($configuration);
    }
}
