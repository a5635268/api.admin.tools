<?php
declare (strict_types = 1);

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
     * 短信验证间隔缓存key
     * @param $mobile
     * @return string
     */
    public static function smsIntervalKey(string $mobile):string
    {
        return self::$prefix . "smsInterval:" . $mobile;
    }

    /**
     * 短信验证有效时间key
     * @param $mobile
     * @return string
     */
    public static function smsValidkey(string $mobile):string
    {
        return self::$prefix . 'smsValid:' . $mobile;
    }

    /**
     * 兑换加锁
     * author: xiaogang.zhou@qq.com
     * datetime: 2020/2/27 16:32
     * @param $memberId
     * @return string
     */
    public static function goodsExchangeLockKey($memberId)
    {
        return self::$prefix . 'goods:exchange:' . $memberId;
    }

    /**
     * 订单支付之后，回调通知
     * @param $orderNo
     * @param $prepayId
     * @return string
     */
    public static function orderPrepayIdKey($orderNo)
    {
        return self::$prefix . 'message:' . $orderNo ;
    }

    /**
     * 生日券已发送集合
     * 有效时间两个月
     * @param $year
     */
    public static function birthdaySet($year)
    {
        return self::$prefix . 'birthday:' . $year ;
    }

}
