<?php

namespace app\common\controller;

use think\Controller;
use traits\ResponsDataBuild;
use think\Cache;
use libs\CacheKeyMap;
use \libs\SnsSigCheck;
use libs\Log;

/**
 * 基控制器
 * Class Base
 * @package app\common\controller
 */
class Base extends Controller
{
    use ResponsDataBuild;

    protected $model; //数据层
    protected $postData; // 提交的post数据
    protected $failException = true; //验证失败要抛出异常；
    protected $redis; // think\cache\driver\Redis
    protected $curl;

    protected function initialize()
    {
        $this->postData = $this->request->post() ? : '';
//        $this->redis = Cache::init();
//        $this->curl = Curl();
    }


    protected function validate($data, $validate, $message = [], $batch = false, $callback = null)
    {
        // 解决TP5在数据为空时不进行验证的bug,如果后续升级后解决可把该方法撤掉；
        if (empty($data)) {
            if ($this->failException) {
                throw new \think\exception\ValidateException('数据不能为空');
            }
            return $this->validateError('数据不能为空');
        }
        return parent::validate($data, $validate, $message, $batch, $callback);
    }

    // 解决vue跨域的问题可以引用
    protected function crossHeader()
    {
        header('Access-Control-Allow-Origin:*');
        // 响应类型
        header('Access-Control-Allow-Methods:POST');
        // 响应头设置
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
    }

    // redis排重锁
    protected function redisLock($key, $value, $expires)
    {
        //在key不存在时,添加key并$expires秒过期
        $lockRes = $this->redis->handler()->set($key, $value, ['nx', 'ex' => $expires]);
        $lockRes || $this->exitJson($this->returnError(6));
    }
}