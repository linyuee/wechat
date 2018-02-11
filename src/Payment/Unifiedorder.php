<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/12/22
 * Time: 下午6:22
 */
namespace Linyuee\Wechat\Payment;

use Linyuee\Wechat\Util\Exception\ApiException;
use Linyuee\Wechat\Util\Helper;

class Unifiedorder extends PayBase
{

    const UNIFIED_ORDER_URL = "https://api.mch.weixin.qq.com/pay/unifiedorder";//统一下单

    //公众号支付
    public function jsapiPay(){
        $data = $this->client->data;
        if (!array_key_exists('openid',$data)){
            throw new ApiException('缺少网页支付参数openid');
        }
        $data['nonce_str'] = Helper::createNonceStr();
        $data['trade_type'] = 'JSAPI';
        $res = $this->handler($data,self::UNIFIED_ORDER_URL);
        if($res['return_code'] == "SUCCESS"){  //微信返回成功
            if ($res['result_code'] == 'SUCCESS'){
                $secondSignData = array(
                    "appId"=>$this->client->appid,
                    "nonceStr"=>$res['nonce_str'],
                    "package"=>"prepay_id=" . $res['prepay_id'],
                    "timeStamp"=>(string)time(),
                    "signType"=>'MD5'
                );
                $secondSignData['paySign'] = Helper::MakeSign($secondSignData,$this->client->key);
                return $secondSignData;
            }else{
                throw new ApiException($res['return_msg'],$res['err_code']);
            }
        }else{
            throw new ApiException($res['return_msg']??'微信服务器错误');
        }
    }

    //app支付
    public function appPay(){
        $data = $this->client->data;
        $data['nonce_str'] = Helper::createNonceStr();
        $data['trade_type'] = 'APP';
        $res = $this->handler($data,self::UNIFIED_ORDER_URL);
        if($res['return_code'] == "SUCCESS"){  //微信返回成功
            if ($res['result_code'] == 'SUCCESS'){
                $secondSignData = array(
                    "appid"=>$this->client->appid,
                    "noncestr"=>Helper::createNonceStr(),
                    "package"=>"Sign=WXPay",
                    "prepayid"=>$res['prepay_id'],
                    "partnerid"=>$this->client->mch_id,
                    "timestamp"=>time(),
                );
                $secondSignData['sign'] = Helper::MakeSign($secondSignData,$this->client->key);
                return $secondSignData;
            }else{
                throw new ApiException($res['err_code_des'],$res['err_code']);
            }
        }else{
            throw new ApiException($res['return_msg']??'微信服务器错误');
        }
    }
    //H5支付
    public function webPay(){
        $data = $this->client->data;
        if (!array_key_exists('scene_info',$data)){
            throw new ApiException('缺少网页支付参数scene_info');
        }
        $data['nonce_str'] = Helper::createNonceStr();
        $data['trade_type'] = 'MWEB';
        $res = $this->handler($data,self::UNIFIED_ORDER_URL);
        if($res['return_code'] == "SUCCESS") {  //微信返回成功
            if ($res['result_code'] == 'SUCCESS'){
                return $res['mweb_url'];
            }else{
                throw new ApiException($res['return_msg'],$res['err_code']);
            }
        }else{
            throw new ApiException($res['return_msg']??'微信服务器错误');
        }

    }

    public function qrcodePay(){
        $data = $this->client->data;
        $data['nonce_str'] = Helper::createNonceStr();
        $data['trade_type'] = 'NATIVE';
        $res = $this->handler($data,self::UNIFIED_ORDER_URL);
        \Log::info($res);
        if($res['return_code'] == "SUCCESS") {  //微信返回成功
            if ($res['result_code'] == 'SUCCESS'){
                return $res['code_url'];
            }else{
                throw new ApiException($res['return_msg'],$res['err_code']);
            }
        }else{
            throw new ApiException($res['return_msg']??'微信服务器错误');
        }
    }
}