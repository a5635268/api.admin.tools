<?php

namespace app\common\facade;

use think\Facade;

/**
 * Class JWT
 * @package app\common\facade
 * @method mixed get($url)
 * @method mixed post($url,$data)
 */
class Http extends Facade
{
    protected static function getFacadeClass()
    {
        return 'libs\HttpClient';
    }
}
