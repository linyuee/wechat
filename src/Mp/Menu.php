<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2018/1/31
 * Time: 上午11:15
 */

namespace Linyuee\Wechat\Mp;


use Linyuee\Wechat\Util\Helper;

class Menu extends Base
{
    const SET_MENU_URL = 'https://api.weixin.qq.com/cgi-bin/menu/create';
    const GET_MENU_URL = 'https://api.weixin.qq.com/cgi-bin/menu/get';
    const DELETE_MENU_URL = 'https://api.weixin.qq.com/cgi-bin/menu/delete';
    public function set($menu)
    {
        $menu = json_encode($menu,JSON_UNESCAPED_UNICODE);//不转义中文
        $access_token = $this->getAccessToken();
        $url = self::SET_MENU_URL."?access_token=".$access_token."";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$menu);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($menu))
        );
        $response = curl_exec($ch);
        $this->refreshToken($response,__FUNCTION__,$menu);
        $response = json_decode($response, true);
        return $response;
    }

    public function get(){
        $access_token = $this->getAccessToken();
        $url = self::GET_MENU_URL."?access_token=".$access_token."";
        //$res = \Linyuee\Util\Http\HttpHelper::get($url);
        $res = Helper::https_get($url);
        return json_decode($res,true);
    }

    public function delete(){
        $access_token = $this->getAccessToken();
        $url = self::DELETE_MENU_URL."?access_token=".$access_token."";
        $res = Helper::https_get($url);
        return json_decode($res,true);
    }
}