<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/12/5
 * Time: 下午4:31
 */

namespace Linyuee;


abstract class WechatBase
{
    protected $appid;
    protected $secret;
    const AUTH_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize';//授权
    const ACCESS_TOKEN_URL = 'https://api.weixin.qq.com/sns/oauth2/access_token';//授权信息
    const REFRESH_TOKEN_URL = 'https://api.weixin.qq.com/sns/oauth2/refresh_token';
    const USERINFO_URL = 'https://api.weixin.qq.com/sns/userinfo';//获取用户信息
    protected function __construct($appid,$secret)
    {
        $this->appid = $appid;
        $this->secret = $secret;
    }

    protected function userinfo_auth($redirect_url,$state = null){
        $url = self::AUTH_URL.'?appid='.$this->appid
            .'&redirect_uri='.urlencode($redirect_url).'&response_type=code&scope=snsapi_userinfo&state='.$state.'#wechat_redirect';
        header("location: ".$url);
    }

    protected function base_auth($redirect_url,$state = null){
        $url = self::AUTH_URL.'?appid='.$this->appid
            .'&redirect_uri='.urlencode($redirect_url).'&response_type=code&scope=snsapi_base&state='.$state.'#wechat_redirect';
        return header("location: ".$url);
    }
    /*
     * 获取授权信息
     * 参数：code
     * 返回：array(
     *    'access_token' => '4_QQ2NOj6OJQ0Pm16pEWRKXuWn41RuhH1pZdIeNFEzMcLMdx76XDAe9m56AS90MvXR8qC7wYP8nDsUdXvo-HZATQ',
          'expires_in' => 7200,
          'refresh_token' => '4_se_0OcjbiS6mafFZdbXWqDNHC7xkThZUSh8qdQdu_7cDAq3duNYjQZ9TaZLORXa2ew4O4bjtptH9d8y6f7DiZw',
          'openid' => 'ogzUjwMevWmSnr__y9aOMVCVvU1g',
          'scope' => 'snsapi_userinfo',)
     */
    protected function get_auth_info($code){
        $get_token_url = self::ACCESS_TOKEN_URL .'?appid='.$this->appid
            .'&secret='.$this->secret.'&code='.$code.'&grant_type=authorization_code';
        $res = \Pingqu\Http\HttpHelper::curl($get_token_url);
        $data = json_decode($res->getBody(),true);
        if(empty($data['refresh_token'])){
            return false;
        }
        return $data;
    }

    protected function refresh_access_token($refresh_token){
        $refresh_token_url = self::REFRESH_TOKEN_URL."?appid=".$this->appid
            ."&grant_type=refresh_token&refresh_token=".$refresh_token."";
        $res = \Pingqu\Http\HttpHelper::curl($refresh_token_url);
        return json_decode($res->getBody(),true);
    }

    /*
     * 获取用户信息
     * 参数：access_token,openid
     * 返回 array(
     *   'openid' => 'ogzUjwMevWmSnr__y9aOMVCVvU1g',
      'nickname' => '小楼听风雨',
      'sex' => 1,
      'language' => 'zh_CN',
      'city' => '汕头',
      'province' => '广东',
      'country' => '中国',
      'headimgurl' => 'http://wx.qlogo.cn/mmopen/vi_32/DYAIOgq83erw3XAK4U4ubvcnDMZANDvibs8VDEGGvJQMQ0NCbMxUxH6Fkac7DrAEjRYz6xLz5NNoz8yLiaibBoxmQ/0',
      'privilege' =>
      array (
        ),
    )
     */

    protected function get_user_info($access_token,$openid){
        $get_user_info_url = self::USERINFO_URL.'?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $res = \Pingqu\Http\HttpHelper::curl($get_user_info_url);
        return json_decode($res->getBody(),true);
    }


    protected function js_sdk_sign($url){
        $jsapiTicket = $this->get_js_api_ticket();
        \Log::info($jsapiTicket);
        $timestamp = time();
        $nonceStr = Helper::createNonceStr();
        $string = "jsapi_ticket=".$jsapiTicket."&noncestr=".$nonceStr.'&timestamp='.$timestamp."&url=".$url."";
        //echo $string1;
        $signature = sha1($string);
        $signPackage = array(
            "appId"     => $this->appid,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature
        );
        return  $signPackage;
    }

    protected function get_api_ticket(){
        $access_token = $this->get_access_token();
        $jsapi_ticket_url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=".$access_token."";
        $res = file_get_contents($jsapi_ticket_url); //获取文件内容或获取网络请求的内容
        $result = json_decode($res, true); //接受一个 JSON 格式的字符串并且把它转换为 PHP 变量
        $jsapi_ticket = $result['ticket'];
        return $jsapi_ticket;
    }

    public  function get_access_token()
    {
        $token_access_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->secret;
        $res = file_get_contents($token_access_url); //获取文件内容或获取网络请求的内容
        $result = json_decode($res, true); //接受一个 JSON 格式的字符串并且把它转换为 PHP 变量
        $access_token = $result['access_token'];
        return $access_token;
    }

    protected function get_js_api_ticket()
    {
        $access_token = $this->get_access_token();
        $jsapi_ticket_url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=".$access_token."";
        $res = file_get_contents($jsapi_ticket_url); //获取文件内容或获取网络请求的内容
        $result = json_decode($res, true); //接受一个 JSON 格式的字符串并且把它转换为 PHP 变量
        $jsapi_ticket = $result['ticket'];
        return $jsapi_ticket;
    }

    /**
     * 卡券签名
     * @return mixed
     */
    protected function card_sign(){
        $data['apiTicket'] = $this->get_api_ticket();
        $data['timestamp'] = time();
        $data['nonce_str'] = Helper::createNonceStr();
        $arr = $data;
        sort($arr,SORT_STRING);
        $string = implode("",$arr);
        $data['signature'] = sha1($string);
        unset($data['apiTicket']);
        return $data;
    }

    /**
     * 自定义公众号菜单
     * @param $menu
     * @return int
     */
    protected function set_menu($menu)
    {
        $access_token = $this->get_access_token();
        $post_url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token."";
        $ch = curl_init($post_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$menu   );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($menu))
        );
        $response = curl_exec($ch);
        $response = json_decode($response, true);
        return $response['errcode'] == 0 ? 1 : 0;
    }


    /*
     * 从微信服务器上获取媒体文件
     * 参数：media_id
     * 返回：image
     */
    protected function get_media($media_id){
        $access_token = $this->get_access_token();
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$access_token.'&media_id='.$media_id;
        $content = file_get_contents($url);
        return $content;
    }


}