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

use Staccato\Component\Listable\ListView;
use Symfony\Component\HttpFoundation\ParameterBag;

class FiltersBlockRenderer extends ListableBlockRenderer
{
    /**
     * {@inheritdoc}
     */
    public function processOptions(array $options): ListableBlockRenderer
    {
        $this->context = array_merge($this->context, $options);

        $options = new ParameterBag($options);

        $this->processFieldsOptions($options);
        $this->processOtherOptions($options);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockName(): string
    {
        return 'staccato_listable_filters';
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseThemeTemplate(): string
    {
        return 'StaccatoListableBundle::filters.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    protected function setListViewContext(ListView $listView): void
    {
        parent::setListViewContext($listView);

        $this->context['method'] = isset($this->context['method']) ?
            $this->context['method'] : ('get' == $listView->vars['filter_source'] ? 'get' : 'post');
        $this->context['method'] = strtolower($this->context['method']);
        $this->context['prefix']['action'] = $listView->vars['params']['action'];
        $this->context['prefix']['field'] = $listView->vars['name'];

        foreach ($this->context['fields'] as $name => $value) {
            if (isset($listView->vars['options']['filters'][$name])) {
                $this->context['fields'][$name]['value'] = $listView->vars['options']['filters'][$name];
            }
        }
    }

    private function processFieldsOptions(ParameterBag $options)
    {
        $this->context['fields'] = $options->get('fields', array());
        $this->context['fields'] = is_array($this->context['fields']) ? $this->context['fields'] : array();

        $defaultTypeAttributes = $this->getDefaultTypeAttributes();

        foreach ($this->context['fields'] as $name => $field) {
            $fieldDefaults = $defaultTypeAttributes['_default'];
            $fieldType = isset($field['type']) ? $field['type'] : $fieldDefaults['type'];

            if (isset($defaultTypeAttributes[$fieldType])) {
                $fieldDefaults = array_merge($fieldDefaults, $defaultTypeAttributes[$fieldType]);
            }

            $this->context['fields'][$name] = array_merge($fieldDefaults, $field);
        }
    }

    private function processOtherOptions(ParameterBag $options)
    {
        $this->context['columns'] = $options->getInt('columns', 3);
        $this->context['columns'] = max(1, min(12, $this->context['columns']));
        $this->context['class'] = $options->get('class', 'staccato-filters');
        $this->context['method'] = $options->get('method', null);
    }

    private function getDefaultTypeAttributes(): array
    {
        return array(
            '_default' => array(
                'type' => 'text',
                'value' => '',
                'label' => '',
                'class' => 'form-control',
                'placeholder' => '',
            ),
            'select' => array(
                'type' => 'select',
                'options' => array(),
                'multiple' => false,
            ),
            'checkbox' => array(
                'type' => 'checkbox',
                'class' => 'form-check-input',
                'options' => array(),
            ),
            'radio' => array(
                'type' => 'radio',
                'class' => 'form-check-input',
                'options' => array(),
            ),
        );
    }
}
