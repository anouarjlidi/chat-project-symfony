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
use App\Service\WebSiteView as WebSiteServiceView;

/**
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/request", name="api_request")
     * @param Request $request
     * @param WebSiteService $webSiteService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function request(Request $request, WebSiteService $webSiteService, WebSiteServiceView $webSiteServiceView)
    {
        $response = new Response();
        $site_id = $request->request->get('site_id');
        $em = $this->getDoctrine()->getManager();
        $webSiteRepo = $em->getRepository('App\Entity\WebSite');
        $webSite = $webSiteRepo->find($site_id);
        if ($webSite instanceof WebSite) {
            if ($webSite->getIsOnline() == false OR $webSite->getInstalled() == false) {
                $webSite = $this->install($request, $webSiteService, $webSite);
                $em->persist($webSite);
            }
            $em->flush();
        }
//        FOR DEV TO CHANGE HTML AND CSS IN PHPSTORM
        $webSite = $webSiteServiceView->configureDefaultValueWebSite($webSite, true);
//        FOR DEV TO CHANGE HTML AND CSS IN PHPSTORM
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
     * @param Request $request
     * @param WebSiteService $webSiteService
     * @param WebSite $webSite
     * @return WebSite
     */
    private function install(Request $request, WebSiteService $webSiteService, WebSite $webSite)
    {
        $source_code = $request->request->get('source_code');
        $url = $request->request->get('url');
        $jsScriptSrc = $request->request->get('thisScriptSrc');
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
            }
        }
        return $webSite;
    }

    /**
     * @Route("/get-chat-room", name="api_get_chat_room")
     * @param Request $request
     * @return Response
     */
    public function getChatRoom(Request $request)
    {
        $response = new Response();
        $user_id = $request->request->get('user_id');
        $temp_user_id = $request->request->get('temp_user_id');
        $chatType = $request->request->get('chat_type');
        if ($user_id == "null" OR $user_id == "") $user_id = null;
        if ($temp_user_id == "null" OR $temp_user_id == "") $temp_user_id = null;
        $return = [];
        if ($user_id == null AND $temp_user_id == null AND ($chatType == "admin" OR $chatType == "private_chat")) {
            $return = [
                'message' => 'user_id and temp_user_id are empty !'
            ];
        }
        if ($chatType != "admin" AND $chatType != "private_chat" AND $chatType != "private_group" AND $chatType != "public_group") {
            $return = [
                'message' => 'invalid chat type !'
            ];
        } else {
            $site_id = $request->request->get('site_id');
            $arrayUserUserIds = explode(",", $user_id);
            $arrayTempUserIds = explode(",", $temp_user_id);
            $em = $this->getDoctrine()->getManager();
            $webSite = $em->getRepository("App\Entity\WebSite")->find($site_id);
            if ($chatType == "admin" OR $chatType == "private_chat") {
                $foreignUserIds = [];
                $foreignUsers = $em->getRepository("App\Entity\ForeignUser")->getForeignUsersWithUserIdAndTempId($webSite->getId(), $arrayUserUserIds, $arrayTempUserIds);
                //update foreign user datas
                foreach ($foreignUsers as $foreignUser) {
                    array_push($foreignUserIds, $foreignUser->getId());
                    if ($chatType == "admin" AND sizeof($arrayUserUserIds) == 1 AND sizeof($arrayTempUserIds) == 1) {
                        if ($foreignUser->getUserId() == null) {
                            $foreignUser->setUserId($arrayUserUserIds[0]);
                            $em->persist($foreignUser);
                        }
                        if ($foreignUser->getTempId() == null) {
                            $foreignUser->setTempId($arrayTempUserIds[0]);
                            $em->persist($foreignUser);
                        }
                    }
                }
                //getChatroom
                $chatRoom = $em->getRepository("App\Entity\ChatRoom")->getChatRoomWithForeignUsers($chatType, $foreignUserIds, $webSite->getId());
                if (!$chatRoom instanceof ChatRoom) {
                    $chatRoom = new ChatRoom();
                    $chatRoom->setWebSite($webSite);
                    $chatRoom->setChatType($chatType);
                    if ($chatType == "admin") {
                        if (sizeof($foreignUsers) < 1) {
                            $foreignUser = new ForeignUser();
                            $foreignUser->setWebSite($webSite);
                            $foreignUser->setUserId($arrayUserUserIds[0]);
                            $foreignUser->setTempId($arrayTempUserIds[0]);
                            $chatRoom->addForeignUsers($foreignUser);
                            $em->persist($chatRoom);
                            $return = [
                                'chatRoom' => $chatRoom->getPublicObject()
                            ];
                        } elseif (sizeof($foreignUsers) == 1) {
                            $chatRoom->addForeignUsers($foreignUsers[0]);
                            $em->persist($chatRoom);
                            $return = [
                                'chatRoom' => $chatRoom->getPublicObject()
                            ];
                        } else {
                            $return = [
                                'message' => 'invalid number of user for an admin chatroom'
                            ];
                        }
                    } elseif ($chatType == "private_chat" AND sizeof($foreignUsers) < 2) {
//                        c'est la le truc compliqué il faudra probablement dev cette partie avec les groupes privés
                    }
                }
                $return = [
                    'chatRoom' => $chatRoom->getPublicObject()
                ];
            } else {
//                chatrooms for groups
            }
            $em->flush();
        }
        $response->setContent(json_encode($return));
        $response->headers->set('Content-Type', 'application/json');
//        if ($webSite->getInstalled() == true AND $webSite->getIsOnline() == true AND filter_var($webSite->getUrl(), FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED)) {
//            $response->headers->set('Access-Control-Allow-Origin', $webSite->getUrl());
//        } else {
        $response->headers->set('Access-Control-Allow-Origin', '*');
//        }
        return $response;
    }
}
