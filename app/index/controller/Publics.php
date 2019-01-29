<?php
namespace app\index\controller;

use app\common\controller\Base;
use function GuzzleHttp\Psr7\_parse_request_uri;
use libs\Log;

class Publics extends Base
{
    public function miss()
    {
        return '路由不存在';
    }

    public function test()
    {
        Log::info('test','testt','追踪');
    }
}