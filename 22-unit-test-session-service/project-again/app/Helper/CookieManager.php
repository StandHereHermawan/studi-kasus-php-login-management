<?php

namespace AriefKarditya\LocalDomainPhp\Helper;

class CookieManager
{
    private $name;
    private $value;
    private $expire = 0;
    private $path = '';
    private $domain = '';
    private $secure = false;
    private $httponly = false;

    public function __construct()
    {
    }

    public function getCookie()
    {
        return setcookie($this->name, $this->value, $this->expire, $this->path, $this->domain, $this->secure, $this->httponly);
    }

    public function setCookie($name, $value, $expire = 0, $path = '', $domain = '', $secure = false, $httponly = false)
    {
        $this->name = $name;
        $this->value = $value;
        $this->expire = $expire;
        $this->path = $path;
        $this->domain = $domain;
        $this->secure = $secure;
        $this->httponly = $httponly;
    }
}
