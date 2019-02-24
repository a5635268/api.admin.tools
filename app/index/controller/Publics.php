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
        echo 2222;die;
        Log::info('hahahaha');
    }
}