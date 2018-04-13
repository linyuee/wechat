<?php
/**
 * Created by PhpStorm.
 * User: yuelin
 * Date: 2018/3/22
 * Time: 下午4:38
 */

namespace Linyuee\Wechat\Mp;


class Message
{
    private $access_token;
    public function __construct(AccessToken $accessToken)
    {
        $this->access_token = $accessToken;
    }

    const ADD_CUSTOMER_SERVICE_URL = 'https://api.weixin.qq.com/customservice/kfaccount/add';

    public function addCustomerService(){

    }
}