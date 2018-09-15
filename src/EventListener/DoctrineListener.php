<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\RequestStack;

class DoctrineListener
{
    private $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getMasterRequest();
    }

    /**
     * @param LifecycleEventArgs $event
     * @throws \Doctrine\ORM\ORMException
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
    }

    /**
     * @param User $user
     * @param LifecycleEventArgs $event
     * @throws \Doctrine\ORM\ORMException
     */
    private function addTempWebSite(User $user, LifecycleEventArgs $event)
    {
        $tempUserId = $this->request->cookies->get('tempUserId');
        $em = $event->getEntityManager();
        $webSitesRepo = $em->getRepository("App\Entity\WebSite");
        $webSites = $webSitesRepo->findBy(["adminTempUser" => $tempUserId]);
        foreach ($webSites as $webSite) {
            $user->addWebSite($webSite);
            $user->setLocale($this->request->getLocale());
            if ($webSite->getAdminUser() == null) {
                $webSite->setAdminUser($user);
            }
        }
        $em->persist($user);
        $em->flush();
    }
}