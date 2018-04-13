<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2018/4/12
 * Time: 下午2:27
 */

namespace Linyuee\Wechat\Mp;


use Linyuee\Wechat\Util\Helper;

class JsSdk
{
    private $access_token;
    private $appid;
    public function __construct(AccessToken $accessToken,$app_id)
    {
        $this->access_token = $accessToken;
        $this->appid = $app_id;
    }

    public function getSign($url){
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

    protected function get_js_api_ticket()
    {
        $access_token = $this->access_token;
        $jsapi_ticket_url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=".$access_token."";
        $res = file_get_contents($jsapi_ticket_url); //获取文件内容或获取网络请求的内容
        $result = json_decode($res, true); //接受一个 JSON 格式的字符串并且把它转换为 PHP 变量
        $jsapi_ticket = $result['ticket'];
        return $jsapi_ticket;
    }
}