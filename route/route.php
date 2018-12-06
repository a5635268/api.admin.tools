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

// 强制使用路由
Route::miss('publics/miss');

// 主页路由
Route::get('/', function () {
    return 'hello,ThinkPHP5!';
});

// 测试
Route::rule('test','publics/test');

Route::rule('sendEmail','publics/sendEmail');
Route::rule('test2','publics/test2');

// 路由分组
$routeTest = [
    ':id' => 'index/Route/index',
    ':name' => 'index/Route/index',
    'create' => 'index/Route/create',
];

// 中间件指定
Route::group('route',$routeTest)
    ->pattern(['id' => '\d+', 'name' => '\w+']);
//    ->middleware(['validate']);