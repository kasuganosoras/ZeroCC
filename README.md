# ZeroCC
一个 PHP 写的多线程单机 CC 压力测试脚本

脚本仅供学习，请勿用于违法使用，造成的一切后果由使用者自行承担。

## 环境需求

如果要运行 ZeroCC，需要服务器安装 PHP 5.2 以上版本，最高支持到 7.2.9（暂不支持 PHP 7.3）

PHP 需要安装并启用 pthreads 扩展，可以使用 Pecl 或手工编译安装

### Pecl 安装方式
1. 执行命令 `pecl install pthreads`
2. 修改 php.ini 加入一行 `extension=pthreads.so`

### 手工编译方式

1. 执行命令
    ```
    wget https://pecl.php.net/get/pthreads-3.1.6.tgz -o pthreads.tgz
    tar xzvf pthreads.tgz
    cd pthreads/
    /your/php/path/bin/phpize
    ./configure --with-php-config=/your/php/path/bin/php-config
    make
    make install
    ```
2. 修改 php.ini 加入一行 `extension=pthreads.so`

## 脚本参数

脚本一共有以下几个参数，<> 是必须的，[] 是可选的

```
php zerocc.php <域名或 IP> <端口> <线程数> [网站 URI] [是否使用 CURL]
```

网站 URI 指的是网址中，域名后面的部分，例如 `http://www.baidu.com/index.html` 中，`/index.html` 就是 URI。

默认使用空连接而不是 CURL 模式，如果最后一个参数指定了 true 将会启用 CURL 模式，适合用于刷访问量，效率不高。

## 示例使用

例如需要压测 `http://www.baidu.com/index.html`，使用 32 线程，那么就输入以下命令

```
php zerocc.php www.baidu.com 80 32 /index.html
```

## Licences

此项目使用 GPL v3 协议开源
