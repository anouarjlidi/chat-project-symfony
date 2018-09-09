<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
//        $response->headers->set('Access-Control-Allow-Origin', 'https://jsfiddle.net/');
        $response->headers->set('Access-Control-Allow-Methods', 'POST');
        return $response;
    }
}
