<?php

namespace App\Controller;

use App\Entity\WebSite;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\WebSite as WebSiteService;

/**
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/request", name="api_request")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function request(Request $request)
    {
        $response = new Response();
        $site_id = $request->request->get('site_id');
        $em = $this->getDoctrine()->getManager();
        $webSiteRepo = $em->getRepository('App\Entity\WebSite');
        $webSite = $webSiteRepo->find($site_id);
        $array = [
            'site' => $webSite->getPublicObject()
        ];

        $response->setContent(json_encode($array));
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        if ($webSite->getInstalled() == true AND $webSite->getIsOnline() == true) {
            $response->headers->set('Access-Control-Allow-Origin', $webSite->getUrl());
        }
        $response->headers->set('Access-Control-Allow-Methods', 'POST');
        return $response;
    }

    /**
     * @Route("/install", name="api_install")
     * @param Request $request
     * @param WebSiteService $webSiteService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function install(Request $request, WebSiteService $webSiteService)
    {
        $response = new Response();
        $site_id = $request->request->get('site_id');
        $source_code = $request->request->get('source_code');
        $url = $request->request->get('url');
        $jsScriptSrc = $request->request->get('thisScriptSrc');
        $em = $this->getDoctrine()->getManager();
        $webSiteRepo = $em->getRepository('App\Entity\WebSite');
        $webSite = $webSiteRepo->find($site_id);
        if ($webSite instanceof WebSite) {
            if ($webSite->getIsOnline() != true OR $webSite->getInstalled() == false) {
                $webSite->setInstalled(true);
                $isOnline = $webSiteService->isOnline($url, $jsScriptSrc);
                $webSite->setIsOnline(false);
                if ($isOnline != false) {
                    $webSite->setIsOnline(true);
                    $urlParse = parse_url($url);
                    if (($urlParse["scheme"] == 'http' OR $urlParse["scheme"] == 'https') AND isset($urlParse["host"])) {
                        $url = $urlParse["scheme"] . "://" . $urlParse["host"] . "/";
                    }
                    $webSite->setUrl($url);
                    $source_code = $isOnline;
                }
                $webSite->setSourceCode($source_code);
                $em->persist($webSite);
                $em->flush();
            }
        }
        $array = [
            'installed' => $webSite->getInstalled()
        ];

        $response->setContent(json_encode($array));
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'POST');
        return $response;
    }
}
