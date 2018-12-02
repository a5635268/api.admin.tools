<?php
namespace app\index\controller;

use app\common\controller\Base;


class Publics extends Base
{
    public function miss()
    {
        return '路由不存在';
    }

    public function test()
    {
       $res = service('user');
       dd($res);
    }
}