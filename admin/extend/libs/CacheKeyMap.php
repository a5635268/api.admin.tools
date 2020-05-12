<?php

namespace libs;

/**
 * 缓存key映射类：缓存KEY要统一配置，便于后期批量更改和管理
 * 注意其命名规则： 项目名：资源名：类型(key类型以外才有):标识id...
 * Class CacheKeyMap
 * @package libs
 */
class CacheKeyMap
{
    public static $prefix = 'ntms:admin:';

    /**
     * 用户接收消息
     * @param $memberId
     * @return string
     */
    public static function sendMsgSet($memberId)
    {
        return self::$prefix . 'message:send:set:' . $memberId;
    }

    /**
     * 活动的参与人数
     * @param $activitiesId
     * @return string
     */
    public static function shareActivitiesJoinNumKey($activitiesId)
    {
        return self::$prefix . 'share:activities:join_num:' . $activitiesId;
    }

    /**
     * 活动时间开奖的时间有序集合
     */
    public static function shareActivitiesListDrawTimeSet()
    {
        return self::$prefix.'share:activitiesList:drawTime:set';
    }

    /**
     * 抢购票的库存id
     * @param $tiketId
     * @return string
     */
    public static function seckillTicketStockKey($tiketId)
    {
        return self::$prefix . 'seckill:ticket:stock:' . $tiketId;
    }

    /**
     * 接口缓存key
     * @param $api
     * @param $args
     * @return string
     */
    public static function InterfaceCacheKey($api,$args)
    {
        $api = md5($api);
        $args = md5(is_array($args) ? json_encode($args) : $args);
        return self::$prefix . 'interface:cache:' . $api . ':' . $args;
    }

    /**
     * 活动结束分享开奖的时间有序集合
     */
    public static function shareActivitiesListEndTimeShareSet()
    {
        return self::$prefix.'share:activitiesList:endTimeShare:set';
    }

    //微信国庆活动参与人数
    public static function wechatActivitiesJoinNumKey($activitiesId)
    {
        return self::$prefix . 'wechat:activities:join_num:' . $activitiesId;
    }

    //微信国庆活动参与人数集合
    public static function wechatActivitiesJoinSet($activitiesId)
    {
        return self::$prefix . 'wechat:activities:join:set:' . $activitiesId;
    }


}
