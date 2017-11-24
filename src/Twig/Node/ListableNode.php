<?php

/*
 * This file is part of staccato listable bundle
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Bundle\ListableBundle\Twig\Node;

use Staccato\Bundle\ListableBundle\Twig\Renderer;
use Twig\Compiler;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\FunctionExpression;

class ListableNode extends FunctionExpression
{
    public function compile(Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        $arguments = $this->getNode('arguments');
        $arguments = iterator_to_array($arguments);

        if (isset($arguments[0])) {
            $name = $this->getAttribute('name');
            $name = substr($name, strrpos($name, '_') + 1);
            $rendererClass = Renderer::class.'\\'.ucfirst($name).'BlockRenderer';

            $compiler->raw('$this->env->getRuntime(\''.$rendererClass.'\')'."\n");
            $compiler->indent(2);

            $compiler->write('->processOptions(');

            if (isset($arguments[1]) && $arguments[1] instanceof ArrayExpression) {
                $compiler->subcompile($arguments[1]);
            } else {
                $compiler->raw('array()');
            }

            $compiler->raw(')'."\n");

            $compiler->write('->render($this, ');
            $compiler->subcompile($arguments[0]);
            $compiler->raw(', $blocks)'."\n");
        } else {
            $compiler->raw('\'\'');
        }
    }
}
