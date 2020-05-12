<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        'create:controller' => 'app\\common\\command\\create\\Controller' ,
        'create:model' => 'app\\common\\command\\create\\Model' ,
        'create:validate' => 'app\\common\\command\\create\\Validate',
        'create:command' => 'app\\common\\command\\create\\Command',
        'test' => 'app\\command\\Test',
        'card' => 'app\\command\\Card' ,
    ],
];
