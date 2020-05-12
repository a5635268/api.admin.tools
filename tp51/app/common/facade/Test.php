<?php

namespace app\common\facade;

use think\Facade;

/**
 * 通过门面实现静态调用实例的非静态方法
 * Class Test
 * app\common\facade\Test::hello()
 * @package app\common\facade
 */
class Test extends Facade
{
    protected static function getFacadeClass()
    {
        return 'app\common\service\Test';
    }
}