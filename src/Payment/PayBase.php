<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2018/1/23
 * Time: 下午4:21
 */

namespace Linyuee\Wechat\Payment;


use Linyuee\Wechat\PayClient;
use Linyuee\Wechat\Util\Helper;

abstract class PayBase
{
    protected $client;

    public function __construct(PayClient $obj)
    {
        $this->client = $obj;
    }

    public function handler($data,$url){
        $sign = Helper::MakeSign($data,$this->client->key);
        $sendData = array_merge($data,array('sign'=>$sign));
        $sendData = Helper::ArrayToXml($sendData);
        $response = Helper::postXmlCurl($sendData,$url);
        $res = Helper::XmlToArray($response);
        return $res;
    }
}