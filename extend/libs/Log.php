<?php

namespace libs;

use think\Log as TPlog;

/**
 * 自定义日志适配器
 * 先适配TP本身的FILE日志，后续可无缝移植到其它日志架构（seaLog || monolog）；
 * Class Log
 * @package libs
 *
 * @method void info($message)
 * @method void err($message)
 * @method void warn($message)
 * @method void debug($message)
 */
class Log {

    const INFO   = 'info';  // 需要关注的。像用户登录，交易等入参；
    const ERROR  = 'err';  // 错误，会造成程序运行，业务逻辑错误，脏数据等，需要及时处理和解决；
    const WARN  =  'warn'; // 程序能正常运行，业务逻辑没错误但不美；
    const DEBUG = 'debug'; // 调试日志

    protected static $type = ['info' => 'log', 'err' => 'error', 'warn' => 'notice','debug' => 'debug'];

    /**
     * 静态调用
     * @param $method
     * @param $args
     * @return mixed
     */
    public static function __callStatic($method, $args = [])
    {
        if (in_array($method, array_keys(self::$type))) {
            $level = self::$type[$method];
            $args[] = self::serviceLog();
            return TPlog::$level($args);
        }
    }

    public static function serviceLog($message = ''){
        // 获取基本信息
        $runtime    = round(microtime(true) - THINK_START_TIME, 10);
        $reqs       = $runtime > 0 ? number_format(1 / $runtime, 2) : '∞';
        $time_str   = '[运行时间：' . number_format($runtime, 6) . 's][吞吐率：' . $reqs . 'req/s]';
        $memory_use = number_format((memory_get_usage() - THINK_START_MEM) / 1024, 2);
        $memory_str = ' [内存消耗：' . $memory_use . 'kb]';
        $file_load  = ' [文件加载：' . count(get_included_files()) . ']';
        $message = $time_str . $memory_str . $file_load  . $message;
        return $message;
    }

}