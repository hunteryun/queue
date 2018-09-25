#用法：
======================

###config.php添加配置：

beanstalk

```
$queue_server = array(
  'driver' => 'beanstalk',
  'queue' => 'default',
  'host' => 'localhost',
  'port' => 11300
);

```

redis

```
$queue_server = array(
  'driver' => 'redis',
  'queue' => 'default',
  'host' => 'localhost',
  'port' => 6379
);

```

file

```
$queue_server = array(
  'driver' => 'file',
  'queue' => 'default',
  'path' => 'sites/message'
);

```

database

```
$queue_server = array(
  'driver' => 'database',
  'queue' => 'default'
);

$databases = array(
    'default' => array(
        'host'      => 'localhost',
        'port'      => '3306',
        'database'  => 'test',
        'username'  => 'username',
        'password'  => 'password',
        'prefix'    => '',
        'charset'   => 'utf8mb4',
    ),
);

```

###服务端启动：

beanstalkd

```
/usr/bin/beanstalkd -l 0.0.0.0 -p 11300 -b /var/lib/beanstalkd/binlog -F

```

redis

```
src/redis-server

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
