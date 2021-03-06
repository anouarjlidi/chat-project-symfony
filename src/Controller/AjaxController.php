<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @Route("/{_locale}/ajax", defaults={"_locale": "%locale%"}, requirements={"_locale": "%available_locale%"})
 */
class AjaxController extends AbstractController
{
    private $redirect;
    private $messages;
    private $messagesAfterRedirect;
    private $timeRedirection;
    private $stopRedirect;

    public function __construct(RequestStack $requestStack)
    {
        $request = $requestStack->getMasterRequest();
        $this->redirect = $request->request->get("redirect");
        $this->messages = [];
        $this->messagesAfterRedirect = [];
        $this->stopRedirect = false;
    }

    /**
     * @Route("/access-dashboard", name="ajax_access_dashboard")
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function accessDashboard(Request $request, TokenStorageInterface $tokenStorage, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();
        $webSiteRepo = $em->getRepository("App\Entity\WebSite");
        $user = $tokenStorage->getToken()->getUser();
        if (!($user instanceof User)) {
            $tempSiteId = $request->cookies->get('tempSiteId');
            $webSites = $webSiteRepo->findBy(["id" => $tempSiteId]);
        } else {
            $webSites = $user->getWebSites();
        }
        if (sizeof($webSites) == 0) {
            //error reset les cookie
        }
        $nbWebSiteInstalled = 0;
        foreach ($webSites as $webSite) {
            if ($webSite->getInstalled() == true) {
                $nbWebSiteInstalled++;
            }
        }
        if ($nbWebSiteInstalled > 0) {
            array_push($this->messagesAfterRedirect, [
                "title" => $translator->trans('word.success'),
                "message" => $translator->trans('dashboard.configure'),
                "class" => "success",
            ]);
        } elseif (sizeof($webSites) > $nbWebSiteInstalled AND $nbWebSiteInstalled == 0) {
            array_push($this->messages, [
                "title" => $translator->trans('word.error'),
                "message" => $translator->trans('demo.developper.not_installed'),
                "class" => "warning",
            ]);
            $this->stopRedirect = true;
        } else {
            array_push($this->messages, [
                "title" => $translator->trans('word.error'),
                "message" => $translator->trans('sentence.error.contact'),
                "class" => "danger",
            ]);
            $this->stopRedirect = true;
        }
        return $this->sendDatas();
    }

    private function sendDatas()
    {
        $array = [];
        $array["redirect"] = $this->redirect;
        $array["messages"] = $this->messages;
        $array["messagesAfterRedirect"] = $this->messagesAfterRedirect;
        $array["timeRedirection"] = $this->timeRedirection;
        $array["stopRedirect"] = $this->stopRedirect;
        return $this->json($array);
    }
}
