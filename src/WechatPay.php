<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/12/6
 * Time: 下午3:06
 */

namespace Linyuee;



use Linyuee\Exception\ApiException;
use Linyuee\Util\Helper;

class WechatPay
{
    private $appid;
    private $secret;
    protected $data;
    protected $key;
    const UNIFIED_ORDER_URL = "https://api.mch.weixin.qq.com/pay/unifiedorder";
    public function __construct($appid, $secret,$input,$key)
    {
        $this->appid = $appid;
        $this->secret = $secret;
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
        $this->data = $input;
        $this->data['appid'] = $this->appid;
        $this->data['secret'] = $this->secret;
        $this->data['spbill_create_ip'] = $_SERVER['SERVER_ADDR'];
    }

    public function jsapi_pay(){
        $data = $this->data;
        if (!array_key_exists('openid',$data)){
            throw new ApiException('缺少网页支付参数openid');
        }
        $data = array_merge($data,array(
            'nonce_str'=>Helper::createNonceStr(),
            'trade_type'=>'JSAPI',
        ));
        $sign = Helper::MakeSign($data,$this->key);
        $data = array_merge($data,array('sign'=>$sign));
        $data = Helper::ArrayToXml($data);
        $response = Helper::postXmlCurl($data,self::UNIFIED_ORDER_URL);
        $res = Helper::XmlToArray($response);
        if($res['return_code'] == "SUCCESS"){  //微信返回成功
            if ($res['result_code'] = 'SUCCESS'){
                $secondSignData = array(
                    "appid"=>$this->appid,
                    "noncestr"=>$res['nonce_str'],
                    "package"=>"prepay_id=" . $res['prepay_id'],
                    "timestamp"=>time(),
                    "signType"=>'MD5'
                );
                $secondSignData['paySign'] = Helper::MakeSign($secondSignData,$this->key);
                return $secondSignData;
            }else{
                throw new ApiException($res['return_msg'],$res['err_code']);
            }
        }else{
            throw new ApiException($res['return_msg']??'微信服务器错误');
        }
    }


    public function app_pay(){
        $data = $this->data;
        $data = array_merge($data,array(
            'nonce_str'=>Helper::createNonceStr(),
            'trade_type'=>'APP',
        ));
        $sign = Helper::MakeSign($data,$this->key);
        $data = array_merge($data,array('sign'=>$sign));
        $data = Helper::ArrayToXml($data);
        $response = Helper::postXmlCurl($data,self::UNIFIED_ORDER_URL);
        $res =  Helper::XmlToArray($response);
        if($res['return_code'] == "SUCCESS"){  //微信返回成功
            if ($res['result_code'] = 'SUCCESS'){
                $secondSignData = array(
                    "appid"=>$this->appid,
                    "noncestr"=>Helper::createNonceStr(),
                    "package"=>"Sign=WXPay",
                    "prepayid"=>$res['prepay_id'],
                    "partnerid"=>$this->data['mch_id'],
                    "timestamp"=>time(),
                );
                $secondSignData['sign'] = Helper::MakeSign($secondSignData,$this->key);
                return $secondSignData;
            }else{
                throw new ApiException($res['return_msg'],$res['err_code']);
            }
        }else{
            throw new ApiException($res['return_msg']??'微信服务器错误');
        }
    }


}