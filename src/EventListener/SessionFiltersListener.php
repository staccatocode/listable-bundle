<?php

/*
 * This file is part of staccato listable bundle
 *
 * (c) Krystian Karaś <dev@karashome.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Staccato\Bundle\ListableBundle\EventListener;

use Staccato\Component\Listable\ListRequestInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class SessionFiltersListener
{
    /**
     * @var ListRequestInterface
     */
    private $listRequest;

    /**
     * @var string
     */
    private $actionParam;

    public function __construct(ListRequestInterface $listRequest, array $config)
    {
        $this->listRequest = $listRequest;
        $this->actionParam = isset($config['builder']['default_values']['action_param']) ?
            $config['builder']['default_values']['action_param'] : 'st_list';
    }

    /**
     * Listen for listable filters action.
     * Add or remove filters from session.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if ($request->isMethod('post') && $request->request->has($this->actionParam)) {
            $data = $request->request->get($this->actionParam, array());
            $data = is_array($data) ? $data : array();

            if (array_key_exists('action', $data) && array_key_exists('name', $data)) {
                $name = $data['name'];

                if (is_string($name)) {
                    $action = (string) $data['action'];

                    switch ($action) {
                        case 'filters.apply':
                            $filters = $request->request->get($name, array());
                            $filters = is_array($filters) ? $filters : array();

                            $this->listRequest->storeFilters($name, $filters);
                            break;
                        case 'filters.clear':
                            $this->listRequest->storeFilters($name, array());
                            break;
                    }

                    if (in_array($action, array('filters.apply', 'filters.clear'))) {
                        $event->setResponse(new RedirectResponse($request->getUri()));
                    }
                }
            }
        }
    }
}
