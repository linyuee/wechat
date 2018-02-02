<?php

namespace Linyuee;





use Linyuee\Exception\ApiException;
use Linyuee\Mp\Auth;
use Linyuee\Mp\Menu;
use Linyuee\Mp\User;

class Wechat
{

    private $appid;
    private $secret;
    private $cache;
    public function __construct($appid, $secret, \Doctrine\Common\Cache\Cache $cache = null)
    {
        $this->appid = $appid;
        $this->secret = $secret;
        $this->cache = $cache;
    }

    public function auth(){
        return new Auth($this);
    }

    public function menu(){
        return new Menu($this);
    }

    public function user(){
        return new User($this);
    }

    /**
     * @return mixed
     */
    public function getAppid()
    {
        return $this->appid;
    }

    public function getSecret(){
        return $this->secret;
    }

    public function getCache(){
        return $this->cache;
    }


}