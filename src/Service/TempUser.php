<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class TempUser
{
    private $requestStack;
    private $tempUserId;
    private $tempSiteId;

    /**
     * TempUser constructor.
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->tempUserId = null;
        $this->tempSiteId = null;
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        $request = $this->requestStack->getCurrentRequest();
        $cookie = $request->cookies;
        return [
            "tempUserId" => $cookie->get('tempUserId'),
            "tempSiteId" => $cookie->get('tempSiteId')
        ];
    }
}