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

#### 1、微信授权

```
$wechat = new \Linyuee\Wechat('appid','secret');
$whchat->auth($callback_url,$attach);//$callback_url授权后回调的地址，$state你自己附加的参数，会在回调的时候传回去，可填可不填
```
然后你需要在回调接口获取用户信息,并接收微信服务器发送的$code和$state
```
$wechat = new \Linyuee\Wechat('appid','secret');
$wechat->setCache($cacheDriver);
$data = $wechat->get_userinfo_by_code($code);
```

#### 2、jssdk签名

```
$wechat = new \Linyuee\Wechat('appid','secret');
$wechat->setCache($cacheDriver);
$data=$wechat->get_js_sdk_sign('签名的url');
```

#### 3、微信支付

```
$wechat = new \Linyuee\Wechat('appid','secret');
$input = array(
            'mch_id'=>'1900009851',
            'attach'=>'testtest',//附加信息
            'body'=>'腾讯充值中心-QQ会员充值',
            'out_trade_no'=>random_int(100000,99999999),
            'total_fee'=>10,
            'notify_url'=>'$notify_url',
            'openid'=>$openid,//发起支付用户的openid
        );
$res = $wechat->pay($input,$key)->js_api_pay();//key为商户平台里面的key
```

#### 4、自定义公众号菜单
```
$wechat = new \Linyuee\Wechat('appid','secret');
$wechat->setCache($cacheDriver);
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
$wechat->set_menu($menu)
```
如果返回{"errcode":0,"errmsg":"ok"}便是设置成功


#### 5、生成带参数公众号二维码
```
$wechat = new \Linyuee\Wechat('appid','secret');
$wechat->setCache($cacheDriver);
$data = $wechat->get_qr_code(12);
```

#### 6、获取全部关注者的openid
```
$wechat = new Wechat('wxf1f0507020402ba2','6c1344bd51a23b2f2f024f050a89bc77');
$wechat->setCache($cacheDriver);
$openids = $wechat->get_users();

$userinfo = $wechat->get_user_info($openid);

```

#### 7、根据openid获取用户信息

```
$wechat = new Wechat('wxf1f0507020402ba2','6c1344bd51a23b2f2f024f050a89bc77');
$wechat->setCache($cacheDriver);
$userinfo = $wechat->get_user_info($openid);

```
#### 8、微信接入和自动回复

要使用该功能，需要到公众平台配置相关信息，首次启用服务器配置要填一个url和token。假如你的服务器地址为
http:://test.com,然后你在服务器的根目录有一个wechat.php的文件，这时你只需要在wechat.php写入
该功能暂时没有实现图片和图文连接，后期会支持
```
<?php
    $response = new \Linyuee\WechatResponse($request->all(),'your_token');
    $response->run();

```
然后url便填写http:://test.com/wechat.php,token填写你在php中填写的token就可以了。

设置关注自动回复：
```
$response = new \Linyuee\WechatResponse($request->all(),'your_token');
$response->set_welcome_reply('欢迎关注')->run();
```

设置按关键字自动回复并且关注自动回复：
```
$response = new WechatResponse($request->all(),'chebao');
        $auto_rule = array(       //可以是数组也可以是字符串，如果是字符串的话不管发什么都会回复该字符串
            '你好'=>'很高兴认识你',
            '我要福利'=>'暂时没有福利',
            'test'=>'测试'
        );
$response->set_welcome_reply('欢迎关注')->set_auto_reply($auto_rule)->run();
```

