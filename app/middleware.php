<?php

// 此处定义的是全局中间件，是会自动执行的，不需要在路由定义

return [
    'signCheck' => app\common\middleware\SignCheck::class,
    'validate' =>  app\common\middleware\Validate::class,
];