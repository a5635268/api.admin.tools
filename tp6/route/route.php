<?php
use think\facade\Route;

$miss = function (){
    $arr = [
        'code'    => -1 ,
        'msg' => '请输入正确的路由地址' ,
        'data'    => []
    ];
    return json($arr);
};
// 主页路由
Route::miss($miss);


Route::get('public/test', 'Publics/test');


// 只需要验证token的放在一起
$func = function(){
    // 发送短信
    Route::post('member/sms', 'Login/sendSms');
    // 会员信息更新
    Route::put('member/info', 'Login/info');
    // 第三方手机登录
    Route::post('login/otherMobile', 'Login/otherMobile');
    // 本地手机登录
    Route::post('login/localMobile', 'Login/localMobile');
    // 会员协议
    Route::get('member/protocol', 'Login/protocol')->cache(true);
};
Route::group('',$func)->middleware(app\common\middleware\TokenCheck::class);
Route::get('banner', 'Banner/query')->cache(true);


// 商品分类
Route::get('integralshop/category', 'integralShop/category')->middleware(app\common\middleware\AuthCheck::class);
// 商品
Route::get('integralshop/goods/', 'integralShop/goods')->middleware(app\common\middleware\AuthCheck::class);
// 商品详情
Route::get('integralshop/goods/:id', 'integralShop/goodsDetail')->pattern(['id' => '\d+'])->middleware(app\common\middleware\AuthCheck::class);

// 积分商城相关
$func = function(){
    // 积分
    Route::get('integers', 'integralShop/integer');
    // 兑换商品
    Route::post('exchange', 'integralShop/exchange');
    // 订单详情
    Route::get('order/:order_id', 'integralShop/orderDetail')->pattern(['order_id' => '\d+']);
    // 商城订单
    Route::get('order', 'integralShop/order');
};
Route::group('integralshop',$func)->middleware(app\common\middleware\MobileCheck::class);

// 获取session
Route::post('nologin/', 'Login/nologin');
Route::post('login/', 'Login/session');
Route::get('wxlogin/', 'Login/wxLogin');


Route::post('order/payCallback', 'IntegralShop/orderCallBack');

// 微信卡包
Route::rule('wechat/receive','Wechat/receive');

$func = function (){
    Route::get('cardJson','Wechat/cardJson');
    Route::get('haveCard','Wechat/haveCard');
    Route::get('received','Wechat/received');
};
Route::post('wechat/cardActivate','Wechat/cardActivate');
Route::group('wechat',$func)->middleware(app\common\middleware\MobileCheck::class);

// 泰客猫后台调用
Route::post('admin/birthdayCard','WxMember/birthdayCard');
