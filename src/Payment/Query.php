<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/12/25
 * Time: 上午11:09
 */

namespace Linyuee\Wechat\Payment;


use Linyuee\Wechat\Util\Helper;

class Query extends PayBase
{
    const QUERY_ORDER_URL = 'https://api.mch.weixin.qq.com/pay/orderquery';

    const QUERY_REFUND_URL = 'https://api.mch.weixin.qq.com/pay/refundquery';


    //根据out_trade_no查询订单
    public function OrderByOutTradeNo($out_trade_no){
        return $this->queryOrder(['out_trade_no'=>$out_trade_no]);
    }
    //根据transaction_id查询订单
    public function OrderByTransactionId($transaction_id){
        return $this->queryOrder(['transaction_id'=>$transaction_id]);

    }

    public function refundByOutTradeNo($out_trade_no){
        return $this->queryRefund(['out_trade_no'=>$out_trade_no]);
    }

    public function refundByTransactionId($transaction_id){
        return $this->queryRefund(['transaction_id'=>$transaction_id]);
    }

    public function refundByOutRefundNo($out_refund_no){
        return $this->queryRefund(['out_refund_no'=>$out_refund_no]);
    }

    public function refundByRefundId($refund_id){
        return $this->queryRefund(['refund_id'=>$refund_id]);
    }

    private function queryOrder(array $by){
        $data = array(
            'appid'=>$this->client->appid,
            'mch_id'=>$this->client->mch_id,
            'nonce_str'=>Helper::createNonceStr(),
        );
        $data = array_merge($data,$by);
        $res = $this->handler($data,self::QUERY_ORDER_URL);
        return $res;
    }

    private function queryRefund(array $by){
        $data = array(
            'appid'=>$this->client->appid,
            'mch_id'=>$this->client->mch_id,
            'nonce_str'=>Helper::createNonceStr(),
        );
        $data = array_merge($data,$by);
        $res = $this->handler($data,self::QUERY_REFUND_URL);
        return $res;
    }
}