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

class TableBlockRenderer extends ListableBlockRenderer
{
    /**
     * {@inheritdoc}
     */
    public function getBlockName(): string
    {
        return 'staccato_listable_table';
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseThemeTemplate(): string
    {
        return 'StaccatoListableBundle::table.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function processOptions(array $options): ListableBlockRenderer
    {
        $this->context = array_merge($this->context, $options);

        $options = new ParameterBag($options);

        $this->processOtherOptions($options);
        $this->processColumnsOptions($options);

        return $this;
    }

    private function processOtherOptions(ParameterBag $options)
    {
        $this->context['class'] = $options->get('class', 'table table-responsive-sm');
    }

    private function processColumnsOptions(ParameterBag $options)
    {
        $this->context['columns'] = $options->get('columns', array());
        $this->context['columns'] = is_array($this->context['columns']) ? $this->context['columns'] : array();

        $columnDefaults = array(
            'title' => '',
            'sort' => '',
            'tooltip' => '',
            'property' => '',
        );

        foreach ($this->context['columns'] as $k => $column) {
            $this->context['columns'][$k] = array_merge($columnDefaults, $column);
        }
    }
}
