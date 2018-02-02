<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2018/1/31
 * Time: 下午4:33
 */

namespace Linyuee\Mp;


use Linyuee\Util\Helper;

class User extends Base
{
    const CREATE_LABEL_URL = 'https://api.weixin.qq.com/cgi-bin/tags/create';

    public function getOne($openid){

    }

    public function getAll(){

    }

    public function setLabel($tag){
        $access_token = $this->getAccessToken();
        $url = self::CREATE_LABEL_URL."?access_token=".$access_token;
        //$res = \Linyuee\Util\Http\HttpHelper::post($url,$tag);
        $res = Helper::https_post($url,$tag);
        return json_decode($res,true);
    }
}