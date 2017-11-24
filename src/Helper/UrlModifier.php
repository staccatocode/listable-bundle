<?php

/*
 * This file is part of staccato listable bundle
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Bundle\ListableBundle\Helper;

use Symfony\Component\HttpFoundation\Request;

class UrlModifier
{
    /**
     * @var Request
     */
    private $request;

    public function __construct()
    {
        $this->request = Request::createFromGlobals();
    }

    /**
     * Get current request URI and modify
     * query string parameters.
     *
     * @param array $mergeParams
     * @param array $removeParams
     *
     * @return string
     */
    public function modifyQueryString(array $mergeParams, array $removeParams): string
    {
        $params = $this->request->query->all();
        $params = array_merge($params, $mergeParams);

        foreach ($removeParams as $param) {
            unset($params[$param]);
        }

        $url = $this->request->getSchemeAndHttpHost().
               $this->request->getBaseUrl().
               $this->request->getPathInfo();

        $qs = Request::normalizeQueryString(http_build_query($params, '', '&'));
        $qs = '' !== $qs && null !== $qs ? ('?'.$qs) : $qs;

        return $url.$qs;
    }
}
