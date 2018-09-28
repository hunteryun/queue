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

Supervisor 守护进程方式


```
yum install supervisor //安装
systemctl enable supervisord.service //开机启动
systemctl start supervisord.service //启动
systemctl restart supervisord.service //重启
systemctl stop supervisord.service //停止服务
systemctl kill httpd.service //停不下来时杀死服务
systemctl daemon-reload //配置改变之后重载

supervisorctl status //状态
supervisorctl stop hunter //关闭 hunter
supervisorctl start hunter //启动 hunter
supervisorctl restart hunter //重启 hunter
supervisorctl reread //重新加载配置
supervisorctl update  //更新新的配置
supervisorctl reload //重新运行
supervisorctl stop all //停止全部进程

```
配置例子：hunter.ini 放置在 /etc/supervisord.d/目录下

```
[program:hunter]
process_name=%(program_name)s_%(process_num)02d
command=php /home/wwwroot/test.hunterphp.com/hunter queue:work
autostart=true
autorestart=true
user=root
numprocs=8
redirect_stderr=true
stdout_logfile=/home/wwwroot/test.hunterphp.com/sites/logs/worker.log

```

报错时运行：

supervisor 配置完毕，使用supervisorctl reload 和supervisorctl update 启动时候报错

解决方法使用下面命令启动
```
/usr/bin/python2 /usr/bin/supervisord -c /etc/supervisord.conf

```

重启阿里云



systemd 守护进程方式

配置例子：hunterphp.service 放置在 /etc/systemd/system/目录下

```
[Unit]
Description=hunterphp test daemon
After=rc-local.service nss-user-lookup.target

[Service]
ProtectSystem=full
Type=simple
ExecStart=/usr/local/php/bin/php /home/wwwroot/test.hunterphp.com/hunter queue:work
Restart=always
RestartSec=5s

[Install]
WantedBy=multi-user.target

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
