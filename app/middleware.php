<?php

// 此处定义的是全局中间件，是会自动执行的，不需要在路由定义

return [
    'validate' =>  app\common\middleware\Validate::class
];