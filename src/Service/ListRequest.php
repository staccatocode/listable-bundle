<?php

/*
 * This file is part of staccato listable bundle
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Bundle\ListableBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class ListRequest extends \Staccato\Component\Listable\ListRequest
{
    public function __construct(RequestStack $requestStack)
    {
        parent::__construct($requestStack->getMasterRequest());
    }
}
