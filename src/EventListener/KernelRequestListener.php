<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class KernelRequestListener
{
    private $sam;
    private $timeCookieTempUser;
    private $user;

    /**
     * KernelRequestListener constructor.
     * @param TokenStorage $sam
     * @param int $timeCookieTempUser
     */
    public function __construct(TokenStorage $sam, int $timeCookieTempUser)
    {
        $this->sam = $sam;
        $this->timeCookieTempUser = $timeCookieTempUser;
        $this->user = null;
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
        if (!empty($this->sam->getToken())) {
            $this->user = $this->sam->getToken()->getUser();
        }
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $route = $event->getRequest()->get('_route');
        if (!($event->isMasterRequest() AND '_wdt' !== $route)) {
            return;
        }
        if (!$this->user instanceof User) {
            $this->createTempUser($event);
        }
    }

    private function createTempUser(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $cookie = $request->cookies;
        if (!$cookie->has('tempUserId')) {
            $response = $event->getResponse();
            $tempUserId = uniqid();
            $event->getRequest()->attributes->set('tempUserId', $tempUserId);
            $request = $event->getRequest();
            $tempUserId = $request->attributes->get('tempUserId');
            $cookie = new Cookie("tempUserId", $tempUserId, time() + $this->timeCookieTempUser);
            $response->headers->setCookie($cookie);
        }
    }
}