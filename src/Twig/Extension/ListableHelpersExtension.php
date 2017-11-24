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

use Staccato\Bundle\ListableBundle\Helper\UrlModifier;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ListableHelpersExtension extends AbstractExtension
{
    /**
     * @var UrlModifier
     */
    private $urlModifier;

    public function __construct(urlModifier $urlModifier)
    {
        $this->urlModifier = $urlModifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('listable_url', array($this, 'getUrl')),
        );
    }

    /**
     * Get current URL.
     *
     * @param array $appendParams query params to append
     * @param array $removeParams query params to remove
     *
     * @return string
     */
    public function getUrl(array $appendParams = array(), array $removeParams = array()): string
    {
        return (string) $this->urlModifier->modifyQueryString($appendParams, $removeParams);
    }
}
