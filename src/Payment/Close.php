<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/12/25
 * Time: 上午11:24
 */

namespace Linyuee\Payment;


use Linyuee\Util\Helper;

class Close
{
    const CLOSE_ORDER_URL = 'https://api.mch.weixin.qq.com/pay/closeorder';//关闭订单

    private $client;
    public function __construct($obj)
    {
        $this->client = $obj;
    }

    public function close_order($out_trade_no){
        $data = array(
            'appid'=>$this->client->appid,
            'mch_id'=>$this->client->mch_id,
            'nonce_str'=>Helper::createNonceStr(),
            'out_trade_no'=>$out_trade_no
        );
        $data['sign'] = Helper::MakeSign($data,$this->client->key);
        $data = Helper::ArrayToXml($data);
        $response = Helper::postXmlCurl($data,self::CLOSE_ORDER_URL);
        $res =  Helper::XmlToArray($response);
        return $res;
    }
}