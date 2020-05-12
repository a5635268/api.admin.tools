<?php

use think\facade\Route;


// 测试；
Route::post('/test','Publics/test');

Route::post('/upload', 'Publics/upload');    //图片上传

Route::get('/profileData', 'Publics/profileData');    //职业，学历等选项
