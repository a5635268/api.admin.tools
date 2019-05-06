<?php
namespace app\index\controller;

use app\common\controller\Base;
use libs\Log;

use Swoole\Client as SwooleClient;
use think\Db;

class Publics extends Base
{

    public function syncClient()
    {
        // 在同步客户端中可以使用`SWOOLE_TCP | SWOOLE_KEEPP`标志，创建的TCP连接在PHP请求结束
        // 或者调用$cli->close时并不会关闭。
        // 下一次执行connect调用时会复用上一次创建的连接
        $client = new SwooleClient(SWOOLE_TCP);

        // 连接到服务器
        // bool $swoole_client->connect(string $host, int $port, float $timeout = 0.5, int $flag = 0)
        if (!$client->connect('127.0.0.1', 9501, 0.5))
        {
            die("connect failed.");
        }
        //向服务器发送数据
        if (!$client->send("hello world " . NOW))
        {
            die("send failed.");
        }
        //从服务器接收数据
        $data = $client->recv();
        if (!$data)
        {
            die("recv failed.");
        }

        echo $data;
        //关闭连接
        $client->close();
    }

    public function miss()
    {
        return '路由不存在';
    }

    public function test()
    {
        return $this->request->param();
    }

    private function log()
    {
        $levelArr = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];
        $level = $levelArr[rand(0,7)];
        Log::$level($_SERVER);
        return $this->returnRight();
    }
}