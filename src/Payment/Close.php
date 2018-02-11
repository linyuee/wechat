<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/12/25
 * Time: 上午11:24
 */

namespace Linyuee\Wechat\Payment;


use Linyuee\Wechat\Util\Exception\ApiException;
use Linyuee\Wechat\Util\Helper;

class Close extends PayBase
{
    const CLOSE_ORDER_URL = 'https://api.mch.weixin.qq.com/pay/closeorder';//关闭订单



    public function close_order($out_trade_no){
        $data = array(
            'appid'=>$this->client->appid,
            'mch_id'=>$this->client->mch_id,
            'nonce_str'=>Helper::createNonceStr(),
            'out_trade_no'=>$out_trade_no
        );
        $res = $this->handler($data,self::CLOSE_ORDER_URL);
        if($res['return_code'] == "SUCCESS"){  //微信返回成功
            if ($res['result_code'] == 'SUCCESS'){
                return true;
            }else{
                throw new ApiException($res['return_msg'],$res['err_code']);
            }
        }else{
            throw new ApiException($res['return_msg']??'微信服务器错误');
        }

    }
}