<?php

namespace app\common\service;

use EasyWeChat\Factory;
use libs\Log;
use traits\ResponsDataBuild;

/**
 * author: xiaogang.zhou@qq.com
 * datetime: 2020/5/12 10:07
 * 服务层通过invoke函数调用
 * @package app\common\service
 */
class WxMsg
{
    use ResponsDataBuild;
    protected $wechat;

    public function __construct()
    {
        $config = [
            'app_id' => env('WECHAT.OFFICIAL_ACCOUNT_APPID'),
            'secret' => env('WECHAT.OFFICIAL_ACCOUNT_SECRET'),
            'log' => [
                // 默认使用的 channel，生产环境可以改为下面的 prod
                'default' => 'dev',
                'channels' => [
                    // 测试环境
                    'dev' => [
                        'driver' => 'single',
                        'path' => './runtime/log/easywechat.log',
                        'level' => 'debug',
                    ],
                    // 生产环境
                    'prod' => [
                        'driver' => 'daily',
                        'path' => './runtime/log/easywechat.log',
                        'level' => 'info',
                    ],
                ],
            ],
        ];
        $this->wechat = Factory::officialAccount($config);
    }

    public function send($params)
    {
        try{
            $this->wechat->template_message->send($params);
        } catch (\Exception $ex) {
            dd($ex->getMessage());
            Log::err('模板消息发送错误',$params);
        }
    }


}
