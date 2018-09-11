<?php

namespace App\EventListener;

use App\Entity\User;
use App\Entity\WebSite;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class KernelRequestListener
{
    private $tokenStorage;
    private $timeCookieTempUser;
    private $em;
    private $session;
    private $user;
    private $tempUserId;
    private $tempSiteId;

    /**
     * KernelRequestListener constructor.
     * @param TokenStorage $tokenStorage
     * @param int $timeCookieTempUser
     * @param EntityManager $em
     * @param Session $session
     */
    public function __construct(TokenStorage $tokenStorage, int $timeCookieTempUser, EntityManager $em, Session $session)
    {
        $this->tokenStorage = $tokenStorage;
        $this->timeCookieTempUser = $timeCookieTempUser;
        $this->em = $em;
        $this->session = $session;
        $this->user = null;
        $this->tempUserId = null;
        $this->tempSiteId = null;
    }

    /**
     * @param GetResponseEvent $event
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $route = $event->getRequest()->get('_route');
        if (!($event->isMasterRequest() AND '_wdt' !== $route)) {
            return;
        }
        if (!empty($this->tokenStorage->getToken())) {
            $this->user = $this->tokenStorage->getToken()->getUser();
        }
        if (!$this->user instanceof User AND strpos($route, "api_") !== 0) {
            $this->createTempUser($event);
            $this->createWebSite($event);
        }
        $request = $event->getRequest();
        if ($request->isMethod('POST')) {
            $this->addFlashMessagesAfterRedirect($request);
        }
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $route = $event->getRequest()->get('_route');
        if (!($event->isMasterRequest() AND '_wdt' !== $route)) {
            return;
        }
        if (!$this->user instanceof User AND strpos($route, "api_") !== 0) {
            $this->createCookie($event);
        }
    }

    /**
     * @param FilterResponseEvent $event
     */
    private function createCookie(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $request = $event->getRequest();
        $cookie = $request->cookies;
        if (!$cookie->has('tempUserId')) {
            $cookie = new Cookie("tempUserId", $this->tempUserId, time() + $this->timeCookieTempUser);
            $response->headers->setCookie($cookie);
        }
        if ($this->tempSiteId != null) {
            $cookie = new Cookie("tempSiteId", $this->tempSiteId, time() + $this->timeCookieTempUser);
            $response->headers->setCookie($cookie);
        }
    }

    /**
     * @param GetResponseEvent $event
     */
    private function createTempUser(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $cookie = $request->cookies;
        if (!$cookie->has('tempUserId')) {
            $this->tempUserId = uniqid();
            $request->attributes->set('tempUserId', $this->tempUserId);
        } else {
            $this->tempUserId = $cookie->get("tempUserId");
        }
    }

    /**
     * @param GetResponseEvent $event
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function createWebSite(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $cookie = $request->cookies;
        if (!$cookie->has('tempSiteId')) {
            $webSiteRepo = $this->em->getRepository('App\Entity\WebSite');
            $webSites = $webSiteRepo->findBy(['adminTempUser' => $this->tempUserId]);
            if (empty($webSites)) {
                $webSite = new WebSite();
                $webSite->setHasAdminChat(false);
                $webSite->setHasPrivateChat(null);
                $webSite->setAdminTempUser($this->tempUserId);
                $this->em->persist($webSite);
                $this->em->flush();
                $this->tempSiteId = $webSite->getId();
                $request->attributes->set('tempSiteId', $this->tempSiteId);
            } else {
                $this->tempSiteId = $webSites[0]->getId();
                $request->attributes->set('tempSiteId', $this->tempSiteId);
            }
        }
    }

    /**
     * @param Request $request
     */
    private function addFlashMessagesAfterRedirect(Request $request)
    {
        $messagesAfterRedirect = $request->request->get('messagesAfterRedirect');
        if ($messagesAfterRedirect != null AND is_array($messagesAfterRedirect) AND sizeof($messagesAfterRedirect) > 0) {
            foreach ($messagesAfterRedirect as $message) {
                $this->session->getFlashBag()->add(
                    $message["class"],
                    [
                        "title" => $message["title"],
                        "message" => $message["message"],
                    ]
                );
            }
        }
    }
}