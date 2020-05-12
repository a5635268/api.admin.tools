<?php
namespace app\validate;

use app\common\validate\BaseValidate;

/**
 * Class Validate
 * https://www.kancloud.cn/manual/thinkphp6_0/1037624
 * @package app\daily\validate
 */
class Intergral extends BaseValidate
{
    //定义验证规则
    protected $rule = [
        'intergral_num|积分数量'   => 'require|gt:0' ,
        'gain_way|积分来源' => 'require',
        /**
         *  1消费调整 2运营调整 3玩转停车 4添实商城 5新用户 6完善个人信息
         *  7自助积分 8停车支付 9系统脚本类（珑讯斗地主）
         *  10 先锋盒子消费扫码获取积分 11 先锋盒子消费扫码退还积分
         *  51积分商城 52活动扣减
         */
        'adjust_type|积分类型' => 'require',
    ];

    //定义验证提示
    protected $message = [
    ];

    //定义验证场景要与方法名相同走自动验证
    protected $scene = [
        'inc_dec' =>  ['intergral_num','gain_way','adjust_type'],
    ];

}
