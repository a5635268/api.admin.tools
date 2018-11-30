<?php
namespace app\index\controller;

use app\common\controller\Base;
use libs\Log;

class Publics extends Base
{
    public function miss()
    {
        return '路由不存在';
    }

    public function test()
    {
        Log::error('hahah','heihei','yayayay');
    }
}