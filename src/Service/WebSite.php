<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class WebSite
{
    /**
     * @param string $url
     * @param string $jsScript
     * @return bool
     */
    public function isOnline(string $url, string $jsScript)
    {
        $source_code = file_get_contents($url);
        if (strpos($source_code, $jsScript) !== false) {
            return $source_code;
        }
        return false;
    }
}