<?php

namespace App\Service;

class WebSiteView
{
    public function configureDefaultValueWebSite(\App\Entity\WebSite $webSite, bool $force = false)
    {
        if ($webSite->getCssChat() == "" OR $force = true) {
            $webSite->setCssChat($this->getDefaultCssChat());
        }
        if ($webSite->getTemplateAdminChat() == "" OR $force = true) {
            $webSite->setTemplateAdminChat($this->getDefaultTemplateAdminChat());
        }
        if ($webSite->getTemplateMainChatWindow() == "" OR $force = true) {
            $webSite->setTemplateMainChatWindow($this->getDefaultTemplateMainChatWindow());
        }
        if ($webSite->getTemplatePrivateChat() == "" OR $force = true) {
            $webSite->setTemplatePrivateChat($this->getDefaultTemplatePrivateChat());
        }
        return $webSite;
    }

    public function getDefaultCssChat()
    {
        ob_start();
        ?>
        <style>
            #id-web-site .chatWindow, #id-web-site .mainChatWindow {
                float: right;
                width: 284px;
                height: 334px;
                background-color: white;
                margin-left: 15px;
                position: fixed;
                bottom: -302px;
                border-radius: 5px 5px 0px 0px;
            }

            #id-web-site div.header {
                border-radius: 5px 5px 0px 0px;
                background-color: #3578e5;
                cursor: pointer;
            }

            #id-web-site span.header {
                padding: 7px;
                display: block;
                width: 239px;
            }

            #id-web-site .chatRooms, #id-web-site .messages {
                overflow-y: auto;
            }

            #id-web-site .chatRooms {
                height: 302px;
            }

            #id-web-site .messages {
                height: 280px;
            }

            #id-web-site span.closeWindow {
                float: right;
                color: white;
                font-weight: bold;
                font-size: 33px;
                padding: 0px 10px 0px 10px;
                position: absolute;
                top: -2px;
                right: 0px;
            }

            #id-web-site .sendMessage input {
                width: 98%;
            }
        </style>
        <?php
        $data = ob_get_contents();
        ob_end_clean();
        return $data;
    }

    public function getDefaultTemplateAdminChat()
    {
        ob_start(); ?>
        <div class="chatWindow" data-chat-room-id="%chat-room-id%">
            <div class="header">
                <span class="header">Admin</span>
            </div>
            <div class="messages">
            </div>
            <div class="sendMessage">
                <form method="POST">
                    <input name="message" type="text" placeholder="type your message here">
                    <input name="chat-id" type="hidden" value="%chat-room-id%">
                </form>
            </div>
        </div>
        <?php
        $data = ob_get_contents();
        ob_end_clean();
        return $data;
    }

    public function getDefaultTemplateMainChatWindow()
    {
        ob_start(); ?>
        <div style="right:15px;" class="mainChatWindow">
            <div class="header">
                <span class="header">Chat Rooms</span>
            </div>
            <div class="chatRooms">
            </div>
        </div>
        <?php
        $data = ob_get_contents();
        ob_end_clean();
        return $data;
    }

    public function getDefaultTemplatePrivateChat()
    {
        ob_start(); ?>
        <div class="chatWindow" data-chat-room-id="%chat-room-id%">
            <div class="header">
                <span class="header">Private chat</span>
                <span class="closeWindow" aria-hidden="true">&times;</span>
            </div>
            <div class="messages">
            </div>
            <div class="sendMessage">
                <form method="POST">
                    <input name="message" type="text" placeholder="type your message here">
                    <input name="chat-id" type="hidden" value="%chat-room-id%">
                </form>
            </div>
        </div>
        <?php
        $data = ob_get_contents();
        ob_end_clean();
        return $data;
    }
}