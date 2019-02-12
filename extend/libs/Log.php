<?php

namespace libs;

use think\facade\Log as TPlog;


/**
 * 自定义日志适配器,多参数记录,先适配TP本身的FILE日志，后续可无缝移植到seaLog日志架构
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
 * Log::info('日志title',__METHOD__,__LINE__,'haha');
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
        // 以高性能日志收集组件seaslog为最优先；
        if(extension_loaded('SeasLog')){
            return self::seasLog($method, $args);
        }
        return TPlog::$method($args);
    }

    private static function seasLog($method, $args){
        $request = app('request');
        $config = config('log.');
        if (empty($config['path'])) {
            $config['path'] = app()->getRuntimePath() . 'log' . DIRECTORY_SEPARATOR;
        }

        \SeasLog::setBasePath($config['path']);
        \SeasLog::setLogger('seasLog');

        $info = [
            'method'    => $request->method(),
            'uri'       => $request->url(),
            'ip'    => $request->ip(),
            'c/a' => $request->controller() . '/' . $request->action(),
        ];
        if($config['append_info']){
            $args = [
                '_sys' => $info,
                '_msg' => $args
            ];
        }
        $args = json_encode($args, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        \SeasLog::log($method,$args);
        // 如果为true的话，马上写入；
        config('app.app_debug') && \SeasLog::flushBuffer();
    }
}