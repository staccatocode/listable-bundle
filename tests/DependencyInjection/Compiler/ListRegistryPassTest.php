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
use Staccato\Bundle\ListableBundle\DependencyInjection\Compiler\ListRegistryPass;
use Staccato\Component\Listable\Field\TextField;
use Staccato\Component\Listable\Filter\TextFilter;
use Staccato\Component\Listable\ListStateProvider;
use Staccato\Component\Listable\Repository\ArrayRepository;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ListRegistryPassTest extends TestCase
{
    /**
     * @var MockObject|ContainerBuilder|null
     */
    private $builder;

    public function setUp(): void
    {
        $this->builder = new ContainerBuilder();
    }

    public function testProcess(): void
    {
        $this->builder->addDefinitions([
            'invalid' => new Definition('\stdClass'),
            'field' => (new Definition(TextField::class))->setShared(true),
            'filter' => (new Definition(TextFilter::class))->setShared(true),
            'state_provider' => (new Definition(ListStateProvider::class))->setShared(true),
            'repository' => (new Definition(ArrayRepository::class))->setShared(true),
        ]);

        $compiler = new ListRegistryPass();
        $compiler->process($this->builder);

        $this->assertFalse($this->builder->getDefinition('field')->isShared());
        $this->assertTrue($this->builder->getDefinition('field')->hasTag('staccato_listable.field'));

        $this->assertFalse($this->builder->getDefinition('filter')->isShared());
        $this->assertTrue($this->builder->getDefinition('filter')->hasTag('staccato_listable.filter'));

        $this->assertTrue($this->builder->getDefinition('repository')->isShared());
        $this->assertTrue($this->builder->getDefinition('repository')->hasTag('staccato_listable.repository'));

        $this->assertTrue($this->builder->getDefinition('state_provider')->isShared());
        $this->assertTrue($this->builder->getDefinition('state_provider')->hasTag('staccato_listable.state_provider'));
    }
}
