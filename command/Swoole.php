<?php

namespace app\command;

use app\common\command\Base;
use think\console\Input;
use think\console\Output;
use think\console\input\Argument;
use think\console\input\Option;
use libs\Log;
use Swoole\Http\Server as HttpServer;
use Swoole\WebSocket\Server as WebSocketServer;
use Swoole\Server as SwooleServer;
use Swoole\Client as SwooleClient;
use Swoole\Timer as SwooleTimer;
use Swoole\Coroutine as co;
use Swoole\Coroutine\Channel as chan;
use think\facade\Debug;

class Swoole extends Base
{
    protected $output;
    protected function configure()
    {
        $this->setName('swl')
            ->addArgument('func', Argument::OPTIONAL, "本命令行的方法名","test")
            ->setDescription('this is a description');
    }

    protected function execute(Input $input , Output $output)
    {
        $this->output = $output;
        $func = $input->getArgument('func');
        try {
            if(!method_exists($this,$func)){
                return $output->error("没有<" . $func . ">方法");
            }
            Debug::remark('begin');
            $this->$func();
            Debug::remark('end');
            $result = PHP_EOL . Debug::getRangeTime('begin','end').'s';
            $this->output->info($result) ;

        } catch (Exception $ex) {
            d($ex->getMessage());
        }
    }

    private function test()
    {

    }

    private function udp()
    {
        //创建Server对象，监听 127.0.0.1:9502端口，类型为SWOOLE_SOCK_UDP
        $serv = new SwooleServer("127.0.0.1", 9502, SWOOLE_PROCESS, SWOOLE_SOCK_UDP);

        // 监听数据接收事件
        // UDP服务器与TCP服务器不同，UDP没有连接的概念。
        // 启动Server后，客户端无需Connect，直接可以向Server监听的9502端口发送数据包。
        // 对应的事件为onPacket。
        $serv->on('Packet', function ($serv, $data, $clientInfo) {
            // $clientInfo是客户端的相关信息，是一个数组，有客户端的IP和端口等内容
            $serv->sendto($clientInfo['address'], $clientInfo['port'], "Server ".$data);
            var_dump($clientInfo);
            /**
             * array(4) {
                    ["server_socket"]=> int(6)
                    ["server_port"]=> int(9502)
                    ["address"]=> string(9) "127.0.0.1"
                    ["port"]=> int(54613)
                }
             */
        });

        //启动服务器
        $serv->start();
    }

    private function asyncUdpClient()
    {
        $client = new SwooleClient(SWOOLE_SOCK_UDP, SWOOLE_SOCK_ASYNC);

        //注册连接成功回调
        $client->on("connect", function($cli) {
            $cli->sendto('127.0.0.1', 9502, "hello world " . NOW . " \n");
        });

        //注册数据接收回调
        $client->on("receive", function($cli, $data){
            echo "Received: ".$data."\n";
            fwrite(STDOUT, '请输入消息：');
            $msg = trim(fgets(STDIN));
            $cli->sendto('127.0.0.1', 9502,  $msg . NOW . " \n");
        });


        /**
         * 默认底层并不会启用udp connect，一个UDP客户端执行connect时，底层在创建socket后会立即返回成功。
         * 这时此socket绑定的地址是0.0.0.0，任何其他对端均可向此端口发送数据包。
         * 如$client->connect('192.168.1.100', 9502)，这时操作系统为客户端socket随机分配了一个端口58232，其他机器，如192.168.1.101也可以向这个端口发送数据包。
         * 将第4项参数设置为1，启用udp connect,这时将会绑定客户端和服务器端，底层会根据服务器端的地址来绑定socket绑定的地址。
         */
        $client->connect('127.0.0.1', 9502, 1, 1);
    }

    private function tcp()
    {
        //创建Server对象，监听 127.0.0.1:9501端口, 客户端只能使用127.0.0.1才能连接上
        $serv = new SwooleServer("127.0.0.1", 9501);

        //监听连接进入事件
        $serv->on('connect', function ($serv, $fd) {
            echo "Client: Connect.\n";
        });

        //监听数据接收事件
        $serv->on('receive', function ($serv, $fd, $from_id, $data) {
            $serv->send($fd, "hi！{$from_id} Server: ".$data);
        });

        //监听连接关闭事件
        $serv->on('close', function ($serv, $fd) {
            echo "Client: Close.\n";
        });

        $this->output->writeln("Swoole tcp server started");
        $this->output->writeln('You can exit with <info>`CTRL-C`</info>');
        $serv->set(
            [
                'reactor_num' => 2 ,
                'worker_num'  => 8
            ]
        );
        //启动服务器
        $serv->start();
    }

    private function websocket()
    {
        // 创建websocket服务器对象，监听0.0.0.0:9502端口
        // 为http_swoole的子类
        $server = new WebSocketServer("0.0.0.0", 9502);

        // onOpen事件: 握手成功后触发
        // $request->fd：当前链接的client_id
        $server->on('open', function (WebSocketServer $server, $request) {
            echo "server: handshake success with fd{$request->fd}\n";
        });

        // onMessage事件：收到消息时
        $server->on('message', function (WebSocketServer $server, $frame) {
            echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
            $server->push($frame->fd, "this is server");
        });

        // onClose事件：长链接关闭时
        $server->on('close', function ($ser, $fd) {
            echo "client {$fd} closed\n";
        });

        $server->start();
    }

    private function http()
    {
        $http = new HttpServer("0.0.0.0", 9502);
        $http->on('request', function ($request, $response) {
            $response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
        });
        $http->on('request', function ($request, $response) {
            $response->end("<h1>Hello Swoole2. #".rand(1000, 9999)."</h1>");
        });
        $http->start();
    }

    private function taskClient()
    {
        $client = new SwooleClient(SWOOLE_TCP);
        if (!$client->connect('127.0.0.1', 9501, 0.5))
        {
            die("connect failed.");
        }
        while (true){
            fwrite(STDOUT, '请输入消息：');
            $msg = trim(fgets(STDIN));
            if($msg == 'quite'){
                break;
            }
            //向服务器发送数据
            $client->send($msg);
        }
    }

    private function tcpClient()
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

        fwrite(STDOUT, '请输入消息：');
        $msg = trim(fgets(STDIN));

        //向服务器发送数据
        if (!$client->send($msg))
        {
            return $this->output->error('send failed.');
        }
        //从服务器接收数据
        $data = $client->recv();
        if (!$data)
        {
            return $this->output->error("recv failed.");
        }

        $this->output->writeln($data);

        //关闭连接
        $client->close();
    }


    private function timer()
    {
        /**
         * @param $timeId 定时器的id，可用于swoole_timer_clear清除此定时器
         * @param $param $params 由swoole_timer_tick传入的第三个参数$param，此参数也为可选参数
         */
        $counter = 0;
        $callback = function ($timeId,$param) use(&$counter){
            $counter++;
            var_dump($param);
            echo $timeId . ": {$counter} after 3000ms.\n";
            /**
             * array(1) {
                ["args"]=>
                string(4) "test"
                }
                1: after 3000ms.
             */

            if($counter == 3){
                // int swoole_timer_after(int $after_time_ms, mixed $callback_function);
                // setTimeout
                // 注意：该回调没有参数
                SwooleTimer::after(1500,function () use($counter){
                    echo "在{$counter}时 创建setTimeout after 1500ms. \n";
                });
            }

            if($counter == 5){
                // bool swoole_timer_clear(int $timer_id)
                SwooleTimer::clear($timeId);
            }


        };
        // int swoole_timer_tick(int $msec, callable $callback, [$mixed $param]);
        // 类似setInterval
        SwooleTimer::tick(3000, $callback, ['args' => 'test']);
    }

    private function task()
    {

        $serv = new SwooleServer("127.0.0.1", 9501);

        // 设置异步任务的工作进程数量
        // 配置此参数后将会启用task功能
        // Server务必要注册onTask、onFinish2个事件回调函数。如果没有注册，服务器程序将无法启动。
        // manage进程创建2个worker子进程，4个taskWorker进程
        $serv->set(
            [
                'task_worker_num' => 4,
                'reactor_num' => 2 ,
                'worker_num'  => 2
            ]
        );

        $serv->on('receive', function($serv, $fd, $from_id, $data) {

            echo "recevie client msg: " . $data , PHP_EOL;  // 1

            // 投递异步任务
            // 此函数是非阻塞的，执行完毕会立即返回
            $task_id = $serv->task($data);
            echo "from $from_id Task_id {$task_id} Delivering successful" , PHP_EOL;  // 2
        });

        //处理异步任务
        $serv->on('task', function ($serv, $task_id, $from_id, $data) {
            // 我们可以把IO耗时比较长的业务逻辑写在此处
            // 此时已经在taskWorker里面，已经是同步堵塞的模式了
            // 所以可以用sleep来模拟长时的异步操作
            sleep(10);
            echo "New AsyncTask[id=$task_id]".PHP_EOL; // 3
            //返回任务执行的结果, onFinish中的data参数
            $serv->finish("$data -> OK");
        });

        //处理异步任务的结果
        $serv->on('finish', function ($serv, $task_id, $data) {
            echo "AsyncTask[$task_id] Finish: $data".PHP_EOL;  //4
        });

        $serv->start();
    }


    private function coroutine()
    {
        $c = 5;
        while($c--) {
            go(function () use($c) {
                // 这里使用 sleep 来模拟一个很长的命令
                // 非协程非异步堵塞的情况下，耗时sum(1+2+3+4+5)
                // 协程下，max(1,2,3,4,5)
                co::exec("sleep {$c}");
            });
        }
    }

    private function process()
    {
        $server = new \Swoole\Server('127.0.0.1', 9501);

        /**
         * 自定义用户进程实现广播功能
         * 循环接收管道消息，并发给服务器的所有连接
         */
        $process = new \Swoole\Process(function($process) use ($server) {
            // 用户进程内应当进行while(true)或EventLoop循环，否则用户进程会不停地退出重启
            while (true) {
                $msg = $process->read();
                // 创建的子进程可以调用$server对象提供的各个方法和属性
                foreach($server->connections as $conn) {
                    $server->send($conn, $msg);
                }
            }
        });

        // 在swooleServer中新增进程时，子进程不需要start
        // 在Server启动时会自动创建进程，并执行指定的子进程函数
        $server->addProcess($process);

        $server->on('receive', function ($serv, $fd, $reactor_id, $data) use ($process) {
            //群发收到的消息
            $process->write($data);
        });

        $server->start();
    }

    private function processPool()
    {
        // 设置10个工作进程
        $workerNum = 10;
        $pool = new \Swoole\Process\Pool($workerNum);

        $pool->on("WorkerStart", function ($pool, $workerId) {
            // 得到Process对象，可以使用Process的方法
            $process = $pool->getProcess();
            $process->exec("/bin/sh", ["ls", '-l']);
        });

        $pool->on("WorkerStop", function ($pool, $workerId) {
            echo "Worker#{$workerId} is stopped\n";
        });

        $pool->start();
    }

    private function memory()
    {
        $server = new \Swoole\Http\Server('0.0.0.0', 9500);
        // 4个进程
        $server->set([ 'worker_num'  => 4]);

        // 参数指定表格的最大行数，必须为2的n次方，如果小于1024则默认成1024，即1024是最小值
        // Table基于行锁，所以单次set/get/del在多线程/多进程的环境下是安全的
        // set/get/del等方法是原子操作，用户代码中不需要担心数据加锁和同步的问题
        $table = new \Swoole\Table(8);
        // 字符串类型必须指定第三参数，如果是整型的话，注意溢出
        $table->column('i',$table::TYPE_INT);
        $table->create();
        $table->set('$i',['i' => 1]);
        $server->on('Request', function ($request, $response) use($table) {
            $table->incr('$i','i');
            $response->end($table->get('$i','i'));
        });
        $server->start();
    }
}
