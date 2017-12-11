<?php

namespace Linyuee;




use Linyuee\Exception\ApiException;

class Wechat extends WechatBase
{

    public function __construct($appid,$secret)
    {
        parent::__construct($appid,$secret);
    }

    public function auth($redirect_url,$state = null){
        return parent::userinfo_auth($redirect_url,$state);
    }

    public function base_auth($redirect_url,$state = null)
    {
        return parent::base_auth($redirect_url,$state);
    }

    public function get_userinfo($code)
    {
        $auth_info = $this->get_auth_info($code);
        $user_info = $this->get_user_info($auth_info['access_token'],$auth_info['openid']);
        return $user_info;
    }
    //获取js_sdk签名
    public function get_js_sdk_sign($url){
        return $this->js_sdk_sign($url);
    }
    //设置公众号菜单
    public function set_menu($menu)
    {
        if (!is_array($menu)){
            throw new ApiException('参数必须为数组');
        }
        $menu = json_encode($menu,JSON_UNESCAPED_UNICODE);//不转义中文
        return parent::set_menu($menu);
    }
    //支付
    public function pay($input,$key){
        return new WechatPay($this->appid,$this->secret,$input,$key);
    }

    //生成带参数二维码
    public function get_qrcode($id)
    {
        return parent::get_qr_code($id);
    }

    public function index($data, $token)
    {
        return parent::index($data, $token);
    }


}