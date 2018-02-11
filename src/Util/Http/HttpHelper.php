<?php
/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */
namespace Linyuee\Wechat\Util\Http;



use Linyuee\Util\Exception\HttpException;

class HttpHelper
{
    public static $connectTimeout = 20000;//20 second
    public static $readTimeout = 80000;//80 second

    private static function curl($url, $httpMethod = "GET", $postFields = null, $headers = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $httpMethod);
        if (defined('ENABLE_HTTP_PROXY')) {  //如果有设置http代理
            curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_PROXY, HTTP_PROXY_IP);
            curl_setopt($ch, CURLOPT_PROXYPORT, HTTP_PROXY_PORT);
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($httpMethod == 'POST'){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        }
        if (self::$readTimeout) {
            curl_setopt($ch, CURLOPT_TIMEOUT, self::$readTimeout);
        }
        if (self::$connectTimeout) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::$connectTimeout);
        }
        //https request
        if (strlen($url) > 5 && strtolower(substr($url, 0, 5)) == "https") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        $output = curl_exec($ch);
        var_dump(curl_error($ch));
        dd($output);
        if (is_array($headers) && 0 < count($headers)) {
            $httpHeaders = self::getHttpHearders($headers);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
        }
        $httpResponse = new HttpResponse();
        $httpResponse->setBody(curl_exec($ch));
        $httpResponse->setStatus(curl_getinfo($ch, CURLINFO_HTTP_CODE));
//        if (curl_errno($ch)) {
//            throw new HttpException('http request fail');
//        }
        curl_close($ch);

        return $httpResponse;
    }

    public static function get($url,$header = null){
        return self::curl($url,'GET',null,$header);
    }

    public static function post($url,$data = null,$header = null){
        return self::curl($url,$data,$header);
    }

    public static function getHttpHearders($headers)
    {
        $httpHeader = array();
        foreach ($headers as $key => $value) {
            array_push($httpHeader, $key.":".$value);
        }

        return $httpHeader;
    }


}
