<?php

/*
 * This file is part of staccato listable bundle
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Bundle\ListableBundle\Tests\Extension;

use PHPUnit\Framework\TestCase;
use Staccato\Bundle\ListableBundle\Helper\UrlModifier;
use Staccato\Bundle\ListableBundle\Twig\Extension\ListableHelpersExtension;
use Twig\TwigFunction;

/**
 * @covers \Staccato\Bundle\ListableBundle\Twig\Extension\ListableHelpersExtension
 */
class ListableHelpersExtensionTest extends TestCase
{
    public function setUp()
    {
        $this->urlModifier = $this->getMockBuilder(UrlModifier::class)->getMock();
    }

    /**
     * @covers \Staccato\Bundle\ListableBundle\Twig\Extension\ListableHelpersExtension::getFunctions
     */
    public function testGetFunctions()
    {
        $extension = new ListableHelpersExtension($this->urlModifier);
        $functions = $extension->getFunctions();

        $this->assertInstanceOf(ListableHelpersExtension::class, $extension);

        $functionNamesChecked = array();
        $functionNames = array(
            'listable_url' => 'listable_url',
        );

        foreach ($functions as $f) {
            $this->assertInstanceOf(TwigFunction::class, $f);
            $functionNamesChecked[$f->getName()] = $f->getName();
        }

        $this->assertEquals($functionNames, $functionNamesChecked);
    }

    /**
     * @covers \Staccato\Bundle\ListableBundle\Twig\Extension\ListableHelpersExtension::getUrl
     */
    public function testGetUrl()
    {
        $this->urlModifier
            ->expects($this->once())
            ->method('modifyQueryString');

        $extension = new ListableHelpersExtension($this->urlModifier);
        $extension->getUrl(array(), array());
    }
}
