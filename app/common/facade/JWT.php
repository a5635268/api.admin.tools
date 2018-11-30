<?php

namespace app\common\facade;

use think\Facade;

/**
 * Class JWT
 * @package app\common\facade
 * @method mixed decode(\string $token) static 解析token
 * @method mixed encode($data) static 生成token
 */
class JWT extends Facade
{
    protected static function getFacadeClass()
    {
        return 'libs\JWT';
    }
}