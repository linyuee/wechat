<?php

namespace Linyuee\Wechat;

use Linyuee\Wechat\Mp\AccessToken;
use Linyuee\Wechat\Mp\Auth;
use Linyuee\Wechat\Mp\JsSdk;
use Linyuee\Wechat\Mp\Menu;
use Linyuee\Wechat\Mp\Message;
use Linyuee\Wechat\Mp\Qrcode;
use Linyuee\Wechat\Mp\User;

class MpClient
{

    private $appid;
    private $secret;
    private $accessToken;
    public function __construct($appid, $secret, \Doctrine\Common\Cache\Cache $cache = null)
    {
        $this->appid = $appid;
        $this->secret = $secret;
        $this->accessToken = new AccessToken($appid,$secret,$cache);
    }

    public function auth(){
        return new Auth($this->appid,$this->secret);
    }

    public function menu(){
        return new Menu($this->accessToken);
    }

    public function user(){
        return new User($this->accessToken);
    }

    public function qrcode(){
        return new Qrcode($this->accessToken);
    }

    public function message(){
        return new Message($this->accessToken);
    }

    public function jsSdk(){
        return new JsSdk($this->accessToken,$this->appid);
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

    public function getAccessToken(){

    }


}