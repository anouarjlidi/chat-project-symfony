<?php

namespace App\Controller;

use App\Entity\WebSite;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\WebSite as WebSiteService;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/{_locale}", defaults={"_locale": "%locale%"}, requirements={"_locale": "%available_locale%"})
 */
class ConfigController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     * @param WebSiteService $webSiteService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dashboard(WebSiteService $webSiteService)
    {
        $webSites = $webSiteService->getWebSitesOfCurrentUser();
        return $this->render('config/content/main.html.twig', [
            "webSites" => $webSites
        ]);
    }

    /**
     * @Route("/dashboard/website/{id}", name="dashboard_website")
     * @param WebSite $website
     * @param WebSiteService $webSiteService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function website(WebSite $website, WebSiteService $webSiteService)
    {
        $webSites = $webSiteService->getWebSitesOfCurrentUser();
        if (!in_array($website, $webSites)) {
            return $this->redirectToRoute('dashboard');
        }
        return $this->render('config/content/website.html.twig', [
            "webSites" => $webSites,
            "currentWebSite" => $website
        ]);
    }
}
