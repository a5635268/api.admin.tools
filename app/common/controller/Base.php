<?php

namespace app\common\controller;

use think\Controller;
use traits\ResponsDataBuild;

/**
 * 基控制器
 * Class Base
 * @package app\common\controller
 */
class Base extends Controller
{
    use ResponsDataBuild;

    //验证失败要抛出异常；
    protected $failException = true;

    public function __construct()
    {
        parent::__construct();
    }

    protected function validate($data , $validate , $message = [] , $batch = false , $callback = null)
    {
        // 解决TP5在数据为空时不进行验证的bug,如果后续升级后解决可把该方法撤掉；
        if (empty($data)) {
            if ($this->failException) {
                throw new \think\exception\ValidateException('数据不能为空');
            }
            return $this->validateError('数据不能为空');
        }
        return parent::validate($data , $validate , $message , $batch , $callback);
    }

    // 解决vue跨域的问题可以引用
    // #todo 跨域通过中间件解决
    protected function crossHeader()
    {
        header('Access-Control-Allow-Origin:*');
        // 响应类型
        header('Access-Control-Allow-Methods:POST');
        // 响应头设置
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
    }
}