<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2018/3/22
 * Time: 下午4:42
 */

namespace Linyuee\Wechat\Mp;


use Linyuee\Cache\CacheTrait;
use Linyuee\Wechat\Util\Exception\ApiException;

class AccessToken
{
    use CacheTrait;

    const ACCESS_TOKEN_URL = 'https://api.weixin.qq.com/cgi-bin/token';

    private $appid;
    private $secret;
    public function __construct($app_id,$secret,\Doctrine\Common\Cache\Cache $cacheTrait = null)
    {
        $this->appid = $app_id;
        $this->secret = $secret;
    }

    public function getAccessToken(){
        //缓存access_token
        if ($this->cache && $data = $this->cache->fetch('access_token')) {
            \Log::info($data);
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

}