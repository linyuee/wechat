<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2018/1/29
 * Time: 下午3:30
 */

namespace Linyuee\Mp;


class Auth extends Base
{
    const AUTH_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize';//授权
    const AUTH_INFO_URL = 'https://api.weixin.qq.com/sns/oauth2/access_token';//授权信息


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

    protected function get_userinfo($code){
        $get_token_url = self::AUTH_INFO_URL .'?appid='.$this->appid
            .'&secret='.$this->secret.'&code='.$code.'&grant_type=authorization_code';
        $res = \Linyuee\Http\HttpHelper::curl($get_token_url);
        $data = json_decode($res->getBody(),true);
//        $get_user_info_url = self::AUTH_USERINFO_URL.'?access_token='.$data['access_token'].'&openid='.$data['openid'].'&lang=zh_CN';
//        $res = \Linyuee\Http\HttpHelper::curl($get_user_info_url);
//        return json_decode($res->getBody(),true);
        return $data;
    }
}