<?php

namespace App\Service;

class WebSiteView
{
    public function configureDefaultValueWebSite(\App\Entity\WebSite $webSite)
    {
        if ($webSite->getCssAdminChat() == "") {
            $webSite->setCssAdminChat($this->getDefaultCssAdminChat());
        }
        if ($webSite->getTemplateAdminChat() == "") {
            $webSite->setTemplateAdminChat($this->getDefaultTemplateAdminChat());
        }
        return $webSite;
    }

    public function getDefaultCssAdminChat()
    {
        ob_start();
        ?>
        <style>
            #admin_chat {
                background-color: red;
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
        <div id="admin_chat">
            default template
        </div>
        <?php
        $data = ob_get_contents();
        ob_end_clean();
        return $data;
    }
}