<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2018/1/31
 * Time: 上午11:15
 */

namespace Linyuee\Wechat\Mp;


use Linyuee\Core\Http\Request;
use Linyuee\Wechat\Util\Helper;

class Menu
{
    const SET_MENU_URL = 'https://api.weixin.qq.com/cgi-bin/menu/create';
    const GET_MENU_URL = 'https://api.weixin.qq.com/cgi-bin/menu/get';
    const DELETE_MENU_URL = 'https://api.weixin.qq.com/cgi-bin/menu/delete';

    private $access_token;
    public function __construct(AccessToken $accessToken)
    {
        $this->access_token = $accessToken->getAccessToken();
    }



    public function set($menu){
        $menu = json_encode($menu,JSON_UNESCAPED_UNICODE);//不转义中文
        $request = new Request();
        $res = $request->setMethod('POST')
            ->setRequestUrl(self::SET_MENU_URL)
            ->setQuery([
                'access_token'=>$this->access_token
            ])->setParams($menu)
            ->send();
        return json_encode($res,true);
    }

    public function get(){
        $access_token = $this->access_token;
        $request = new Request();
        $res = $request
            ->setMethod('GET')
            ->setRequestUrl(self::GET_MENU_URL)
            ->setQuery([
                'access_token'=>$access_token
            ])->send();
        return json_decode($res->getBody(),true);
    }

    public function delete(){
        $access_token = $this->access_token;
        $url = self::DELETE_MENU_URL."?access_token=".$access_token."";
        $res = Helper::https_get($url);
        return json_decode($res,true);
    }
}