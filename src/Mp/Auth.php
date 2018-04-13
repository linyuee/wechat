<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2018/1/29
 * Time: 下午3:30
 */

namespace Linyuee\Wechat\Mp;


use Linyuee\Core\Http\Http;
use Linyuee\Core\Http\Request;
use Linyuee\Wechat\Util\Exception\ApiException;

class Auth
{
    const AUTH_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize';//授权

    const ACCESS_TOKEN_URL = 'https://api.weixin.qq.com/sns/oauth2/access_token';//授权信息

    const USER_INFO_URL = 'https://api.weixin.qq.com/sns/userinfo';

    const REFRESH_TOKEN_URL = 'https://api.weixin.qq.com/sns/oauth2/refresh_token';

    private $appid;

    private $secret;

    private $scopes;

    private static $scopes_in = ['snsapi_userinfo','snsapi_base'];

    public function __construct($appid,$secret)
    {

        $this->appid = $appid;
        $this->secret = $secret;
    }

    public function scopes($scopes = 'snsapi_userinfo'){
        if (!in_array($scopes,self::$scopes_in)){
            throw new ApiException('授权类型请填snsapi_userinfo或snsapi_base');
        }
        $this->scopes = $scopes;
        return $this;
    }

    public function redirect($redirect_url,$state = ''){
        $url = self::AUTH_URL.'?appid='.$this->appid
            .'&redirect_uri='.urlencode($redirect_url).'&response_type=code&scope='.$this->scopes.'&state='.$state.'#wechat_redirect';
        header("location: ".$url);die();
    }


    public function getAccessToken($code){
        $request = new Request();
        $res = $request
            ->setMethod('GET')
            ->setRequestUrl(self::ACCESS_TOKEN_URL)
            ->setQuery([
                'appid'=>$this->appid,
                'secret'=>$this->secret,
                'code'=>$code,
                'grant_type'=>'authorization_code'
            ])->send();

        $data = json_decode($res->getBody(),true);
        return $data;
    }


    public function getUserInfo($access_token,$openid){
        $request = new Request();
        $res = $request
            ->setMethod('GET')
            ->setRequestUrl(self::USER_INFO_URL)
            ->setQuery([
            'access_token'=>$access_token,
            'openid'=>$openid,
            'lang'=>'zh_CN'
        ])->send();

        $data = json_decode($res->getBody(),true);
        return $data;
    }

    public function refreshToken($refresh_token){
        $request = new Request();
        $res = $request
            ->setMethod('GET')
            ->setRequestUrl(self::REFRESH_TOKEN_URL)
            ->setQuery([
                'appid'=>$this->appid,
                'grant_type'=>'refresh_token',
                'refresh_token'=>$refresh_token
            ])->send();
        $data = json_decode($res->getBody(),true);
        return $data;
    }







}