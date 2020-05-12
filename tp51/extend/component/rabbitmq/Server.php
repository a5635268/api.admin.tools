<?php

namespace RabbitMQ;

use libs\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * 服务端用于测试，后续再封装
 * Class RpcServer
 * @package RabbitMQ
 */
class Server {
    private $connection;
    private $channel;

    const ROUTING_KEY = 'task.event.rk';
    const EXCHANGE = 'task.event.exchange';
    const MODULE_NAME = 'wp_schedule_guess';

    public function __construct() {
        $rabbitmqConfig = config('rabbitMQ');
        $this->connection = new AMQPStreamConnection(
            $rabbitmqConfig['host'] , $rabbitmqConfig['port'] ,
            $rabbitmqConfig['login'] , $rabbitmqConfig['password']
        );
        $this->channel = $this->connection->channel();
    }

    /**
     * 其他交换机的消费者
     */
    public function consumer()
    {
        // 系统创建一个临时队列
        list($queue_name, ,) = $this->channel->queue_declare("", false, false, true, false);
        //绑定临时队列到交换机上,并指定消息的路由 key
        $this->channel->queue_bind($queue_name, self::EXCHANGE , self::ROUTING_KEY);
        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
        $callback = function($msg) {
            echo " [x] Received ", $msg->body, "\n";
        };
        $this->channel->basic_consume($queue_name, '', false, true, false, false, $callback);
        while(count($this->channel->callbacks)) {
            $this->channel->wait();
        }

        $this->channel->close();
        $this->connection->close();
    }


    /**
     * 注意：跑起来就不支持热更新
     * @param string $func
     */
    public function run($func = 'rpctest')
    {

        $this->channel->queue_declare(self::ROUTING_KEY, false, false, false, false);

        echo " [x] Awaiting RPC requestsn";

        $callback = function($req) use($func){
            $json = $req->body;
            Log::info('$json',$json);
            echo " [.] $func(", $json, ")\n";
            $msg = new AMQPMessage(
                $func($json),
                array('correlation_id' => $req->get('correlation_id'))
            );
            $req->delivery_info['channel']->basic_publish(
                $msg, '', $req->get('reply_to'));
            $req->delivery_info['channel']->basic_ack(
                $req->delivery_info['delivery_tag']);
        };

        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume(self::ROUTING_KEY, '', false, false, false, false, $callback);
        while(count($this->channel->callbacks)) {
            $this->channel->wait();
        }
        $this->channel->close();
        $this->connection->close();
    }
}