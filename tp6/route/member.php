<?php
/**
 * Created by PhpStorm.
 * User: yanglidong
 * Date: 2020/2/29
 * Time: 10:25
 */
use think\facade\Route;

$func = function(){
    Route::get('/read', 'WxMember/read');// 会员基础信息
    Route::put('/perfectPersonalData', 'WxMember/PerfectPersonalData');//会员信息完善
    Route::get('/integral', 'WxMember/integral');// 会员积分记录表
    Route::get('/integralStatistics', 'WxMember/integralStatistics');// 会员积分记录表
};
Route::group('member',$func)->middleware(app\common\middleware\MobileCheck::class);


$func = function(){
    Route::get('/read', 'DaySign/read');// 会员签到信息
    Route::put('/sign', 'DaySign/sign');//会员签到
};

Route::group('daysign',$func)->middleware(app\common\middleware\MobileCheck::class);


$func = function(){
    Route::get('/memberCoupon/:status', 'Coupon/MemberCoupon')->pattern(['status' => '\d+']);// 优惠券卡包
    Route::get('/read/:id', 'Coupon/read')->pattern(['id' => '\d+']);// 优惠券详情
};
Route::group('coupon',$func)->middleware(app\common\middleware\AuthCheck::class);

$func = function(){
    Route::post('/join/:sa_id', 'ShopActivity/join')->pattern(['sa_id' => '\d+']);// 参与活动
};
Route::group('activity',$func)->middleware(app\common\middleware\MobileCheck::class);

Route::get('activity/index/:status', 'ShopActivity/index')->pattern(['status' => '\d+'])->middleware(app\common\middleware\AuthCheck::class);// 活动列表
Route::get('activity/read/:sa_id', 'ShopActivity/read')->pattern(['sa_id' => '\d+'])->middleware(app\common\middleware\AuthCheck::class);// 活动详情
Route::post('activity/notify', 'ShopActivity/Notify');// 活动支付回调

