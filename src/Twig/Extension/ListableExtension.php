<?php

/*
 * This file is part of staccato listable bundle
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Bundle\ListableBundle\Twig\Extension;

use Staccato\Bundle\ListableBundle\Twig\Node\ListableNode;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ListableExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('listable_box', null, array('node_class' => ListableNode::class), array('is_safe' => array('html'))),
            new TwigFunction('listable_table', null, array('node_class' => ListableNode::class), array('is_safe' => array('html'))),
            new TwigFunction('listable_pagination', null, array('node_class' => ListableNode::class), array('is_safe' => array('html'))),
            new TwigFunction('listable_filters', null, array('node_class' => ListableNode::class), array('is_safe' => array('html'))),
        );
    }
}
