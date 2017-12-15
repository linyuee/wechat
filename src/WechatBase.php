<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/12/5
 * Time: 下午4:31
 */

namespace Linyuee;

use Linyuee\Cache\CacheTrait;
use Linyuee\Exception\ApiException;
use Linyuee\Util\Helper;


abstract class WechatBase
{
    use CacheTrait;

    protected $appid;
    protected $secret;
    const AUTH_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize';//授权

    const AUTH_INFO_URL = 'https://api.weixin.qq.com/sns/oauth2/access_token';//授权信息

    const REFRESH_TOKEN_URL = 'https://api.weixin.qq.com/sns/oauth2/refresh_token';

    const AUTH_USERINFO_URL = 'https://api.weixin.qq.com/sns/userinfo';//获取授权用户信息

    const QRCODE_URL = 'https://api.weixin.qq.com/cgi-bin/qrcode/create';

    const MENU_URL = 'https://api.weixin.qq.com/cgi-bin/menu/create';

    const USER_INFO_URL = 'https://api.weixin.qq.com/cgi-bin/user/info';

    const ACCESS_TOKEN_URL = 'https://api.weixin.qq.com/cgi-bin/token';


    protected function __construct($appid,$secret)
    {
        $this->appid = $appid;
        $this->secret = $secret;

    }


    protected function userinfo_auth($redirect_url,$state = null){
        $url = self::AUTH_URL.'?appid='.$this->appid
            .'&redirect_uri='.urlencode($redirect_url).'&response_type=code&scope=snsapi_userinfo&state='.$state.'#wechat_redirect';
        return header("location: ".$url);
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
        $get_token_url = self::AUTH_INFO_URL .'?appid='.$this->appid
            .'&secret='.$this->secret.'&code='.$code.'&grant_type=authorization_code';
        $res = \Linyuee\Http\HttpHelper::curl($get_token_url);
        $data = json_decode($res->getBody(),true);
        if(empty($data['refresh_token'])){
            return false;
        }
        $refresh_token = $data['refresh_token'];
        return $this->refresh_access_token($refresh_token);
    }

    protected function refresh_access_token($refresh_token){
        $refresh_token_url = self::REFRESH_TOKEN_URL."?appid=".$this->appid
            ."&grant_type=refresh_token&refresh_token=".$refresh_token."";
        $res = \Linyuee\Http\HttpHelper::curl($refresh_token_url);
        return json_decode($res->getBody(),true);
    }

    /*
     * 获取用户信息
     * 参数：access_token,openid
     * 返回
     */

    protected function get_userinfo($access_token,$openid){
        $get_user_info_url = self::AUTH_USERINFO_URL.'?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $res = \Linyuee\Http\HttpHelper::curl($get_user_info_url);
        return json_decode($res->getBody(),true);
    }


    protected function js_sdk_sign($url){
        $jsapiTicket = $this->get_js_api_ticket();
        $timestamp = time();
        $nonceStr = Helper::createNonceStr();
        $string = "jsapi_ticket=".$jsapiTicket."&noncestr=".$nonceStr.'&timestamp='.$timestamp."&url=".$url."";

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
        $this->handler($res,__FUNCTION__);
        $result = json_decode($res, true); //接受一个 JSON 格式的字符串并且把它转换为 PHP 变量
        $jsapi_ticket = $result['ticket'];
        return $jsapi_ticket;
    }

    public  function get_access_token()
    {
        //缓存access_token
        if ($this->cache && $data = $this->cache->fetch('access_token')) {
            //var_dump($data);
            return $data;
        }
        $token_access_url = self::ACCESS_TOKEN_URL."?grant_type=client_credential&appid=".$this->appid."&secret=".$this->secret;
        $res = file_get_contents($token_access_url); //获取文件内容或获取网络请求的内容
        $result = json_decode($res, true); //接受一个 JSON 格式的字符串并且把它转换为 PHP 变量
        $access_token = $result['access_token'];
        if ($this->cache){
            $this->cache->save('access_token', $access_token, 7200);
        }
        return $access_token;
    }

    protected function get_js_api_ticket()
    {
        $access_token = $this->get_access_token();
        $jsapi_ticket_url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=".$access_token."";
        $res = file_get_contents($jsapi_ticket_url); //获取文件内容或获取网络请求的内容
        $this->handler($res,__FUNCTION__);
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
        $post_url = self::MENU_URL."?access_token=".$access_token."";
        $ch = curl_init($post_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$menu);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($menu))
        );
        $response = curl_exec($ch);
        $this->handler($response,__FUNCTION__,$menu);
        $response = json_decode($response, true);
        return $response;
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

    /**
     * 生成带参数的公众号二维码
     * @param $id
     * @return string
     */
    public function get_qr_code($id)
    {
        $access_token = $this->get_access_token();
        $qrcode = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": '.$id.'}}}';
        $url = self::QRCODE_URL."?access_token=$access_token";
        $result = Helper::https_post($url,$qrcode);
        $this->handler($result,__FUNCTION__,$id);
        $jsoninfo = json_decode($result, true);
        $ticket = $jsoninfo["ticket"];
        $get_url="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($ticket);
        return $get_url;
    }

    public function get_users(){
        $access_token = $this->get_access_token();
        $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$access_token;
        $result = Helper::https_post($url);
        return $this->handler($result,__FUNCTION__);
    }


    protected function get_user_info($openid){
        $access_token = $this->get_access_token();
        $get_user_info_url = self::USER_INFO_URL.'?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $result = Helper::https_post($get_user_info_url);
        return $this->handler($result,__FUNCTION__,$openid);

    }


    protected function handler($result,$function,$params = null){
        $response = json_decode($result,true);
        if (isset($response['errcode'])){
            if ($response['errcode'] == 40001 && $this->cache){
                $this->cache->delete('access_token');
                return $this->$function($params);
            }
        }
        return $result;
    }

}