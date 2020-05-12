<?php

use think\Env;

define('APP_PATH', __DIR__ . '/../application/');
define('NOW', time());

// 加载框架引导文件
require __DIR__ . '/../thinkphp/base.php';

// 绑定到admin模块
\think\Route::bind('admin');

// 设置根url
\think\Url::root(Env::get('root_url'));

// 执行应用
\think\App::run()->send();

