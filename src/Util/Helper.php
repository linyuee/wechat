<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2017/12/6
 * Time: 下午3:45
 */

namespace Linyuee\Util;



use App\Logic\Api\ExpressLogic;
use Linyuee\Exception\ApiException;

class Helper
{
    public static function createNonceStr($length = 16){
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    public static function MakeSign($params,$key)
    {
        //签名步骤一：按字典序排序参数
        ksort($params);
        $string = self::ToUrlParams($params);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".$key;
        var_dump($string);
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    public static function refund_decrypt($str, $key) {
        $str = base64_decode($str);
        $key = md5($key);
        $iv = substr(random_int(100000,999999).'0000000000000000', 0,16);
        $decrypted = openssl_decrypt($str, 'AES-256-CBC', $key, OPENSSL_ZERO_PADDING, $iv);
    }

    /**
     * 格式化参数格式化成url参数
     */
    public static function ToUrlParams($params)
    {
        $buff = "";
        foreach ($params as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 数组转xml
     * @param $values
     * @return string
     * @throws ApiException
     */
    public static function ArrayToXml($values)
    {
        if(!is_array($values)
            || count($values) <= 0)
        {
            throw new ApiException("数组数据异常！");
        }

        $xml = "<xml>";
        foreach ($values as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    public static function XmlToArray($data){
        $msg = (array)simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
        return $msg;
    }

    /**
     * 获取毫秒的时间戳
     */
    public static function getMillisecond()
    {
        $time = explode ( " ", microtime () );
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode( ".", $time );
        $time = $time2[0];
        return $time;
    }

    /**
     * post发送http的xml数据请求
     * @param $xml
     * @param $url
     * @param bool $useCert
     * @param int $second
     * @return mixed
     */
    public static function postXmlCurl($xml, $url, $useCert = false,array $cert = [],$second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if($useCert == true){
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, $cert['SSLCERT_PATH']??'');
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, $cert['SSLKEY_PATH']??'');
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            throw new ApiException("curl出错，错误码:$error");
        }
    }


    public static function https_post($url, $data = null){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }


    public static function get_access_token(){
        if (!file_exists(dirname(__FILE__).'/access_token')){
            return false;
        }
        $data = file_get_contents(dirname(__FILE__).'/access_token');
        if (empty($data)){
            return false;
        }
        $data = explode('=>',$data);
        if ($data[1]<time()){
            return false;
        }
        return base64_decode($data[0]);
    }

    public static function set_access_token($access_token){
        file_put_contents(dirname(__FILE__).'/access_token',base64_encode($access_token).'=>'.(time()+7200));
    }


    public static function gzdecode ($data) {
        $flags = ord(substr($data, 3, 1));
        $headerlen = 10;
        $extralen = 0;
        $filenamelen = 0;
        if ($flags & 4) {
            $extralen = unpack('v' ,substr($data, 10, 2));
            $extralen = $extralen[1];
            $headerlen += 2 + $extralen;
        }
        if ($flags & 8) // Filename
            $headerlen = strpos($data, chr(0), $headerlen) + 1;
        if ($flags & 16) // Comment
            $headerlen = strpos($data, chr(0), $headerlen) + 1;
        if ($flags & 2) // CRC at end of file
            $headerlen += 2;
        $unpacked = @gzinflate(substr($data, $headerlen));
        if ($unpacked === FALSE)
            $unpacked = $data;
        return $unpacked;
    }


    //获取客户端ip
    public static function get_client_ip(){
        $cip = 'unknown';
        if ($_SERVER['REMOTE_ADDR']){
            $cip = $_SERVER['REMOTE_ADDR'];
        }elseif (getenv('REMOTE_ADDR')){
            $cip = getenv('REMOTE_ADDR');
        }
        return $cip;
    }




}