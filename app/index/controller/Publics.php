<?php
namespace app\index\controller;

use app\common\controller\Base;
use app\index\model\Game;

class Publics extends Base
{
    public function miss()
    {
        return '路由不存在';
    }

    public function test()
    {
        echo '测试测试';
    }
}