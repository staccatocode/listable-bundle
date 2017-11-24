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

class PaginationBlockRenderer extends ListableBlockRenderer
{
    /**
     * {@inheritdoc}
     */
    public function getBlockName(): string
    {
        return 'staccato_listable_pagination';
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseThemeTemplate(): string
    {
        return 'StaccatoListableBundle::pagination.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function processOptions(array $options): ListableBlockRenderer
    {
        $this->context = array_merge($this->context, $options);

        $options = new ParameterBag($options);

        $this->context['sides'] = $options->getInt('sides', 2);
        $this->context['class'] = $options->get('class', 'pagination pagination-sm justify-content-center');

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function setListViewContext(ListView $listView): void
    {
        parent::setListViewContext($listView);

        $pagination = $listView->vars['pagination'];
        $sides = isset($this->context['sides']) ? $this->context['sides'] : 0;
        $from = max(0, $pagination['page'] - $sides);
        $to = min($pagination['pages'] - 1, $pagination['page'] + $sides);

        $this->context['pagination'] = $pagination;
        $this->context['pages'] = range($from, $to);
    }
}
