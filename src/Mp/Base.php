<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2018/1/29
 * Time: 下午3:32
 */

namespace Linyuee\Wechat\Mp;


use Linyuee\Cache\CacheTrait;
use Linyuee\Exception\ApiException;
use Linyuee\Wechat\MpClient;

class Base
{
    use CacheTrait;
    protected $appid;
    protected $secret;
    const ACCESS_TOKEN_URL = 'https://api.weixin.qq.com/cgi-bin/token';

    public function __construct(MpClient $client)
    {
        $this->appid = $client->getAppid();
        $this->secret = $client->getSecret();
        if (!empty($client->getCache())){
            $this->setCache($client->getCache());
        }
    }


    public function getAccessToken(){
        //缓存access_token

        if ($this->cache && $data = $this->cache->fetch('access_token')) {
            return $data;
        }
        $token_access_url = self::ACCESS_TOKEN_URL."?grant_type=client_credential&appid=".$this->appid."&secret=".$this->secret;
        $res = file_get_contents($token_access_url); //获取文件内容或获取网络请求的内容
        $result = json_decode($res, true); //接受一个 JSON 格式的字符串并且把它转换为 PHP 变量
        if (!isset($result['access_token'])&& isset($result['errcode'])){
            throw new ApiException($result['errmsg']);
        }
        $access_token = $result['access_token'];
        if ($this->cache){
            $this->cache->save('access_token', $access_token, 7200);
        }
        return $access_token;
    }
    //access_token过期则删除缓存重新请求
    public function refreshToken($result,$function,$params = null){
        $response = json_decode($result,true);
        if (isset($response['errcode'])){
            if ($response['errcode'] == 40001 && $this->cache){
                $this->cache->delete('access_token');
                return $this->$function($params);
            }
        }
        return $result;
    }

    public function refresh($result,$function,$params = null){
        return function () use ($result,$function,$params){
            callback($function);

        };
    }

}