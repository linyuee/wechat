<?php

namespace Linyuee;




use Linyuee\Exception\ApiException;

class Wechat extends WechatBase
{

    public function __construct($appid,$secret = '')
    {
        parent::__construct($appid,$secret);
    }

    public function auth($redirect_url,$state = null){
        return parent::userinfo_auth($redirect_url,$state);
    }

    public function baseAuth($redirect_url,$state = null)
    {
        return parent::base_auth($redirect_url,$state);
    }

    public function getUserinfoByCode($code)
    {
        $auth_info = $this->get_auth_info($code);

        $user_info = $this->get_userinfo($auth_info['access_token'],$auth_info['openid']);
        return $user_info;
    }
    //获取js_sdk签名
    public function getJsSdkSign($url){
        return $this->js_sdk_sign($url);
    }
    //设置公众号菜单
    public function setMenu($menu)
    {
        if (!is_array($menu)){
            throw new ApiException('参数必须为数组');
        }
        $menu = json_encode($menu,JSON_UNESCAPED_UNICODE);//不转义中文
        return parent::set_menu($menu);
    }

    //生成带参数二维码
    public function getQrcode($id)
    {
        return parent::get_qr_code($id);
    }


    public function getUsers()
    {
        return json_decode(parent::get_users(),true);
    }


    public function getUserInfo($openid){
        return parent::get_user_info($openid);
    }


}