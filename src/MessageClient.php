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
    protected $input;
    protected $token;
    protected $response;
    protected $welcome_response;
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
        $this->reply();
        //return false;
    }

    protected  function reply(){
        //获取到微信推送过来post数据（xml格式）
        //$postArr = $GLOBALS["HTTP_RAW_POST_DATA"];
        $postArr = file_get_contents('php://input');
        $postObj = simplexml_load_string( $postArr );
        $toUser   = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;
        $time     = time();
        $msgType  =  'text';

        if( $postObj->MsgType == 'event'){ //事件
//            if( $postObj->Event == 'SCAN' ){
//                //$EventKey = isset($postObj->EventKey)??null; //带的参数
//                $content = "欢迎关注";
//                $info = self::response_text($toUser, $fromUser, $time, $msgType,$content);
//                echo $info;exit();
//            }
            if ($postObj->Event == 'subscribe'){
                if ($this->welcome_response){
                    $info = self::responseText($toUser, $fromUser, $time, $msgType,$this->welcome_response);
                    echo $info;exit();
                }
            }
        }elseif ($postObj->MsgType == 'text' && $this->response != null){//消息
            $response = $this->response; //自动回复规则
            $content = $postObj->Content;
            if (is_array($response)){
                if (isset($response[(string)$content])){
                    $info = self::responseText($toUser, $fromUser, $time, $msgType,$response[(string)$content]);
                    echo $info;exit();
                }
            }elseif (is_string($response)){
                $info = self::responseText($toUser, $fromUser, $time, $msgType,$response);
                echo $info;exit();
            }
        }

    }

    public function setAutoReply($response){
        $this->response = $response;
        return $this;
    }

    public function setWelcomeReply($response){
        $this->welcome_response = $response;
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
}