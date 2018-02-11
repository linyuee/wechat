<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/12/29
 * Time: 下午7:56
 */

namespace Linyuee\Wechat\Payment;


use Linyuee\Wechat\Util\Exception\ApiException;
use Linyuee\Wechat\Util\Helper;

class Refund extends PayBase
{

    const REFUND_URL = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
    private $by;
    private $cert;

    public function refundByTranscodeId($transcode_id){
        $this->by = array('transcode_id'=>$transcode_id);
        return $this->refund();
    }

    public function refundByOutTradeNo($out_trade_no){
        $this->by = array('out_trade_no'=>$out_trade_no);
        return $this->refund();
    }

    public function refund(){
        if (empty($this->cert)){
            throw new ApiException('证书不能为空');
        }
        $data = array(
            'appid'=>$this->client->appid,
            'mch_id'=>$this->client->mch_id,
            'nonce_str'=>Helper::createNonceStr(),
        );
        $data = array_merge($data,$this->by);
        $data = array_merge($data,$this->client->data);
        $res = $this->handler($data,self::REFUND_URL);
        return $res;
    }

    public function setCert(array $cert){
        if (!array_key_exists('SSLCERT_PATH',$cert)){
            throw new ApiException('证书数组没有SSLCERT_PATH');
        }
        if (!array_key_exists('SSLKEY_PATH',$cert)){
            throw new ApiException('证书数组没有SSLKEY_PATH');
        }
        if (!file_exists($cert['SSLKEY_PATH'])||!file_exists($cert['SSLCERT_PATH'])){
            throw new ApiException('证书路径错误');
        }
        $this->cert = $cert;
        return $this;
    }

}