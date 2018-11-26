<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

error_reporting(E_ERROR | E_PARSE);//过滤错误

// 常量定义
define('NOW', time());

// 环境定义
// define('WEB_VERSION', 'develop'); //开发
define('WEB_VERSION', 'test');  //测试
// define('WEB_VERSION', 'product');    //正式