<?php
require "AbstractPool.php";

class MysqlPoolCoroutine extends AbstractPool
{
    protected $dbConfig = [
        'host'     => '127.0.0.1' ,
        'port'     => 3306 ,
        'user'     => 'root' ,
        'password' => '123456' ,
        'database' => 'test' ,
        'charset'  => 'utf8' ,
        'timeout'  => 10 ,
    ];
    public static $instance;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new MysqlPoolCoroutine();
        }
        return self::$instance;
    }

    protected function createDb()
    {
        $db = new Swoole\Coroutine\Mysql();
        $db->connect(
            $this->dbConfig
        );
        return $db;
    }
}

$httpServer = new swoole_http_server('0.0.0.0' , 9501);
$httpServer->set(
    ['worker_num' => 1]
);
$httpServer->on(
    "WorkerStart" , function (){
    MysqlPoolCoroutine::getInstance()->init();
}
);
$httpServer->on(
    "request" , function ($request , $response){
    $db = null;
    $obj = MysqlPoolCoroutine::getInstance()->getConnection();
    if (!empty($obj)) {
        $db =  $obj['db'];
    }
    if ($db) {
        // 遇上sleep阻塞时，woker进程不是在等待select的完成，而是切换到另外的协程去处理下一个请求。
        $db->query("select sleep(2)");
        $ret = $db->query("select * from tb_game limit 1");
        // 完成后同样释放对象到池中
        MysqlPoolCoroutine::getInstance()->free($obj);
        $response->end(json_encode($ret));
    }
}
);
$httpServer->start();