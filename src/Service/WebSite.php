<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use App\Entity\WebSite as WebSiteEntity;

class WebSite
{
    private $authorizationChecker;
    private $tokenStorage;
    private $requestStack;
    private $entityManager;

    public function __construct(AuthorizationChecker $authorizationChecker, TokenStorage $tokenStorage, RequestStack $requestStack, EntityManager $entityManager)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $url
     * @param string $jsScript
     * @return bool
     */
    public function isOnline(string $url, string $jsScript)
    {
        $source_code = file_get_contents($url);
        if (strpos($source_code, $jsScript) !== false) {
            return $source_code;
        }
        return false;
    }

    public function getWebSitesOfCurrentUser()
    {
        $webSiteRepo = $this->entityManager->getRepository("App\Entity\WebSite");
        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED ') OR $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY  ')) {
            $webSites = $this->tokenStorage->getToken()->getUser()->getWebSites();
        } else {
            $webSites = $webSiteRepo->findBy(["adminTempUser" => $this->requestStack->getMasterRequest()->cookies->get("tempUserId")]);
        }
        return $webSites;
    }

    public function refresh(WebSiteEntity $webSite)
    {
        try {
            $source_code = file_get_contents($webSite->getUrl());
            $webSite->setSourceCode($source_code);
        } catch (\Exception $e) {
        }
        return $webSite;
    }
}