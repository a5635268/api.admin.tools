<?php

namespace RabbitMQ;

use libs\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 *  普通队列调用，无返回
    Client::getInstance('rabbitMQTest')
    ->buildEvent(1,'do_lottery')
    ->send();
 *
 * Rpc队列调用，有返回
    echo Client::getInstance('rabbitMQTest')
    ->buildEvent(1,'do_lottery')
    ->call();
 *
 * Class Client
 * @package RabbitMQ
 */
class Client {

    /**
     * 静态成品变量 保存全局实例
     */
    private static  $_instance = NULL;

    private  $connection;
    private  $channel;
    private $body;

    // 回调队列（Rpc）
    private $callbackQueue;
    // 队列返回 （Rpc）
    private $response;
    // 唯一id （Rpc）
    private $correlationId;
    // 是否持久
    private $durable = true;
    // 是否自动删除
    private $autodelete = false;

    // 路由key
    private $routingKey = 'task.event.rk';
    // rpc模式的是推送到默认交换机
    private $exchange =  'task.event.exchange';
    // 交换机类型
    private $exchangeType = 'direct';
    // 队列名称
    private $queueName = '';

    const MODULE_NAME = 'wp_schedule_guess';

    /**
     * 私有化默认构造方法，保证外界无法直接实例化
     */
    private function __construct() {

        
    }

    /**
     * 静态工厂方法，返还此类的唯一实例
     */
    public static function getInstance($configName = 'rabbitMQ') {
        if (!is_null(self::$_instance)) {
            return self::$_instance;
        }
        $rabbitmqConfig = config($configName);
        $object = new self();
        $object->connection = new AMQPStreamConnection(
            $rabbitmqConfig['host'] , $rabbitmqConfig['port'] ,
            $rabbitmqConfig['login'] , $rabbitmqConfig['password']
        );
        $object->channel = $object->connection->channel();
        self::$_instance = $object;
        return self::$_instance;
    }

    /**
     * 构建事件通知
     * @param $uid
     * @param $eventName
     * do_lottery	 每次竞猜
     * lottery_true	 竞猜正确
     * lottery_false 竞猜错误
     *
     * @param int $count
     * @return RpcClient
     */
    public function buildEvent($uid , $eventName, $count = 1)
    {
        $data = [
            'uid' => $uid,
            'module' => self::MODULE_NAME,
            'event' => $eventName,
            'c' => $count,
            'ts' => NOW
        ];
        return $this->setBoby($data);
    }

    /**
     * 构建消息体
     * @param $data
     * @return $this
     */
    public function setBoby($data)
    {
        $this->body = json_encode($data);
        Log::info(__METHOD__ . __LINE__, 'rabbitMq消息体',$data);
        return $this;
    }


    public function setAutodelete($val = false)
    {
        $this->autodelete = $val;
        return $this;
    }

    public function setDurable($val = true)
    {
        $this->durable = $val;
        return $this;
    }

    /**
     * 重置交换机，路由key，队列名
     * @param $exchangeName
     * @param $routeKey
     * @param string $queueName
     * @return $this
     */
    public function reset($exchangeName, $routeKey, $queueName = '')
    {
        $this->exchange = $exchangeName;
        $this->routeKey = $routeKey;
        $this->queueName = $queueName;
        return $this;
    }

    /**
     * 队列回调
     * @param $response
     */
    public function onResponse($response)
    {
        Log::info(__METHOD__ . __LINE__, '回调响应数据' ,$response->body);
        if($response->get('correlation_id') == $this->correlationId) {
            $this->response = $response->body;
        }
    }

    /**
     * 非即时响应队列发送,交换机要指定
     * @throws \Exception
     */
    public function send()
    {
        if(empty($this->body)){
            exception('缺少消息主体', 1);
        }
        $msg = new AMQPMessage($this->body);
        $channel = self::$_instance->channel;
        $channel->exchange_declare(
            $this->exchange ,
            $this->exchangeType ,
            false ,
            $this->durable ,
            $this->autodelete
        );
        $channel->basic_publish ( $msg , $this->exchange , $this->routingKey );
        Log::info(__METHOD__ . __LINE__, '队列推送完成' , $this->body);
    }

    /**
     * 必须要指定队列的普通发布
     * @throws \Exception
     */
    public function publish()
    {
        if(empty($this->body)){
            exception('缺少消息主体', 1);
        }
        if(empty($this->queueName)){
            exception('请指定队列',1);
        }
        $msg = new AMQPMessage($this->body);

        //声明队列
        $channel = self::$_instance->channel;
        $channel->queue_declare(
            $this->queueName,
            false,
            $this->durable,
            false,
            $this->autodelete
        );

        // 发布
        $channel->basic_publish($msg, $this->exchange, $this->queueName);
        if(IS_CLI){
            echo " [x] Sent '{$msg}!'\n";
        }
        Log::info('普通发布',$this->body);
    }



    /**
     * 即时响应的回调队列调用
     * @return null
     * @throws \Exception
     */
    public function call() {
        if(empty($this->body)){
            exception('缺少消息主体', 1);
        }

        $channel = self::$_instance->channel;

        // 创建一个临时回调队列
        list($this->callbackQueue, ,) = $channel->queue_declare(
            "", false,
            $this->durable,
            true,
            $this->autodelete
        );

        // 作为生产者的同时也要作为消费者接收回调队列传过来的值
        $channel->basic_consume(
            $this->callbackQueue ,
            '' , false , false ,
            false , false ,
            [$this , 'onResponse']
        );

        // 清空上次队列响应
        $this->response = null;
        // 设置唯一标识
        $this->correlationId = uniqid();

        $msg = new AMQPMessage(
            $this->body ,
            [
                'content_type' => 'json',
                'correlation_id' => $this->correlationId ,
                'reply_to'       => $this->callbackQueue
            ]
        );

        // 推送到默认交换机上
        $channel->basic_publish($msg, '' , $this->routingKey);

        while(!$this->response) {
            // 测试一下超时情况
            $channel->wait(null,false,10);
        }
        Log::debug('$this->response',$this->response);
        return  $this->response;
    }

    /**
     * 析构函数，用于关闭队列
     */
    public function __destruct()
    {
        Log::debug(__METHOD__ . __LINE__, '链接关闭' , $this->body);
        self::$_instance->channel->close();
        self::$_instance->connection->close();
    }

}