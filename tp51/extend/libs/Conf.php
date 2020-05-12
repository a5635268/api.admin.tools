<?php
namespace libs;

use think\facade\Config;

/**
 * 兼容yaconf的conf获取器
 * Class Conf
 * @package Libs
 */
class Conf
{
    public static function get(string $arg)
    {
        if(extension_loaded('yaconf') && \Yaconf::has(strtolower($arg))){
             return \Yaconf::get(strtolower($arg));
        }
        $arg = substr($arg,strpos($arg, '.')+1);
        return Config::get($arg);
    }
}