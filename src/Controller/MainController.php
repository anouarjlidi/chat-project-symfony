<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/{_locale}", defaults={"_locale": "%locale%"}, requirements={"_locale": "%available_locale%"})
 */
class MainController extends AbstractController
{
    /**
     * @Route("", name="home")
     */
    public function index()
    {
        return $this->render('main/index.html.twig', [
        ]);
    }

    /**
     * @Route("/feature", name="feature")
     */
    public function feature()
    {
        return $this->render('main/feature.html.twig', [
        ]);
    }

    /**
     * @Route("/pricing", name="pricing")
     */
    public function pricing()
    {
        return $this->render('main/pricing.html.twig', [
        ]);
    }

    /**
     * @Route("/demo", name="demo")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function demo(Request $request)
    {
        $tempSiteId = $request->attributes->get('tempSiteId');
        if (empty($tempSiteId)) {
            $tempSiteId = $request->cookies->get('tempSiteId');
        }
        return $this->render('main/demo/index.html.twig', [
            'tempSiteId' => $tempSiteId
        ]);
    }

    /**
     * @Route("/try-it-now", name="try_it_now")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function tryItNow(Request $request)
    {
        $tempSiteId = $request->attributes->get('tempSiteId');
        if (empty($tempSiteId)) {
            $tempSiteId = $request->cookies->get('tempSiteId');
        }
        return $this->render('main/demo/index.html.twig', [
            'tempSiteId' => $tempSiteId
        ]);
    }
}
