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
use Staccato\Bundle\ListableBundle\Twig\Extension\ListableExtension;
use Twig\TwigFunction;

/**
 * @covers \Staccato\Bundle\ListableBundle\Twig\Extension\ListableExtension
 */
class ListableExtensionTest extends TestCase
{
    /**
     * @covers \Staccato\Bundle\ListableBundle\Twig\Extension\ListableExtension::getFunctions
     */
    public function testGetFunctions()
    {
        $extension = new ListableExtension();
        $functions = $extension->getFunctions();

        $this->assertInstanceOf(ListableExtension::class, $extension);

        $functionNamesChecked = array();
        $functionNames = array(
            'listable_box' => 'listable_box',
            'listable_table' => 'listable_table',
            'listable_pagination' => 'listable_pagination',
            'listable_filters' => 'listable_filters',
        );

        foreach ($functions as $f) {
            $this->assertInstanceOf(TwigFunction::class, $f);
            $functionNamesChecked[$f->getName()] = $f->getName();
        }

        $this->assertEquals($functionNames, $functionNamesChecked);
    }
}
