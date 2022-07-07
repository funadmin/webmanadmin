<?php

return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            // 数据库类型
            'type' => 'mysql',
            // 服务器地址
            'hostname' => getenv('DB_HOST','127.0.0.1'),
            // 数据库名
            'database' => getenv('DB_NAME','test_webman_com'),
            // 数据库用户名
            'username' =>getenv('DB_USER','test_webman_com'),
            // 数据库密码
            'password' => getenv('DB_PASSWORD','4ss3FABCWj4EPcxa'),
            // 数据库连接端口
            'hostport' => getenv('DB_PORT','3306'),
            // 数据库连接参数
            'params' => [],
            // 数据库编码默认采用utf8
            'charset' => 'utf8mb4',
            // 数据库表前缀
            'prefix' =>  getenv('DB_PREFIX','fun_'),
            // 断线重连
            'break_reconnect' => true,
            // 关闭SQL监听日志
            'trigger_sql' => false,
        ],
    ],
];