<?php
namespace app\index\controller;

use app\common\controller\Base;
use app\common\facade\ResponsData;

class Publics extends Base
{
    public function miss()
    {
        return '路由不存在';
    }

    public function test()
    {
        $r = ResponsData::returnSucc('hahahah');
        d($r);
    }
}