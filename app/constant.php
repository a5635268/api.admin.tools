<?php

/**
 * 本文件用于各种常量定义
 */

//过滤错误
error_reporting(E_ERROR | E_PARSE);

// 常量定义
define('NOW', time());

// 环境定义
define('WEB_VERSION', 'develop'); //开发
//define('WEB_VERSION', 'test');  //测试
//define('WEB_VERSION', 'product'); //正式