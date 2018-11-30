<?php

namespace libs;

use think\facade\Log as TPlog;

/**
 *
 * 先适配TP本身的FILE日志，后续可无缝移植到其它日志架构（seaLog || monolog）；
 * Class Log
 * @package libs
 *
 * @method void info($message)
 * @method void err($message)
 * @method void warn($message)
 * @method void debug($message)
 */

/**
 * 自定义日志适配器,多参数记录
 * Class Log

 * @method void emergency(mixed $message, array $context = []) static 记录emergency信息
 * @method void alert(mixed $message, array $context = []) static 记录alert信息
 * @method void critical(mixed $message, array $context = []) static 记录critical信息
 * @method void error(mixed $message, array $context = []) static 记录error信息，会造成程序运行，业务逻辑错误，脏数据等，需要及时处理和解决；
 * @method void warning(mixed $message, array $context = []) static 记录warning信息，程序能正常运行，业务逻辑没错误，但需要及时解决；
 * @method void notice(mixed $message, array $context = []) static 记录notice信息，程序能正常运行，业务逻辑没错误但不美；
 * @method void info(mixed $message, array $context = []) static 需要关注的。像用户登录，交易等入参；
 * @method void debug(mixed $message, array $context = []) static 记录debug信息
 * @method void sql(mixed $message, array $context = []) static 记录sql信息
 * @package libs
 */
class Log {

    /**
     * 静态调用
     * @param $method
     * @param $args
     * @return mixed
     */
    public static function __callStatic($method, $args = [])
    {
        return TPlog::$method($args);
    }
}