<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/12/6
 * Time: 下午3:06
 */

namespace Linyuee;



use Linyuee\Exception\ApiException;

class WechatPay
{
    private $appid;
    private $secret;
    protected $input;
    protected $key;
    const UNIFIED_ORDER_URL = "https://api.mch.weixin.qq.com/pay/unifiedorder";
    public function __construct($appid, $secret,$input,$key)
    {
        $this->appid = $appid;
        $this->secret = $secret;
        $this->input = $input;
        $this->key;
        if (!is_array($input)){
            throw new ApiException('数据格式错误');
        }
        if (!array_key_exists('mch_id',$input)){
            throw new ApiException('缺少统一参数mch_id');
        }
        if (!array_key_exists('body',$input)){
            throw new ApiException('缺少统一参数body');
        }
        if (!array_key_exists('out_trade_no',$input)){
            throw new ApiException('缺少统一参数out_trade_no');
        }
        if (!array_key_exists('total_fee',$input)){
            throw new ApiException('缺少统一参数total_fee');
        }
        if (!array_key_exists('notify_url',$input)){
            throw new ApiException('缺少统一参数notify_url');
        }
    }

    public function js_api_pay(){
        $input = $this->input;
        if (!array_key_exists('openid',$input)){
            throw new ApiException('缺少网页支付参数openid');
        }
        $data = array_merge($input,array(
            'appid'=>$this->appid,
            'secret'=>$this->secret,
            'nonce_str'=>Helper::createNonceStr(),
            'spbill_create_ip'=>$_SERVER['SERVER_ADDR'],
            'trade_type'=>'JSAPI',
        ));
        $sign = Helper::MakeSign($data,$this->key);
        $data = array_merge($data,array('sign'=>$sign));
        $data = Helper::ToXml($data);
        $res = Helper::postXmlCurl($data,self::UNIFIED_ORDER_URL);
        return $res;
    }


}