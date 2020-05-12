<?php
// 事件定义文件
return [
    'bind'  => [
        'mobile_check'=> 'app\common\event\MobileCheck',
    ],

    'listen'    => [
        'AppInit'  => [],
        'HttpRun'  => [],
        'HttpEnd'  => [],
        'LogLevel' => [],
        'LogWrite' => [],
        'mobile_check'=> 'app\common\listener\MobileCheck',
    ],

    'subscribe' => [
    ],
];
