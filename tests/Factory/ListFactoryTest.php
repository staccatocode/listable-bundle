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
use Staccato\Bundle\ListableBundle\Factory\ListFactory;
use Staccato\Component\Listable\AbstractType;
use Staccato\Component\Listable\ListBuilderInterface;
use Staccato\Component\Listable\ListRegistryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $type = new class() extends AbstractType {
            public $options = [];

            public function buildList(ListBuilderInterface $builder, array $options): void
            {
                $this->options = $options;
            }

            public function configureOptions(OptionsResolver $resolver): void
            {
                $resolver->setDefaults([
                    'a' => null,
                    'b' => null,
                    'c' => null,
                ]);
            }
        };

        /** @var MockObject|ListRegistryInterface $mockRegistry */
        $mockRegistry = $this->getMockBuilder(ListRegistryInterface::class)->getMock();
        $mockRegistry
            ->expects($this->once())
            ->method('getListType')
            ->willReturn($type)
        ;

        $defaultOptions = [
            'builder' => [
                'default_options' => [
                    'a' => 1,
                    'b' => 2,
                ],
            ],
        ];

        $listFactory = new ListFactory($mockRegistry, $defaultOptions);
        $listFactory->create('test', ['a' => 0, 'c' => 3]);

        $this->assertArrayHasKey('a', $type->options);
        $this->assertArrayHasKey('b', $type->options);
        $this->assertArrayHasKey('c', $type->options);
        $this->assertSame(0, $type->options['a']);
        $this->assertSame(2, $type->options['b']);
        $this->assertSame(3, $type->options['c']);
    }
}
