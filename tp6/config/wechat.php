<?php

return [
    /*
      * 默认配置，将会合并到各模块中
      */
    'default'         => [
        /*
         * 使用 ThinkPHP 的缓存系统
         */
        'use_tp_cache'  => true,
        'token' => 'omJNpZEhZeHj1ZxFECKkP48B5VFbk1HP',
        'aes_key' => 'rOyv7myiKskXknSADBi7qhMBiD9QoCryrCVb1FnvnkQ',
        /*
         * 日志配置
         *
         * level: 日志级别，可选为：
         *                 debug/info/notice/warning/error/critical/alert/emergency
         * file：日志文件位置(绝对路径!!!)，要求可写权限
         */
        'log'           => [
            'level' => env('WECHAT_LOG_LEVEL', 'debug'),
            'file' => env('WECHAT_LOG_FILE', app()->getRuntimePath()."log/wechat.log"),
        ],
        'guzzle' => [
            'verify' => false,
            'timeout' => 4.0,
        ],
    ],

    //小程序
    'mini_program'     => [
        'app_id'  => env('WECHAT.MINI_PROGRAM_APPID', ''),
        'secret'  => env('WECHAT.MINI_PROGRAM_SECRET', ''),
    ],

    'official_account' => [
        'app_id'  => env('WECHAT.OFFICIAL_ACCOUNT_APPID', ''),
        'secret'  => env('WECHAT.OFFICIAL_ACCOUNT_SECRET', ''),
    ],

    //支付
    'payment'          => [
        'sandbox'    => env('WECHAT.PAYMENT_SANDBOX', false),
        'app_id'     => env('WECHAT.PAYMENT_APPID', 'wx67523baf13c0163c'),
        'mch_id'     => env('WECHAT.PAYMENT_MCH_ID', '1558388411'),
        'key'        => env('WECHAT.PAYMENT_KEY', 'chamshareapi20191009CHAMSHAREAPI'),
    ],
];
