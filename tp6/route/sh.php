<?php
/**
 * Created by PhpStorm.
 * User: yanglidong
 * Date: 2020/3/4
 * Time: 17:13
 */
use think\facade\Route;


Route::get('sh/index', 'Sh/index');    //商圈信息


$func = function(){
    Route::get('/getProportion', 'Sh/getProportion');    //预计获得积分
    Route::post('/selfHelpIntegral', 'Sh/selfHelpIntegral');    //自助积分提交
    Route::get('/queryIntegral', 'Sh/querySelfHelpIntegral');    //自助积分查询
    Route::get('/getOneIntegral/:si_id', 'Sh/queryOneSelfHelpIntegral')->pattern(['si_id' => '\d+']);    //自助单条详情
};
Route::group('sh',$func)->middleware(app\common\middleware\MobileCheck::class);


$func = function(){
    Route::get('/index', 'ShopActivity/index');    //活动列表
};
Route::group('activity',$func)->middleware(app\common\middleware\MobileCheck::class);