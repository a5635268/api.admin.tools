<?php
// 全局中间件定义文件

return [
    // 全局请求缓存
    //\think\middleware\CheckRequestCache::class,
    // 多语言加载
    // \think\middleware\LoadLangPack::class,
    // Session初始化
    // \think\middleware\SessionInit::class

    // 全局签名判断
    // 'signCheck' => app\common\middleware\SignCheck::class,

    // 自动验证,有拿不到controller的bug
    //app\common\middleware\Validate::class,

    // 跨域设置
    \think\middleware\AllowCrossDomain::class
];
