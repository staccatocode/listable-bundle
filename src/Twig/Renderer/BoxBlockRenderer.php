<?php

/*
 * This file is part of staccato listable bundle
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Bundle\ListableBundle\Twig\Renderer;

use Symfony\Component\HttpFoundation\ParameterBag;

class BoxBlockRenderer extends ListableBlockRenderer
{
    /**
     * {@inheritdoc}
     */
    public function getBlockName(): string
    {
        return 'staccato_listable_box';
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseThemeTemplate(): string
    {
        return 'StaccatoListableBundle::box.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function processOptions(array $options): ListableBlockRenderer
    {
        $this->context = array_merge($this->context, $options);

        $options = new ParameterBag($options);

        $this->processOtherOptions($options);

        return $this;
    }

    private function processOtherOptions(ParameterBag $options)
    {
        $this->context['class'] = $options->get('class', '');
    }
}
