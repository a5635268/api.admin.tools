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
