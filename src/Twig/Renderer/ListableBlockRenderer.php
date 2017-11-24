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
use Twig\Template;

abstract class ListableBlockRenderer
{
    /**
     * Twig context variables that will be
     * available in block during rendering.
     *
     * @var array
     */
    protected $context = array();

    /**
     * External configuration.
     *
     * @var ParameterBag
     */
    protected $config;

    public function __construct(array $config = array())
    {
        $this->config = new ParameterBag($config);
    }

    /**
     * Render block.
     *
     * @param Template $template
     * @param ListView $listView
     * @param array    $blocks
     *
     * @return string rendered block
     */
    public function render(Template $template, ListView $listView, array $blocks): string
    {
        $this->setListViewContext($listView);

        $renderer = function ($blockName, $baseTheme, $blockContext, $blocks) {
            $theme = $this->loadTemplate($baseTheme);

            if (!$theme->isTraitable()) {
                throw new Twig_Error_Runtime('Template "'.$baseTheme.'" cannot be used as a trait.');
            }

            $themeBlocks = $theme->getBlocks();

            $this->traits = array_merge($themeBlocks, $this->traits);
            $this->blocks = array_merge($this->traits, $this->blocks);

            return $this->displayBlock($blockName, $blockContext, $blocks);
        };

        return (string) $renderer->call(
            $template,
            $this->getBlockName(),
            $this->getBaseThemeTemplate(),
            $this->context,
            $blocks
        );
    }

    /**
     * Process block options.
     *
     * This function is good place to process block
     * options and add some variables to the block
     * context.
     *
     * @param array $options
     *
     * @return ListableBlockRenderer $this
     */
    public function processOptions(array $options): ListableBlockRenderer
    {
        // Nothing to do by default

        return $this;
    }

    /**
     * Return block name that will be rendered.
     *
     * @return string
     */
    abstract public function getBlockName(): string;

    /**
     * Return twig template name containing
     * default block name definition.
     *
     * @return string
     */
    abstract public function getBaseThemeTemplate(): string;

    /**
     * Set ListView context into this block.
     *
     * @param ListView $listView
     */
    protected function setListViewContext(ListView $listView): void
    {
        $this->context['list'] = $listView;
    }
}
