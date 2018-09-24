#用法：
======================

###服务端启动：

```
/usr/bin/beanstalkd -l 0.0.0.0 -p 11300 -b /var/lib/beanstalkd/binlog -F

```

###使用：

你可以在你的任意Controller里如下使用：

```
use Hunter\queue\Plugin\ProviderManager;

$providerManager = new ProviderManager(); //或者依赖注入

$data = array();
$data['title'] = '消息队列发邮件';
$data['content'] = '消息队列邮件内容';
$data['send_to'] = '498023235@qq.com';

$provider = $providerManager->loadProvider();
$provider->createItem('send_mail', $data);

```

###监听：

```
php hunter queue:work --daemon（不加--daemon为执行单个任务）

```
