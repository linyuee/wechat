<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2018/1/29
 * Time: 下午3:30
 */

namespace Linyuee\Wechat\Mp;


class Auth extends Base
{
    const AUTH_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize';//授权



    public function userinfo($redirect_url,$state = null){
        $url = self::AUTH_URL.'?appid='.$this->appid
            .'&redirect_uri='.urlencode($redirect_url).'&response_type=code&scope=snsapi_userinfo&state='.$state.'#wechat_redirect';
        return header("location: ".$url);
    }

    public function base($redirect_url,$state = null){
        $url = self::AUTH_URL.'?appid='.$this->appid
            .'&redirect_uri='.urlencode($redirect_url).'&response_type=code&scope=snsapi_base&state='.$state.'#wechat_redirect';
        return header("location: ".$url);
    }


}