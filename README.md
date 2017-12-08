###简单的微信公众号开发扩展包
集成了微信授权登录，jssdk签名，自定义公众号菜单，微信支付等功能，可能是最适合微信小白开发的包了
####1、微信授权
```
$wechat = new \Linyuee\Wechat('appid','secret');
$whchat->auth($callback_url,$attach);//$callback_url授权后回调的地址，$state你自己附加的参数，会在回调的时候传回去，可填可不填
```
然后你需要在回调接口获取用户信息,并接收微信服务器发送的$code和$state
```
$wechat = new \Linyuee\Wechat('appid','secret');
$data = $wechat->get_userinfo($code);
```
####2、jssdk签名
```
$wechat = new \Linyuee\Wechat('appid','secret');
$data=$wechat->get_js_sdk_sign('签名的url');
```

####3、微信支付
```
$wechat = new \Linyuee\Wechat('appid','secret');
$input = array(
            'mch_id'=>'1900009851',
            'attach'=>'testtest',//附加信息
            'body'=>'腾讯充值中心-QQ会员充值',
            'out_trade_no'=>random_int(100000,99999999),
            'total_fee'=>10,
            'notify_url'=>'$notify_url',
            'openid'=>$redis->get('openid'),
        );
$res = $wechat->pay($input,$key)->js_api_pay();//key为商户平台里面的key
```
