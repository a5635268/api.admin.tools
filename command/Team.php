<?php

namespace command;

use app\common\command\Base;
use think\console\Input;
use think\console\Output;
use think\console\input\Argument;
use think\console\input\Option;
use libs\Log;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Team extends Base
{
    private $config;
    private $queueName = 'team-queue';

    protected function configure()
    {
        $this->config = config('rabbitmq.');
        $this->setName('Team')
            ->addArgument('team_id', Argument::OPTIONAL, "argument::team_id")
            ->addOption('publish', 'p', Option::VALUE_NONE, 'this is a publish')
            ->addOption('consume', 'c', Option::VALUE_NONE, 'this is a consume')
            ->addOption('test', 't', Option::VALUE_NONE, 'this is a test')
            ->setDescription('this is a description');
    }

    protected function execute(Input $input , Output $output)
    {
        $options = array_filter($input->getOptions(true));
        if (empty($options)) {
            return $output->warning('please enter options ^_^');
        }
        $teamId = $input->getArgument('team_id');
        try {
            $input->getOption('test') && $this->test();
            if($input->getOption('publish')){
                if(empty($teamId)){
                    return $output->warning('please enter team_id ^_^');
                }
                $this->publish($teamId);
            }
            $input->getOption('consume') && $this->consume();
        } catch (Exception $ex) {
            return Log::err(__METHOD__ , $options , $ex->getMessage());
        }
    }


    /**
     * 普通生产者
     */
    protected function publish($teamId)
    {
        $connection = new AMQPStreamConnection($this->config['host'], $this->config['port'], $this->config['login'], $this->config['password']);
        $channel = $connection->channel();
        $channel->queue_declare($this->queueName, false, true, false, false);

        //创建一个消息
        $body = [
            'team_id' => $teamId,
            'ts'    => NOW
        ];
        $body = json_encode($body);
        $msg = new AMQPMessage($body);
        $channel->basic_publish($msg, '', $this->queueName);

        echo " [x] Sent '{$body}!'\n";
        $channel->close();
        $connection->close();
    }

    /**
     * 普通消费者
     */
    protected function consume()
    {
        // 链接
        $connection = new AMQPStreamConnection($this->config['host'], $this->config['port'], $this->config['login'], $this->config['password']);
        $channel = $connection->channel();
        $channel->queue_declare($this->queueName, false, true, false, false);

        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
        $callback = function($msg) {
            echo " [x] Received ", $msg->body, "\n";
            Log::debug($msg->body);
            echo " [x] Done", "\n";
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };
        $channel->basic_qos(null, 1, null);
        $channel->basic_consume($this->queueName, '', false, false, false, false, $callback);
        while(count($channel->callbacks)) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }
}
