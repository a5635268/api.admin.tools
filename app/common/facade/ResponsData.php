<?php

namespace app\common\facade;

use think\Facade;


/**
 * ResponsData, 非controller和model的时候静态调用
 * Class ResponsData
 * @package app\common\facade
 */
class ResponsData extends Facade
{
    protected static function getFacadeClass()
    {
        return 'libs\ResponsData';
    }
}