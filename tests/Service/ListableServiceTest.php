<?php

/*
 * This file is part of staccato listable bundle
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Bundle\ListableBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Staccato\Bundle\ListableBundle\Service\ListableService;
use Staccato\Bundle\ListableBundle\Tests\FakeRepository;
use Staccato\Component\Listable\ListRequestInterface;
use Staccato\Component\Listable\Repository\Exception\InvalidRepositoryFactoryException;
use Staccato\Component\Listable\Repository\RepositoryFactory;

/**
 * @covers \Staccato\Bundle\ListableBundle\Service\ListableService
 */
class ListableServiceTest extends TestCase
{
    public function setUp()
    {
        $this->listRequest = $this->getMockBuilder(ListRequestInterface::class)->getMock();
        $this->repositoryFactory = $this->getMockBuilder(RepositoryFactory::class)->getMock();
    }

    /**
     * @covers \Staccato\Bundle\ListableBundle\Service\ListableService::__construct
     */
    public function testCreate()
    {
        $listable = new ListableService($this->repositoryFactory, $this->listRequest, array());

        $this->assertInstanceOf(ListableService::class, $listable);
    }

    /**
     * @covers \Staccato\Bundle\ListableBundle\Service\ListableService::use
     */
    public function testInvalidRepositoryFactoryException()
    {
        $this->expectException(InvalidRepositoryFactoryException::class);

        $listable = new ListableService($this->repositoryFactory, $this->listRequest, array());
        $listable->use('unknown', array());
    }

    /**
     * @covers \Staccato\Bundle\ListableBundle\Service\ListableService::use
     */
    public function testUseRepostiroyFactory()
    {
        $config = array(
            'builder' => array(
                'default_values' => array(
                    'limit' => 10,
                    'invalid_param' => 'invalid',
                ),
            ),
        );

        $fakeRepository = $this->getMockBuilder(FakeRepository::class)->getMock();

        $this->repositoryFactory
            ->method('has')
            ->with('test')
            ->willReturn(true);

        $this->repositoryFactory
            ->expects($this->once())
            ->method('create')
            ->with('test')
            ->willReturn($fakeRepository);

        $listable = new ListableService($this->repositoryFactory, $this->listRequest, $config);
        $listBuilder = $listable->use('test', array(1, 2));

        $this->assertEquals(10, $listBuilder->getLimit());
    }

    /**
     * @covers \Staccato\Bundle\ListableBundle\Service\ListableService::use
     */
    public function testUseRepostiroyFactoryClass()
    {
        $fakeRepository = $this->getMockBuilder(FakeRepository::class)->getMock();

        $this->repositoryFactory
            ->method('has')
            ->with($this->logicalOr(
                FakeRepository::class,
                'class'
            ))
            ->will($this->returnValueMap(array(
                array(FakeRepository::class, false),
                array('class', true),
            )));

        $this->repositoryFactory
            ->expects($this->once())
            ->method('create')
            ->with('class')
            ->willReturn($fakeRepository);

        $listable = new ListableService($this->repositoryFactory, $this->listRequest, array());
        $listable->use(FakeRepository::class, array(1, 2));
    }

    /**
     * @covers \Staccato\Bundle\ListableBundle\Service\ListableService::use
     */
    public function testUseRepostiroyFactoryClassAlias()
    {
        $config = array(
            'repository' => array(
                'classes' => array(
                    'fake' => array(
                        'class' => FakeRepository::class,
                    ),
                ),
            ),
        );

        $fakeRepository = $this->getMockBuilder(FakeRepository::class)->getMock();

        $this->repositoryFactory
            ->method('has')
            ->with($this->logicalOr(
                FakeRepository::class,
                'class'
            ))
            ->will($this->returnValueMap(array(
                array(FakeRepository::class, false),
                array('class', true),
            )));

        $this->repositoryFactory
            ->expects($this->once())
            ->method('create')
            ->with('class')
            ->willReturn($fakeRepository);

        $listable = new ListableService($this->repositoryFactory, $this->listRequest, $config);
        $listable->use('fake', array(1, 2));
    }
}
