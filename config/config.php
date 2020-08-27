<?php
// +----------------------------------------------------------------------
// | 配置文件
// +----------------------------------------------------------------------

return [
    // 数据库配置
    'database'            => [
        // 默认数据连接标识
        'default'     => 'mysql',
        // 数据库连接信息
        'connections' => [
            'mysql' => [
                // 数据库类型
                'type'     => 'mysql',
                // 主机地址
                'hostname' => '127.0.0.1',
                // 用户名
                'username' => 'root',
                // 数据库密码
                'password'    => 'root',
                // 数据库名
                'database' => 'worker',
                // 数据库编码默认采用utf8
                'charset'  => 'utf8',
                // 数据库表前缀
                'prefix'   => '',
                // 数据库调试模式
                'debug'    => false,
                // 开启断线重连
                'break_reconnect' => true,
            ],
        ],
    ],
    //密码盐
    'password_salt' => 'erigtjoerjhw',
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'         => '',

    //redis配置
    'redis'            => [
        // 主机地址
        'host' => '127.0.0.1',
        //端口号
        'port' => '6379',
        // 密码
        'password'    => '',
    ],


];
