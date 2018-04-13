<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2018/1/31
 * Time: 下午4:33
 */

namespace Linyuee\Wechat\Mp;



use Linyuee\Wechat\Util\Helper;

class User
{
    const CREATE_LABEL_URL = 'https://api.weixin.qq.com/cgi-bin/tags/create';

    const USERS_OPENID_URL = 'https://api.weixin.qq.com/cgi-bin/user/get';

    const USER_INFO_URL = 'https://api.weixin.qq.com/cgi-bin/user/info';


    private $access_token;

    public function __construct(AccessToken $accessToken)
    {
        $this->access_token = $accessToken->getAccessToken();
    }

    public function getUserInfo($openid){
        $access_token = $this->access_token;
        $url = self::USER_INFO_URL."?access_token=".$access_token.'&openid='.$openid.'&lang=zh_CN';
        $result = Helper::https_get($url);
        return json_decode($result,true);
    }

    public function getAllOpenid($next_openid = null){
        $access_token = $this->access_token;
        $url = self::USERS_OPENID_URL."?access_token=".$access_token.(empty($next_openid)?'':'&next_openid='.$next_openid);
        $result = Helper::https_get($url);
        return json_decode($result,true);
    }

    public function setLabel($tag){
        $access_token = $this->access_token;
        $url = self::CREATE_LABEL_URL."?access_token=".$access_token;
        //$res = \Linyuee\Util\Http\HttpHelper::post($url,$tag);
        $res = Helper::https_post($url,$tag);
        return json_decode($res,true);
    }


}