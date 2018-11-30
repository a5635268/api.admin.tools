<?php

namespace libs;

/**
 * 缓存key映射类：缓存KEY要统一配置，便于后期批量更改和管理
 * 注意其命名规则： 项目名：模块名：名称：类型   tkmall:mem:uid:hash
 * Class CacheKeyMap
 * @package libs
 */
class CacheKeyMap
{
    public static $prefix = 'tkmall:';

    /**
     * 基于会员uid的hash
     * @param $uid
     * @param int $prefix
     * @return string
     */
    public static function memberUidHash($uid,$prefix=0)
    {
        if($prefix){
            return self::$prefix . 'mem:' . $uid .':*';
        }
        return self::$prefix . 'mem:' . $uid .':hash';
    }
}