<?php

namespace App\EventListener;

use App\Entity\User;
use App\Entity\WebSite;
use App\Service\WebSiteView as WebSiteViewService;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\RequestStack;

class DoctrineListener
{
    private $request;
    private $webSiteViewService;

    public function __construct(RequestStack $requestStack, WebSiteViewService $webSiteViewService)
    {
        $this->request = $requestStack->getMasterRequest();
        $this->webSiteViewService = $webSiteViewService;
    }

    /**
     * @param LifecycleEventArgs $event
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function postPersist(LifecycleEventArgs $event)
    {
        $route = $this->request->get('_route');
        if ('_wdt' == $route) {
            return;
        }
        $entity = $event->getObject();
        if ($entity instanceof User) {
            $this->addTempWebSite($entity, $event);
        }
        if ($entity instanceof WebSite) {
            $this->setDefaultValueWebSite($entity, $event);
        }
    }

    /**
     * @param WebSite $webSite
     * @param LifecycleEventArgs $event
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function setDefaultValueWebSite(WebSite $webSite, LifecycleEventArgs $event)
    {
        $em = $event->getEntityManager();
        $webSite = $this->webSiteViewService->configureDefaultValueWebSite($webSite);
        $em->persist($webSite);
        $em->flush();
    }

    /**
     * @param User $user
     * @param LifecycleEventArgs $event
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function addTempWebSite(User $user, LifecycleEventArgs $event)
    {
        $tempUserId = $this->request->cookies->get('tempUserId');
        $em = $event->getEntityManager();
        $webSitesRepo = $em->getRepository("App\Entity\WebSite");
        $webSites = $webSitesRepo->findBy(["adminTempUser" => $tempUserId]);
        foreach ($webSites as $webSite) {
            if ($webSite instanceof WebSite) {
                $user->addWebSite($webSite);
                $user->setLocale($this->request->getLocale());
                if ($webSite->getAdminUser() == null) {
                    $webSite->setAdminUser($user);
                }
            }
        }
        $em->persist($user);
        $em->flush();
    }
}