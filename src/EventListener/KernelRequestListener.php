<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class KernelRequestListener
{
    private $sam;

    /**
     * KernelRequestListener constructor.
     * @param TokenStorage $sam
     */
    public function __construct(TokenStorage $sam)
    {
        $this->sam = $sam;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $route = $event->getRequest()->get('_route');
        if (!($event->isMasterRequest() AND '_wdt' !== $route)) {
            return;
        }
//        $user = $this->sam->getToken()->getUser();
    }
}