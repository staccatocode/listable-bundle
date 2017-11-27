<?php

/*
 * This file is part of staccato listable bundle
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Bundle\ListableBundle\Tests\Helper;

use PHPUnit\Framework\TestCase;
use Staccato\Bundle\ListableBundle\Helper\UrlModifier;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \Staccato\Bundle\ListableBundle\Helper\UrlModifier
 */
class UrlModifierTest extends TestCase
{
    /**
     * @covers \Staccato\Bundle\ListableBundle\Helper\UrlModifier::__construct
     */
    public function testCreate()
    {
        $urlModifier = new UrlModifier();

        $this->assertInstanceOf(UrlModifier::class, $urlModifier);
    }

    /**
     * @covers \Staccato\Bundle\ListableBundle\Helper\UrlModifier::modifyQueryString
     */
    public function testModifyQueryString()
    {
        $request = $this->getMockBuilder(Request::class)->getMock();
        $request->query = $this->getMockBuilder(ParameterBag::class)->getMock();

        $urlModifier = new UrlModifier();

        $reflection = new \ReflectionClass($urlModifier);
        $reflectionProperty = $reflection->getProperty('request');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($urlModifier, $request);

        $request
            ->method('getSchemeAndHttpHost')
            ->willReturn('http://example.com');

        $request
            ->method('getBaseUrl')
            ->willReturn('');

        $request
            ->method('getPathInfo')
            ->willReturn('/test');

        $request->query
            ->method('all')
            ->willReturn(array('param1' => 'original', 'param2' => 'original'));

        $this->assertEquals('http://example.com/test?param1=original&param2=original',
            $urlModifier->modifyQueryString(array(), array()));
        $this->assertEquals('http://example.com/test?param2=changed&param3=test',
            $urlModifier->modifyQueryString(array('param2' => 'changed', 'param3' => 'test'), array('param1')));
    }
}
