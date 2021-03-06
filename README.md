简单的微信公众号开发扩展包wechat
===================

简介
------------
这是一个方便微信开发新手的开发的包，你们甚至可以不用知道其中的逻辑，而且使用非常方便，如果觉得对你有用的话请支持一下点个star

安装
------------

```
composer require linyuee/wechat
```
集成了微信授权登录，jssdk签名，自定义公众号菜单，微信支付等功能，可能是最适合微信小白开发的包了

使用
------------
#### 说明
AccessToken 的有效期目前为 7200 秒，有请求次数限制，重复获取将导致上次获取的 AccessToken 失效，
因此有必要缓存AccessToken，你如果只是测试也可以不使用，我们使用doctrine/cache包作为依赖，在初始化Wechat类
的时候进行注入，目前的缓存驱动有 file、APC、redis、memcache、memcached、xcache。
不理解请移步http://blog.csdn.net/wolehao/article/details/17733289
``
Filesystem
```
$cacheDriver = new \Doctrine\Common\Cache\FilesystemCache('./cacheDir');
```
APC
```
$cacheDriver = new \Doctrine\Common\Cache\ApcCache();
```
Memcache
```
$memcache = new Memcache();
$memcache->connect('127.0.0.1', 11211);

$cacheDriver = new \Doctrine\Common\Cache\MemcacheCache();
$cacheDriver->setMemcache($memcache);
```
Mamcached
```
$memcached = new Memcached();
$memcached->addServer('127.0.0.1', 11211);

$cacheDriver = new \Doctrine\Common\Cache\MemcachedCache();
$cacheDriver->setMemcached($memcached);
```
Xcache
```
$cacheDriver = new \Doctrine\Common\Cache\XcacheCache();
Redis

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$cacheDriver = new \Doctrine\Common\Cache\RedisCache();
$cacheDriver->setRedis($redis);

```


### 一、公众号开发

####初始化客户端

$cacheDriver在\Linyuee\Wechat\MpClient类初始化的时候进行注入，用来缓存access_token，如果不注入也可以运行，但是每次都会去获取新的access_token
```angular2html
$wechat = new \Linyuee\Wechat\MpClient('appid','secret',$cacheDriver);
```
#### 1、微信授权

```
$whchat->auth()->base($callback_url,$attach);//$callback_url授权后回调的地址，$state你自己附加的参数，会在回调的时候传回去，可填可不填
```
然后你需要在回调接口获取用户信息,并接收微信服务器发送的$code和$state
```
$data = $wechat->getUserinfoByCode($code);
```

#### 2、jssdk签名

```
$data=$wechat->getJsSdkSign('签名的url');
```
#### 3、用户管理

根据openid获取用户信息
```angular2html
$res = $wechat->user()->getUserInfo('ogzUjwMevWmSnr__y9aOMVCVvU1g');
```
获取所有用户的openid
```angular2html
$res = $wechat->user()->getAllOpenid('ogzUjwMevWmSnr__y9aOMVCVvU1g');
```


#### 4、公众号菜单
自定义菜单
```
$menu = array(
            'button'=>array(
                [
                    'name'=>'公司主页',
                    'type'=>'view',
                    'url'=>'http://test.com'
                ],
                [
                    'name'=>'项目主页',
                    'type'=>'view',
                    'url'=>'http://test.com/api/test',
                ]
            ),
        );
$wechat->menu()->setMenu($menu)
```
如果返回{"errcode":0,"errmsg":"ok"}便是设置成功

删除菜单
```angular2html
$wechat->menu()->deleteMenu($menu)
```
#### 5、生成带参数公众号二维码
```
$wechat = new \Linyuee\Wechat('appid','secret',$cacheDriver)
$data = $wechat->getQrcode('id'); //id是带的参数
```

#### 8、微信接入和自动回复

要使用该功能，需要到公众平台配置相关信息，首次启用服务器配置要填一个url和token。假如你的服务器地址为
http:://test.com,然后你在服务器的根目录有一个wechat.php的文件，这时你只需要在wechat.php写入
该功能暂时没有实现图片和图文连接，后期会支持
```
$response = new \Linyuee\Message('your_token');
$response->run();

```
然后url便填写http:://test.com/wechat.php,token填写你在php中填写的token就可以了。

设置关注自动回复：
```
$response = new \Linyuee\WechatResponse('your_token');
$response->setWelcomeReply('欢迎关注')->run();
```

设置按关键字自动回复并且关注自动回复：
```
$response = new WechatResponse('chebao');
        $auto_rule = array(       //可以是数组也可以是字符串，如果是字符串的话不管发什么都会回复该字符串
            '你好'=>'很高兴认识你',
            '我要福利'=>'暂时没有福利',
            'test'=>'测试'
        );
$response->setWelcomeReply('欢迎关注')->setAutoReply($auto_rule)->run();
```
### 支付

```
$wechat = new PayClient('appid','mch_id','key'); //初始化
$input1 = array(  //公众号支付参数
            //必须参数
            'mch_id'=>'1900009851',
            'body'=>'腾讯充值中心-QQ会员充值', 
            'out_trade_no'=>random_int(100000,99999999),
            'total_fee'=>10,
            'notify_url'=>'$notify_url',
            'openid'=>'23da2Ar3efD23r1rd12S',//发起支付用户的openid
            //非必须参数
            'device_info'=>'',
            'attach'=>'', //附加数据，回调时会返回
            'time_start'=>'20091225091010',
            'time_expire'=>'20091227091010',
            'detail'=>'',
            'goods_tag'=>'',
            'scene_info'=>''
        );
        $input2 = array(   //app支付参数
            //必须参数
            'mch_id'=>'1900009851',
            'body'=>'腾讯充值中心-QQ会员充值',
            'out_trade_no'=>random_int(100000,99999999),
            'total_fee'=>10,
            'notify_url'=>'$notify_url',
            //非必须参数
            'device_info'=>'',
            'attach'=>'', //附加数据，回调时会返回
            'time_start'=>'20091225091010',
            'time_expire'=>'20091227091010',
            'detail'=>'',
            'goods_tag'=>'',
            'scene_info'=>''
        );

$res = $wechat->unifiedOrder($input1)->jsapiPay();
$res = $wechat->unifiedOrder($input2)->appPay();
$res = $wechat->unifiedOrder($input2)->webPay();
```
##### 2、查询
```
$res = $wechat->query()->OrderByOutTradeNo('10644950');//通过out_trade_no查询订单
$res = $wechat->query()->OrderByTransactionId('12177525012014070');//通过transaction_id查询订单
$res = $wechat->query()->refundByOutTradeNo('10644950');//通过out_trade_no查询订单退款
$res = $wechat->query()->refundByTransactionId('1217752501201407033233368018');//通过transaction_id查询订单退款
$res = $wechat->query()->refundByOutRefundNo('1217752501201407033233368018');//通过out_refund_no查询订单退款
$res = $wechat->query()->refundByRefundId('1217752501201407033233368018');//通过refund_id查询订单退款
```
##### 3、关闭订单
```

$res = $wechat->close('10644950');//关闭订单只能通过out_trade_no

```

##### 4、获取数据
```

$res = $wechat->download()->allOrder('20171111'); //获取2017年11月11日所有的订单
$res = $wechat->download()->successOrder('20171111');//获取2017年11月11日所有成功的订单
$res = $wechat->download()->refundOrder('20171111');//获取2017年11月11日所有退款的订单
$res = $wechat->download()->rechargeRefundOrder('20171111');//获取2017年11月11日充值退款订单（相比其他对账单多一栏“返还手续费”）

```

##### 5、退款
微信退款必须申请到证书，并将证书发在服务器上可访问的路径
```

$data = array(
       'total_fee'=>'订单金额',
       'out_refund_no'=>'自定义退款号',
       'refund_fee'=>'退款金额'
      );
$res = $pay->refund($data)->setCert(array(
        'SSLCERT_PATH'=>'/etc/Cert/wechat/apiclient_cert.pem',
        'SSLKEY_PATH'=>'/etc/Cert/wechat/apiclient_key.pem'
      ))->refundByOutTradeNo($params['out_trade_no']);

```
