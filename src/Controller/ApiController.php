<?php

namespace App\Controller;

use App\Entity\ChatRoom;
use App\Entity\ForeignUser;
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
     * @Route("/get-chat-room", name="api_get_chat_room")
     * @param Request $request
     * @return Response
     */
    public function getChatRoom(Request $request)
    {
        $response = new Response();
        $site_id = $request->request->get('site_id');
        $user_id = $request->request->get('user_id');
        $temp_user_id = $request->request->get('temp_user_id');
        $chatType = $request->request->get('chat_type');
        $arrayUserIds = explode(",", $user_id);
        $em = $this->getDoctrine()->getManager();
        $webSite = $em->getRepository("App\Entity\WebSite")->find($site_id);
        $array = [];
        if (($user_id == "" OR $user_id == null) AND ($temp_user_id == "" OR $temp_user_id == null)) {
//            retourner un message pour dire qu'il faut identifier le user
//            dans le cas ou les 2 sont définis on prend le user id et si son temp user id
        } else {
            $foreignUserRepo = $em->getRepository("App\Entity\ForeignUser");
            $foreignUsers = $foreignUserRepo->findBy([
                "webSite" => $site_id,
                "userId" => $arrayUserIds
            ]);
            $arrayForeignUserIds = [];
            foreach ($foreignUsers as $foreignUser) {
                if ($foreignUser instanceof ForeignUser) array_push($arrayForeignUserIds, $foreignUser->getId());
            }
            $arrayForeignUserUserIds = [];
            foreach ($foreignUsers as $foreignUser) {
                if ($foreignUser instanceof ForeignUser) array_push($arrayForeignUserUserIds, $foreignUser->getUserId());
            }
            $chatRoom = $em->getRepository("App\Entity\ChatRoom")->getChatRoomWithUsers($chatType, $arrayForeignUserIds);
            if (!$chatRoom instanceof ChatRoom) {
                $chatRoom = new ChatRoom();
                $chatRoom->setChatType($chatType);
                $chatRoom->setWebSite($webSite);
                foreach ($arrayUserIds as $user_id) {
                    $foreignUserPersist = null;
                    if (in_array($user_id, $arrayForeignUserUserIds)) {
                        foreach ($foreignUsers as $foreignUser) {
                            if ($foreignUser instanceof ForeignUser AND $foreignUser->getUserId() == $user_id) $foreignUserPersist = $foreignUser;
                            break;
                        }
                    } else {
                        $foreignUserPersist = new ForeignUser();
                        $foreignUserPersist->setUserId($user_id);
                        $foreignUserPersist->setWebSite($webSite);
                    }
                    $chatRoom->addForeignUsers($foreignUserPersist);
                }
                $em->persist($chatRoom);
            }
            if (sizeof($chatRoom->getForeignUsers()) != sizeof($arrayUserIds)) {
                //optimiser avec un compteur de foreign users à la place de getForeignUsers()
                $array = [
                    'chatRoom' => null
                ];
            } else {
                $em->flush();
                $array = [
                    'chatRoom' => $chatRoom->getPublicObject()
                ];
            }
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
