<?php
require "AbstractPool.php";

class MysqlPoolPdo extends AbstractPool
{
    //数据库配置
    protected $dbConfig = [
        'host'     => 'mysql:host=127.0.0.1:3306;dbname=test' ,
        'port'     => 3306 ,
        'user'     => 'root' ,
        'password' => '123456' ,
        'database' => 'test' ,
        'charset'  => 'utf8' ,
        'timeout'  => 2 ,
    ];
    public static $instance;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new MysqlPoolPdo();
        }
        return self::$instance;
    }

    protected function createDb()
    {
        return new PDO($this->dbConfig['host'] , $this->dbConfig['user'] , $this->dbConfig['password']);
    }
}

$httpServer = new swoole_http_server('0.0.0.0' , 9501);
$httpServer->set(
    ['worker_num' => 1]
);
$httpServer->on(
    "WorkerStart" , function (){
            // 初始化最少数量(min指定)的连接对象，放进类型为Channel的connections对象中。
            MysqlPoolPdo::getInstance()->init();
        }
);
$httpServer->on(
    "request" , function ($request , $response){
    $db = null;
    // 从channle中pop数据库链接对象出来
    $obj = MysqlPoolPdo::getInstance()->getConnection();
    if (!empty($obj)) {
        $db = $obj['db'];
    }
    if ($db) {
        // 此时如果并发了10个请求，server因为配置了1个worker,所以再pop到一个对象返回时，遇到sleep()的查询，
        // 因为用的连接对象是pdo的查询,是同步阻塞的，所以此时的woker进程只能等待，完成后才能进入下一个请求。
        // 因此，池中的其余连接其实是多余的，同步客户端的请求速度只能和woker的数量有关。
        // ab -c 10 -n 10 http://127.0.0.1:9501/
        $db->query("select sleep(2)");
        $ret = $db->query("select * from tb_game limit 1");
        // 使用完链接对象就回收
        MysqlPoolPdo::getInstance()->free($obj);
        $response->end(json_encode($ret));
    }
}
);
$httpServer->start();