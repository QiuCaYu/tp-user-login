# 基于tp6开发的一个登录验证功能

### 安装
```

composer install QiuCaYu/tp-user-login

```

### 文件目录
```
  cayu/tp-user-login
    ├── composer.json 
    ├── README.md
    └── src
        ├── concern
        │   └── LoginService.php   #登录服务类
        ├── config.php 配置文件
        ├── exception
        │   └── ValidateErrorException.php  统一错误异常处理
        ├── lib
        │   └── Config.php  初始化配置类
        ├── Login.php  facade门面类
        └── model 模型
            └── User.php 用户模型

```

### 数据库设计要求
```
    用户表必须带有以下固定字段：
        account 账户名称
        password 密码
        salt 加密盐
        
    加密方法略
        
```

### tp6配置目录下生成的 tplogin.php 配置说明
```
   // 请勿修改统一响应码key，可修改 code、message字段
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
        ],
        '411' => [
            'code' => '411',
            'system_error_message' => '请检查文件缓存配置',
            'message' => '请检查文件缓存配置',
        ],
        '410' => [
            'code' => '410',
            'system_error_message' => '请检查文件配置',
            'message' => '请检查文件配置',
        ],
        '430' => [
            'code' => '430',
            'system_error_message' => '用户信息不存在',
            'message' => '用户信息不存在',
        ],
        '435' => [
            'code' => '435',
            'system_error_message' => '缓存用户信息有误',
            'message' => '缓存用户信息有误',
        ]
    ],
    // 配置模型、缓存定义
    'meta' => [
        // 默认读取 default 内容,新增配置可复制 default内容,修改为其他名称即可使用
        'default' => [
            'table' => '',
            // 过滤表字段，获取用户信息时，过滤用户敏感数据字段
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
```

### 使用方法

