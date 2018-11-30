<?php

namespace libs;

/**
 * 缓存key映射类：缓存KEY要统一配置，便于后期批量更改和管理
 * 注意其命名规则： 项目名：模块名：名称：类型  tkmall:member:username:str
 * Class CacheKeyMap
 * @package libs
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