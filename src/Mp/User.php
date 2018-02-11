<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2018/1/31
 * Time: 下午4:33
 */

namespace Linyuee\Wechat\Mp;


use Linyuee\Wechat\Util\Helper;

class User extends Base
{
    const CREATE_LABEL_URL = 'https://api.weixin.qq.com/cgi-bin/tags/create';

    const USERS_OPENID_URL = 'https://api.weixin.qq.com/cgi-bin/user/get';

    const USER_INFO_URL = 'https://api.weixin.qq.com/cgi-bin/user/info';

    const AUTH_INFO_URL = 'https://api.weixin.qq.com/sns/oauth2/access_token';//授权信息

    public function getUserInfo($openid){
        $access_token = $this->getAccessToken();
        $url = self::USER_INFO_URL."?access_token=".$access_token.'&openid='.$openid.'&lang=zh_CN';
        $result = Helper::https_get($url);
        return json_decode($result,true);
    }

    public function getAllOpenid($next_openid = null){
        $access_token = $this->getAccessToken();
        $url = self::USERS_OPENID_URL."?access_token=".$access_token.(empty($next_openid)?'':'&next_openid='.$next_openid);
        $result = Helper::https_get($url);
        return json_decode($result,true);
    }

    public function setLabel($tag){
        $access_token = $this->getAccessToken();
        $url = self::CREATE_LABEL_URL."?access_token=".$access_token;
        //$res = \Linyuee\Util\Http\HttpHelper::post($url,$tag);
        $res = Helper::https_post($url,$tag);
        return json_decode($res,true);
    }

    public function authUserInfo($code){
        $get_token_url = self::AUTH_INFO_URL .'?appid='.$this->appid
            .'&secret='.$this->secret.'&code='.$code.'&grant_type=authorization_code';
        $res = \Linyuee\Http\HttpHelper::curl($get_token_url);
        $data = json_decode($res->getBody(),true);
        return $data;
    }
}