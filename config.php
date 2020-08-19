<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
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

];
