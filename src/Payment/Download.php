<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/12/25
 * Time: 上午11:56
 */

namespace Linyuee\Wechat\Payment;



use Linyuee\Wechat\Util\Helper;

class Download extends PayBase
{
    const DOWNLOAD_BILL_URL = 'https://api.mch.weixin.qq.com/pay/downloadbill';//下载账单


    public function allOrder($date,$gzip = false){
        return $this->download($date,'ALL',$gzip);
    }

    public function successOrder($date,$gzip = false){
        return $this->download($date,'SUCCESS',$gzip);
    }

    public function refundOrder($date,$gzip = false){
        return $this->download($date,'REFUND',$gzip);
    }

    public function rechargeRefundOrder($date,$gzip = false){
        return $this->download($date,'REFUND',$gzip);
    }

    private function download($date,$type,$gzip){
        $data = array(
            'appid'=>$this->client->appid,
            'mch_id'=>$this->client->mch_id,
            'nonce_str'=>Helper::createNonceStr(),
            'bill_date'=>$date,
            'bill_type'=>$type,
        );
        if($gzip == true){
            $data['tar_type'] = 'GZIP';
        }
        $sign = Helper::MakeSign($data,$this->client->key);
        $sendData = array_merge($data,array('sign'=>$sign));
        $sendData = Helper::ArrayToXml($sendData);
        $response = Helper::postXmlCurl($sendData,self::DOWNLOAD_BILL_URL);
        return $response;
    }
}