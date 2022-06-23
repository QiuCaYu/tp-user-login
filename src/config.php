<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2022 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: qjy
// +----------------------------------------------------------------------

return [
    // 请勿修改响应码key，可修改 code、message字段
    'response_code' => [
        '400' => [
            'code' => '400',
            'system_error_message' => '参数校验错误',
            'message' => '参数校验错误',
        ],
        '420' => [
            'code' => '420',
            'system_error_message' => '获取用户信息失败',
            'message' => '账号或密码有误',
        ],
        '421' => [
            'code' => '421',
            'system_error_message' => '校验密码错误',
            'message' => '账号或密码有误',
        ]
    ],
    'meta' => [
        // 默认读取 default 内容,新增配置可复制 default内容,修改为其他名称即可使用
        'default' => [
            'table' => '',
            // 过滤表字段
            'filter_field' => [
                'password',
                'salt',
            ],
            'cache'=> [
                // 缓存时间
                'times' => 86400,
                // token缓存前缀定义
                'token_prefix' => 'default_user_login:',
                // 用户信息缓存前缀定义
                'user_prefix' => 'default_user_login:user:',
            ]
        ]
        // ...
    ]
];
