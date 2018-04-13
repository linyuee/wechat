<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2018/2/10
 * Time: 下午4:38
 */

namespace Linyuee\Wechat\Mp;


use Linyuee\Wechat\Util\Helper;

class Qrcode
{
    const QRCODE_URL = 'https://api.weixin.qq.com/cgi-bin/qrcode/create';

    private $access_token;

    public function __construct(AccessToken $accessToken)
    {
        $this->access_token = $accessToken;
    }

    public function get($id)
    {
        $access_token = $this->access_token;
        $qrcode = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": ' . $id . '}}}';
        $url = self::QRCODE_URL . "?access_token=$access_token";
        $result = Helper::https_post($url, $qrcode);
        $jsoninfo = json_decode($result, true);
        $ticket = $jsoninfo["ticket"];
        $get_url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . urlencode($ticket);
        return $get_url;
    }
}