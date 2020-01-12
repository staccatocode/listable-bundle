<?php

/*
 * This file is part of staccato listable bundle
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Bundle\ListableBundle\Tests\Compiler;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Staccato\Bundle\ListableBundle\Service\ListRequest;
use Symfony\Component\HttpFoundation\RequestStack;

class ListRequestTest extends TestCase
{
    public function testCreate(): void
    {
        /** @var MockObject|RequestStack $mockStackRequest */
        $mockStackRequest = $this->getMockBuilder(RequestStack::class)->getMock();
        $mockStackRequest
            ->expects($this->once())
            ->method('getMasterRequest')
            ->willReturn(null)
        ;

        new ListRequest($mockStackRequest);
    }
}
