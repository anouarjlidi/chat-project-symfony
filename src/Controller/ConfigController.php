<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/{_locale}", defaults={"_locale": "%locale%"}, requirements={"_locale": "%available_locale%"})
 */
class ConfigController extends AbstractController
{
    /**
     * @Route("/demo", name="demo")
     */
    public function index()
    {
        return $this->render('config/demo/index.html.twig', [
        ]);
    }
}
