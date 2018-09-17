<?php

namespace App\Controller;

use App\Entity\ChatRoom;
use App\Entity\ForeignUserWebSite;
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
//        if ($webSite->getInstalled() == true AND $webSite->getIsOnline() == true AND filter_var($webSite->getUrl(), FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED)) {
//            $response->headers->set('Access-Control-Allow-Origin', $webSite->getUrl());
//        } else {
        $response->headers->set('Access-Control-Allow-Origin', '*');
//        }
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
                    $source_code = $isOnline;
                }
                $webSite->setUrl($url);
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
        return $response;
    }

    /**
     * @Route("/get-admin-chat-room", name="api_get_admin_chat_room")
     * @param Request $request
     * @return Response
     */
    public function getAdminChatRoom(Request $request)
    {
        $response = new Response();
        $site_id = $request->request->get('site_id');
        $user_id = $request->request->get('user_id');
        $em = $this->getDoctrine()->getManager();
        $webSite = $em->getRepository("App\Entity\WebSite")->find($site_id);
        if ($user_id == "null") {
            $adminChatRoom = new ChatRoom();
            $adminChatRoom->setChatType("admin");
            $adminChatRoom->setWebSite($webSite);
            $em->persist($adminChatRoom);
            $em->flush();
            $array = [
                'adminChatRoom' => $adminChatRoom->getPublicObject()
            ];
        } else {
            //get the chat room in bdd if not exist, create it et flush it
            $adminChatRoom = $em->getRepository("App\Entity\ChatRoom")->findOneBy([
                "userWebSiteForAdminUserId" => $user_id,
                "webSite" => $site_id,
                "chatType" => "admin"
            ]);
            if (!$adminChatRoom instanceof ChatRoom) {
                $adminChatRoom = new ChatRoom();
                $adminChatRoom->setChatType("admin");
                $adminChatRoom->setWebSite($webSite);
                $foreignUserWebSite = new ForeignUserWebSite();
                $foreignUserWebSite->setUserId($user_id);
                $adminChatRoom->setUserWebSiteForAdmin($foreignUserWebSite);
                $adminChatRoom->setUserWebSiteForAdminUserId($foreignUserWebSite->getUserId());
                $em->persist($adminChatRoom);
                $em->flush();
            }
            $array = [
                'adminChatRoom' => $adminChatRoom->getPublicObject()
            ];
        }
        $response->setContent(json_encode($array));
        $response->headers->set('Content-Type', 'application/json');
//        if ($webSite->getInstalled() == true AND $webSite->getIsOnline() == true AND filter_var($webSite->getUrl(), FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED)) {
//            $response->headers->set('Access-Control-Allow-Origin', $webSite->getUrl());
//        } else {
        $response->headers->set('Access-Control-Allow-Origin', '*');
//        }
        return $response;
    }
}
