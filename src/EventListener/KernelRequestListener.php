<?php

namespace App\EventListener;

use App\Entity\User;
use App\Entity\WebSite;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class KernelRequestListener
{
    private $sam;
    private $timeCookieTempUser;
    private $em;
    private $user;
    private $tempUserId;

    /**
     * KernelRequestListener constructor.
     * @param TokenStorage $sam
     * @param int $timeCookieTempUser
     * @param EntityManager $em
     */
    public function __construct(TokenStorage $sam, int $timeCookieTempUser, EntityManager $em)
    {
        $this->sam = $sam;
        $this->timeCookieTempUser = $timeCookieTempUser;
        $this->em = $em;
        $this->user = null;
        $this->tempUserId = null;
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

    /**
     * @param FilterResponseEvent $event
     * @throws \Doctrine\ORM\ORMException
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $route = $event->getRequest()->get('_route');
        if (!($event->isMasterRequest() AND '_wdt' !== $route)) {
            return;
        }
        if (!$this->user instanceof User) {
            $this->createTempUser($event);
            $this->createWebSite($event);
        }
    }

    /**
     * @param FilterResponseEvent $event
     */
    private function createTempUser(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $cookie = $request->cookies;
        if (!$cookie->has('tempUserId')) {
            $response = $event->getResponse();
            $this->tempUserId = uniqid();
            $event->getRequest()->attributes->set('tempUserId', $this->tempUserId);
            $request = $event->getRequest();
            $this->tempUserId = $request->attributes->get('tempUserId');
            $cookie = new Cookie("tempUserId", $this->tempUserId, time() + $this->timeCookieTempUser);
            $response->headers->setCookie($cookie);
        } else {
            $this->tempUserId = $cookie->get("tempUserId");
        }
    }

    /**
     * @param FilterResponseEvent $event
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function createWebSite(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $cookie = $request->cookies;
        if (!$cookie->has('tempSiteId')) {
            $webSiteRepo = $this->em->getRepository('App\Entity\WebSite');
            $webSites = $webSiteRepo->findBy(['adminTempUser' => $this->tempUserId]);
            if (empty($webSites)) {
                $webSite = new WebSite();
                $webSite->setHasAdminChat(true);
                $webSite->setHasPrivateChat(false);
                $webSite->setAdminTempUser($this->tempUserId);
                $this->em->persist($webSite);
                $this->em->flush();
                $cookie = new Cookie("tempSiteId", $webSite->getId(), time() + $this->timeCookieTempUser);
                $response = $event->getResponse();
                $response->headers->setCookie($cookie);
            } else {
                $cookie = new Cookie("tempSiteId", $webSites->first()->getAdminTempUser(), time() + $this->timeCookieTempUser);
                $response = $event->getResponse();
                $response->headers->setCookie($cookie);
            }
        }
    }
}