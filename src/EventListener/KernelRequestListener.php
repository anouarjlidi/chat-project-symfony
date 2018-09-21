<?php

namespace App\EventListener;

use App\Entity\User;
use App\Entity\WebSite;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class KernelRequestListener
{
    private $tokenStorage;
    private $timeCookieTempUser;
    private $em;
    private $session;
    private $router;
    private $user;
    private $tempUserId;
    private $tempSiteId;

    /**
     * KernelRequestListener constructor.
     * @param TokenStorage $tokenStorage
     * @param int $timeCookieTempUser
     * @param EntityManager $em
     * @param Session $session
     * @param RouterInterface $router
     */
    public function __construct(TokenStorage $tokenStorage, int $timeCookieTempUser, EntityManager $em, Session $session, RouterInterface $router)
    {
        $this->tokenStorage = $tokenStorage;
        $this->timeCookieTempUser = $timeCookieTempUser;
        $this->em = $em;
        $this->session = $session;
        $this->router = $router;
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
        if ($route != null) $this->redirect($event, $route);
    }

    /**
     * @param FilterResponseEvent $event
     * @param string $route
     */
    private function redirect(FilterResponseEvent $event, string $route)
    {
        if (strpos($route, "ajax_") === 0 OR strpos($route, "api_")) {
            return;
        }
//        $redirectToDashBoard = ["demo", "try_it_now"];
//        $redirectToDemo = "dashboard";
//        if (in_array($route, $redirectToDashBoard)) {
//            $repoWebSite = $this->em->getRepository("App\Entity\WebSite");
//            if (!$this->user instanceof User) {
//                $arrayDashBoard = ["installed" => true, "adminTempUser" => $this->tempUserId];
//                $webSiteInstalled = $repoWebSite->findBy($arrayDashBoard);
//            } else {
//                $webSiteInstalled = $repoWebSite->getInstalledWebSitesForUser($this->user);
//            }
//            if (sizeof($webSiteInstalled) > 0) {
//                $event->setResponse(new RedirectResponse($this->router->generate('dashboard')));
//            }
//        }
//        if (strpos($route, $redirectToDemo) !== false) {
//            $repoWebSite = $this->em->getRepository("App\Entity\WebSite");
//            if (!$this->user instanceof User) {
//                $arrayDashBoard = ["installed" => true, "adminTempUser" => $this->tempUserId];
//                $webSiteInstalled = $repoWebSite->findBy($arrayDashBoard);
//                if (sizeof($webSiteInstalled) == 0) {
//                    $event->setResponse(new RedirectResponse($this->router->generate('demo')));
//                }
//            }
//        }
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