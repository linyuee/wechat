<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2018/2/10
 * Time: 下午4:38
 */

namespace Linyuee\Wechat\Mp;


use Linyuee\Wechat\Util\Helper;

class Qrcode extends Base
{
    const QRCODE_URL = 'https://api.weixin.qq.com/cgi-bin/qrcode/create';
    public function get_qr_code($id)
    {
        $access_token = $this->getAccessToken();
        $qrcode = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": '.$id.'}}}';
        $url = self::QRCODE_URL."?access_token=$access_token";
        $result = Helper::https_post($url,$qrcode);
        $this->refreshToken($result,__FUNCTION__,$id);
        $jsoninfo = json_decode($result, true);
        $ticket = $jsoninfo["ticket"];
        $get_url="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($ticket);
        return $get_url;
    }
}