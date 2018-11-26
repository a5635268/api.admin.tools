<?php

namespace libs;
/**
 * 缓存key映射类：缓存KEY要统一配置，便于后期批量更改和管理
 * Author: Zhou xiaogang
 * Date: 2017/11/14
 * Time: 14:15
 * Class CacheKeyMap
 */
class CacheKeyMap
{
    public static $prefix = 'test:';


    /**
     * 战队排行数据列表
     * @param $gameId
     * @return string
     */
    public static function eventTeamRankListKey($gameId,$prefix=0)
    {
        if($prefix){
            return self::$prefix . 'event_team:rank:list:*';
        }
        return self::$prefix . 'event_team:rank:list:' . $gameId;
    }
}