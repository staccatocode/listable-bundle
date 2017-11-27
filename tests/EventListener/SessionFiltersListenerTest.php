<?php

/*
 * This file is part of staccato listable bundle
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Bundle\ListableBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Staccato\Bundle\ListableBundle\EventListener\SessionFiltersListener;
use Staccato\Component\Listable\ListRequestInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * @covers \Staccato\Bundle\ListableBundle\EventListener\SessionFiltersListener
 */
class SessionFiltersListenerTest extends TestCase
{
    public function setUp()
    {
        $this->listRequest = $this->getMockBuilder(ListRequestInterface::class)->getMock();
    }

    /**
     * @covers \Staccato\Bundle\ListableBundle\EventListener\SessionFiltersListener::__construct
     */
    public function testCreate()
    {
        $listener = new SessionFiltersListener($this->listRequest, array());

        $reflection = new \ReflectionClass($listener);
        $reflectionProperty = $reflection->getProperty('actionParam');
        $reflectionProperty->setAccessible(true);

        $this->assertInstanceOf(SessionFiltersListener::class, $listener);
        $this->assertEquals('st_list', $reflectionProperty->getValue($listener));

        $listener = new SessionFiltersListener($this->listRequest, array(
            'builder' => array(
                'default_values' => array('action_param' => 'test'),
            ),
        ));

        $reflection = new \ReflectionClass($listener);
        $reflectionProperty = $reflection->getProperty('actionParam');
        $reflectionProperty->setAccessible(true);

        $this->assertInstanceOf(SessionFiltersListener::class, $listener);
        $this->assertEquals('test', $reflectionProperty->getValue($listener));
    }

    /**
     * @covers \Staccato\Bundle\ListableBundle\EventListener\SessionFiltersListener::onKernelRequest
     */
    public function testOnKernelRequestFiltersApplay()
    {
        $postData = array(
            'action' => 'filters.apply',
            'name' => 'list',
            'list' => array('test' => 'value'),
        );

        $request = $this->getMockRequest('list', 'st_list', $postData);
        $event = $this->getMockGetResponseEvent($request, true);

        $this->listRequest
            ->expects($this->once())
            ->method('storeFilters')
            ->with('list', $postData['list']);

        $listener = new SessionFiltersListener($this->listRequest, array());
        $listener->onKernelRequest($event);
    }

    /**
     * @covers \Staccato\Bundle\ListableBundle\EventListener\SessionFiltersListener::onKernelRequest
     */
    public function testOnKernelRequestFiltersClear()
    {
        $postData = array(
            'action' => 'filters.clear',
            'name' => 'list',
        );

        $request = $this->getMockRequest('list', 'st_list', $postData);
        $event = $this->getMockGetResponseEvent($request, true);

        $this->listRequest
            ->expects($this->once())
            ->method('storeFilters')
            ->with('list', array());

        $listener = new SessionFiltersListener($this->listRequest, array());
        $listener->onKernelRequest($event);
    }

    /**
     * @covers \Staccato\Bundle\ListableBundle\EventListener\SessionFiltersListener::onKernelRequest
     */
    public function testOnKernelRequestNotMasterRequest()
    {
        $request = $this->getMockRequest('list', 'st_list', array());
        $event = $this->getMockGetResponseEvent($request, false);

        $this->listRequest
            ->expects($this->never())
            ->method('storeFilters');

        $listener = new SessionFiltersListener($this->listRequest, array());
        $listener->onKernelRequest($event);
    }

    private function getMockRequest($listName, $actionParam, array $postData)
    {
        $request = $this->getMockBuilder(Request::class)->getMock();
        $request->request = $this->getMockBuilder(ParameterBag::class)->getMock();

        $request
            ->method('isMethod')
            ->with('post')
            ->willReturn(true);

        $request
            ->method('getUri')
            ->willReturn('/');

        $request->request
            ->method('has')
            ->with($actionParam)
            ->willReturn(true);

        $request->request
            ->method('get')
            ->with($this->logicalOr(
                $actionParam, $listName
            ))
            ->will($this->returnValueMap(array(
                array($actionParam, array(), $postData),
                array($listName, array(), isset($postData[$listName]) ? $postData[$listName] : array()),
            )));

        return $request;
    }

    private function getMockGetResponseEvent($request, $isMasterRequest)
    {
        $event = $this->getMockBuilder(GetResponseEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $event
            ->method('isMasterRequest')
            ->willReturn($isMasterRequest);

        if ($isMasterRequest) {
            $event
                ->method('getRequest')
                ->willReturn($request);

            $event
                ->expects($this->once())
                ->method('setResponse');
        }

        return $event;
    }
}
