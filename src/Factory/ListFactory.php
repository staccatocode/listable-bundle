<?php

/*
 * This file is part of staccato listable bundle
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Bundle\ListableBundle\Factory;

use Staccato\Component\Listable\ListInterface;
use Staccato\Component\Listable\ListRegistryInterface;

class ListFactory extends \Staccato\Component\Listable\ListFactory
{
    /** @var array */
    private $defaultOptions;

    public function __construct(ListRegistryInterface $registry, array $config)
    {
        $this->defaultOptions = $config['builder']['default_options'] ?? [];

        parent::__construct($registry);
    }

    public function create(string $listType, array $options = []): ListInterface
    {
        $options += $this->defaultOptions;

        return parent::create($listType, $options);
    }
}
