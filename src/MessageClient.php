<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/12/11
 * Time: 下午7:53
 */

namespace Linyuee\Wechat;


class MessageClient
{
    private $input;
    private $token;
    private $response;
    private $welcome_response;
    private $click_response;
    private $msgType = 'text';
    private $qrscene;
    private $scene_id;
    private $scan_response;
    private $latitude; //地理位置纬度
    private $longitude;  //地理位置经度
    private $precision; //地理位置精度
    private $event;
    public function __construct($token)
    {
        $this->input = $_GET;
        $this->token = $token;
    }

    public  function run(){
        $data = $this->input;
        $token = $this->token;
        $signature = $data['signature'];
        $timestamp = $data['timestamp'];
        $nonce = $data['nonce'];
        if (isset($data['echostr'])){
            $tmpArr = array($token, $timestamp, $nonce);
            sort($tmpArr, SORT_STRING);
            $tmpStr = implode($tmpArr);
            $tmpStr = sha1($tmpStr);
            if ($tmpStr == $signature) {
                return $data['echostr'];
            }
        }

        //回复事件或消息
        $this->handler();
        //return false;
    }
    //处理微信服务器推送
    protected  function handler(){
        //获取到微信推送过来post数据（xml格式）
        //$postArr = $GLOBALS["HTTP_RAW_POST_DATA"];
        $postArr = file_get_contents('php://input');
        $postObj = simplexml_load_string( $postArr );
        $toUser   = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;
        $time     = time();
        $msgType  =  $this->msgType;
        $this->event = $postObj->Event;
        \Log::info($postObj->Event);
        \Log::info($postObj->EventKey);
        if( $postObj->MsgType == 'event'){ //事件

            if ($postObj->Event == 'subscribe'){  //关注事件
                if ($this->welcome_response){
                    $this->qrscene = $postObj->EventKey; //事件KEY值，qrscene_为前缀，后面为二维码的参数值
                    $info = self::responseText($toUser, $fromUser, $time, $msgType,$this->welcome_response);
                    return $info;
                }
            }
            if ($postObj->Event == 'SCAN'){  //已关注扫码事件
                $this->scene_id = $postObj->EventKey; //事件KEY值，是一个32位无符号整数，即创建二维码时的二维码scene_id
                $info = self::responseText($toUser, $fromUser, $time, $msgType,$this->scan_response);
                return $info;
            }
            if ($postObj->Event == 'LOCATION'){  //获取地理位置
                $this->latitude = $postObj->Latitude;
                $this->longitude = $postObj->Longitude;
                $this->precision = $postObj->Precision;
            }
            if ($postObj->Event == 'CLICK'){  //点击事件

            }
        }elseif ($postObj->MsgType == 'text' && $this->response != null){//消息
            $response = $this->response; //自动回复规则
            $content = $postObj->Content;
            if (is_array($response)){
                if (isset($response[(string)$content])){
                    $info = self::responseText($toUser, $fromUser, $time, $msgType,$response[(string)$content]);
                    return $info;
                }
            }elseif (is_string($response)){
                $info = self::responseText($toUser, $fromUser, $time, $msgType,$response);
                return $info;
            }
        }

    }

    //设置扫码
    public function setScanReply($response){
        $this->scan_response = $response;
        return $this;
    }

    public function setAutoReply($response){
        $this->response = $response;
        return $this;
    }

    public function setWelcomeReply($response){
        $this->welcome_response = $response;
        return $this;
    }

    public function setClickReply($response){
        $this->click_response = $response;
        return $this;
    }

    protected static function responseText($toUser, $fromUser, $time, $msgType, $content){
        $template = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            </xml> ";
        return sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
    }

    public function getSceneId(){
        return $this->scene_id;
    }

    public function getQrscene(){
        return $this->qrscene;
    }
    //返回地理位置
    public function getLocation(){
        return ['latitude'=>$this->latitude,'longitude'=>$this->longitude,'precision'=>$this->precision];
    }

    public function getEvent(){
        return $this->event;
    }
}