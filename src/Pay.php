<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/12/22
 * Time: 下午6:28
 */

namespace Linyuee;


use Linyuee\Exception\ApiException;
use Linyuee\Payment\Close;
use Linyuee\Payment\Download;
use Linyuee\Payment\Query;
use Linyuee\Payment\Refund;
use Linyuee\Payment\Unifiedorder;
use Linyuee\Util\Helper;
use Mockery\Matcher\Closure;

class Pay
{
    public $data;
    public function __construct($appid,$mch_id,$key)
    {
        $this->appid = $appid;
        $this->mch_id = $mch_id;
        $this->key = $key;
    }

    public function unifiedOrder($input){
        if (!is_array($input)){
            throw new ApiException('数据格式错误');
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
        $this->data['mch_id'] = $this->mch_id;
        $this->data['spbill_create_ip'] = Helper::get_client_ip();
        $unifiedorder = new Unifiedorder($this);
        return $unifiedorder;
    }

    public function query(){
        return new Query($this);
    }

    public function close($out_trade_no){
        $close =  new Close($this);
        return $close->close_order($out_trade_no);
    }

    public function download(){
        return new Download($this);
    }

    public function refund($input){
        if (!is_array($input)){
            throw new ApiException('数据格式错误');
        }
        if (!array_key_exists('out_refund_no',$input)){
            throw new ApiException('缺少参数out_refund_no');
        }
        if (!array_key_exists('total_fee',$input)){
            throw new ApiException('缺少参数total_fee');
        }
        if (!array_key_exists('refund_fee',$input)){
            throw new ApiException('缺少参数refund_fee');
        }
        $this->data = $input;
        return new Refund($this);
    }
}