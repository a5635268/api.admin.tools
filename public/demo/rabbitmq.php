<?php
namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\console\input\Option;
use libs\Log;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use RabbitMQ\RpcClient;

class Test extends Command
{
    protected $config;
    protected function configure()
    {
        $this->config = config('rabbitMQ');
        $this->setName('test')
            ->addOption('t', null, Option::VALUE_NONE, 'this is a test option')
            ->addOption('publish' , '' , Option::VALUE_NONE , 'publish to mq')
            ->addOption('fanout_publish' , '' , Option::VALUE_NONE , 'fanout publish to mq')
            ->addOption('direct_publish' , '' , Option::VALUE_NONE , 'direct publish to mq')
            ->addOption('topic_publish' , '' , Option::VALUE_NONE , 'topic publish to mq')
            ->addOption('rpc_client' , '' , Option::VALUE_NONE , 'rpc client')

            ->addOption('consume' , '' , Option::VALUE_NONE , 'get consume msg')
            ->addOption('fanout_consume' , '' , Option::VALUE_NONE , 'get fanout_consume msg')
            ->addOption('direct_consume' , '' , Option::VALUE_NONE , 'get direct_consume msg')
            ->addOption('topic_consume' , '' , Option::VALUE_NONE , 'get topic_consume msg')
            ->addOption('topic_consume_2' , '' , Option::VALUE_NONE , 'get topic_consume_2 msg')
            ->addOption('rpc_server' , '' , Option::VALUE_NONE , 'rpc server')
            ->setDescription('Here is the remark ');
    }


    protected function execute(Input $input , Output $output)
    {
        $options = array_filter($input->getOptions(true));
        if (empty($options)) {
            return $output->writeln('please enter options ^_^');
        }
        try {
            $input->getOption('publish') && $this->publish();
            $input->getOption('fanout_publish') && $this->fanout_publish();
            $input->getOption('direct_publish') && $this->direct_publish();
            $input->getOption('topic_publish') && $this->topic_publish();
            $input->getOption('rpc_client') && $this->rpc_client();

            $input->getOption('consume') && $this->consume();
            $input->getOption('fanout_consume') && $this->fanout_consume();
            $input->getOption('direct_consume') && $this->direct_consume();
            $input->getOption('topic_consume') && $this->topic_consume();
            $input->getOption('topic_consume_2') && $this->topic_consume_2();
            $input->getOption('rpc_server') && $this->rpc_server();

        } catch (Exception $ex) {
            return Log::err(__METHOD__ , $options , $ex->getMessage());
        }
    }

    protected function rpc_client()
    {
        // 普通队列调用，无返回
        RpcClient::getInstance('rabbitMQTest')
            ->buildEvent(1,'do_lottery')
            ->send();

        // Rpc队列调用，有返回
        //        echo RpcClient::getInstance('rabbitMQTest')
        //            ->buildEvent(1,'do_lottery')
        //            ->call();
    }

    protected function rpc_server()
    {
        $server = new \RabbitMQ\RpcServer();
        $server->run();
    }

    /**
     * 普通生产者
     */
    protected function publish()
    {
        // 连接到 test_host 虚拟主机,每个虚拟主机有自己的队列,交换机...
        $connection = new AMQPStreamConnection($this->config['host'], $this->config['port'], $this->config['login'], $this->config['password']);
        //创建一个 channel
        $channel = $connection->channel();
        //声明 hello 队列
        $channel->queue_declare('hello', false, false, false, false);
        //创建一个消息
        $body = 'Hello World!  ' . NOW ;
        /**
         * 第二参数预定义了一套14个属性，大多数属性很少使用，除了以下几个属性：
         * delivery_mode: 将消息标记为持久性。 (with a value of 2) or transient (1).
         * content_type：用来描述编码的MIME类型。例如，对于常用的JSON编码，将此属性设置为应用程序JSON是一个很好的做法。
         * reply_to：常用的名字一个回调队列。
         * correlation_id：有助于将RPC响应与请求关联起来。
         */
        $msg = new AMQPMessage($body);

        //消息是保存在交换机中的,当消息存放时指定的队列存在,交换机会把消息推送到该队列
        //把消息推送到默认的交换机中,并且告诉交换机要把消息交给 hello 队列
        $channel->basic_publish($msg, '', 'hello');

        echo " [x] Sent '{$body}!'\n";
        $channel->close();
        $connection->close();
    }

    /**
     * 扇形交换机的生产者
     * 发布订阅模式,可以实现一个消息发送到多个队列中, (类似于聊天推送)
     * 在发布消息脚本中,创建一个扇形交换机,把消息推送到交换机,不需要推动到指定的队列中,队列在消费者脚本中创建
     * 消费脚本定义个临时队列,并绑定这个临时队列到交换机中,扇形交换机会把接收到的消息推动到每一个绑定的队列中
     */
    protected function fanout_publish()
    {
        //连接到默认虚拟主机,每个虚拟主机有自己的队列,交换机...
        $connection = new AMQPStreamConnection($this->config['host'], $this->config['port'], $this->config['login'], $this->config['password']);
        //创建一个 channel
        $channel = $connection->channel();

        //创建一个fanout类型交换机,不用申明队列，队列在消费者脚本中临时创建
        $channel->exchange_declare("logs","fanout",false,false,false);

        //创建一个消息
        $body = 'Hello World!  ' . NOW ;
        $msg = new AMQPMessage($body);

        //把消息推送到交换机logs, 不需要推动到指定的队列中, 队列在消费者脚本中创建
        $channel->basic_publish ( $msg , 'logs' );

        echo " [x] Sent '{$body}!'\n";
        $channel->close();
        $connection->close();
    }

    /**
     * 直连交换机的生产者
     * 交换机将会对绑定键（binding key）和路由键（routing key）进行精确匹配，从而确定消息该分发到哪个队列
     */
    protected function direct_publish()
    {
        //连接到默认虚拟主机,每个虚拟主机有自己的队列,交换机...
        $connection = new AMQPStreamConnection($this->config['host'], $this->config['port'], $this->config['login'], $this->config['password']);
        //创建一个 channel
        $channel = $connection->channel();

        //声明 hello 队列
        // $channel->queue_declare('task_event_queue', false, true, false, false);

        // 创建一个直连交换机
        $channel->exchange_declare("task.event.exchange","direct",false,true,false);

        //创建一个消息
        $body = 'Hello World!  ' . NOW ;

        $body = '{"uid":2, "module":"wp_schedule_guess", "event":"do_lottery" , "c":1 , "ts":1521232112}';
        $msg = new AMQPMessage($body);

        //把消息推动到direct_logs交换机,并给消息加上routing_key,让消费者队列来根据 key 接收消息
        // $channel->basic_publish ( $msg , 'direct_logs', "warning" );

        $channel->basic_publish ( $msg , 'task.event.exchange', "task.event.rk" );

        echo " [x] Sent '{$body}!'\n";
        $channel->close();
        $connection->close();
    }

    /**
     * 主题交换机生产者 (相当于直连交换机的基础上增加路由key匹配规则)
     * (星号*) 用来表示一个单词.  比如：*.orange.* 匹配 a.orange.b
     * (井号#) 用来表示任意数量（零个或多个）单词。比如： lazy.# 匹配 lazy.a, lazy.a.b.c
     * 当 * (星号) 和 # (井号) 这两个特殊字符都未在绑定键中出现的时候，此时主题交换机就拥有的直连交换机的行为。
     */
    protected function topic_publish()
    {
        //连接到默认虚拟主机,每个虚拟主机有自己的队列,交换机...
        $connection = new AMQPStreamConnection($this->config['host'], $this->config['port'], $this->config['login'], $this->config['password']);
        //创建一个 channel
        $channel = $connection->channel();

        //创建一个主题交换机
        $channel->exchange_declare("topic_logs","topic",false,false,false);

        //创建一个消息
        $body = 'Hello World!  ' . NOW ;
        $msg = new AMQPMessage($body);

        //把消息推动到topic_logs交换机,并给消息加上路由 key,让消费者队列来根据 key 接收消息
        $channel->basic_publish ( $msg , 'topic_logs', "ali.logs.warning" );

        echo " [x] Sent '{$body}!'\n";
        $channel->close();
        $connection->close();
    }

    /**
     * 普通消费者
     */
    protected function consume()
    {
        // 连接
        $connection = new AMQPStreamConnection($this->config['host'], $this->config['port'], $this->config['login'], $this->config['password']);
        $channel = $connection->channel();

        // 可以运行这个命令很多次，但是只有一个队列会被创建, 在程序中重复将队列重复声明一下是种值得推荐的做法,保证队列存在
        // queue_declare第三参数durable为true: 就代表该队列为持久队列； 注意： 只能设置新队列才生效；已被设置的队列不生效；
        // 设置持久队列，还得设置消费者中的delivery_mode $msg = new AMQPMessage($data, array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT) );
        $channel->queue_declare('hello', false, false, false, false);

        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
        $callback = function($msg) {
            echo " [x] Received ", $msg->body, "\n";
            sleep(rand(5,10));
            echo " [x] Done", "\n";
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };

        //  默认情况下,队列会把消息公平的分配给各个消费者
        //  如果某个消费者脚本处理完成分配给他的消息任务后,会一直空闲
        //  另外一个消费者脚本处理的消息都非常耗时,这就容易导致消费者脚本得不到合理利用,
        //  加入此句话,是告诉队列,取消把消息公平分配到各个脚本,而是那个脚本空闲,就交给它一个消息任务
        //  这样，合理利用到每一个空闲的消费者脚本
        //  设置参数prefetch_count = 1。这告诉RabbitMQ同一个时间最多给一个消费者1个消息；
        $channel->basic_qos(null, 1, null);

        /**
         * basic_consume 方法    从队列中读取数据
         * @param string $queue 指定队列
         * @param string $consumer_tag
         * @param bool $no_local
         * @param bool $no_ack    消费者处理完消息后,是否不需要告诉队列已经处理完成,true 不需要 false 需要,
         * true
        默认情况下,队列会把消息公平分配到各个消费者中,然后一次性把消息交给消费者,如果消费者处理了一半挂了,那么消息就丢失了
         * false
        默认情况下,队列会把消息公平的分配给各个消费者,然后一个一个的把消息分配到消费者脚本中,脚本处理完成后,告诉队列,队列会删除这个消息,并且接着给下一个消息,
        当脚本挂掉,不会丢失消息,队列会把未完成的消息分配给其他消费者
        在 callback 函数中需要加入这句话,处理完后通知队列可以删除消息了
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        未加入这句话,队列不会删除已处理完的消息,当脚本挂掉时,会把分配给当前队列的所有消息再次重新分配给其他队列,会导致消息会重复处理
         */
        $channel->basic_consume('hello', '', false, false, false, false, $callback);

        while(count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    /**
     * 直连交换机的消费者
     */
    protected function direct_consume()
    {
        $server = new \RabbitMQ\RpcServer();
        $server->consumer();
        return false;

        // 连接
        $connection = new AMQPStreamConnection($this->config['host'], $this->config['port'], $this->config['login'], $this->config['password']);
        $channel = $connection->channel();

        // 系统创建一个临时队列
        list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);

        Log::debug('$direct_consume_queue_name',$queue_name);

        //绑定临时队列到交换机上,并指定消息的路由 key
        $channel->queue_bind($queue_name, 'direct_logs', 'error');
        $channel->queue_bind($queue_name, 'direct_logs', 'warning');

        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
        $callback = function($msg) {
            sleep(100);
            echo " [x] Received ", $msg->body, "\n";
        };

        /**
         * basic_consume 方法    从队列中读取数据
         * @param string $queue 指定队列
         * @param string $consumer_tag
         * @param bool $no_local
         * @param bool $no_ack    消费者处理完消息后,是否不需要告诉队列已经处理完成,true 不需要 false 需要,
         * true
        默认情况下,队列会把消息公平分配到各个消费者中,然后一次性把消息交给消费者,如果消费者处理了一半挂了,那么消息就丢失了
         * false
        默认情况下,队列会把消息公平的分配给各个消费者,然后一个一个的把消息分配到消费者脚本中,脚本处理完成后,告诉队列,队列会删除这个消息,并且接着给下一个消息,
        当脚本挂掉,不会丢失消息,队列会把未完成的消息分配给其他消费者
        在 callback 函数中需要加入这句话,处理完后通知队列可以删除消息了
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        未加入这句话,队列不会删除已处理完的消息,当脚本挂掉时,会把分配给当前队列的所有消息再次重新分配给其他队列,会导致消息会重复处理
         */
        $channel->basic_consume($queue_name, '', false, true, false, false, $callback);

        while(count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    /**
     * 扇形交互机的消费者，多队列消费
     */
    protected function fanout_consume()
    {
        // 连接
        $connection = new AMQPStreamConnection($this->config['host'], $this->config['port'], $this->config['login'], $this->config['password']);
        $channel = $connection->channel();

        // 系统创建一个临时队列
        list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);

        Log::debug('$queue_name',$queue_name);

        //绑定临时队列到交换机上
        $channel->queue_bind($queue_name, 'logs');

        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
        $callback = function($msg) {
            echo " [x] Received ", $msg->body, "\n";
        };

        /**
         * basic_consume 方法    从队列中读取数据
         * @param string $queue 指定队列
         * @param string $consumer_tag
         * @param bool $no_local
         * @param bool $no_ack    消费者处理完消息后,是否不需要告诉队列已经处理完成,true 不需要 false 需要,
         * true
        默认情况下,队列会把消息公平分配到各个消费者中,然后一次性把消息交给消费者,如果消费者处理了一半挂了,那么消息就丢失了
         * false
        默认情况下,队列会把消息公平的分配给各个消费者,然后一个一个的把消息分配到消费者脚本中,脚本处理完成后,告诉队列,队列会删除这个消息,并且接着给下一个消息,
        当脚本挂掉,不会丢失消息,队列会把未完成的消息分配给其他消费者
        在 callback 函数中需要加入这句话,处理完后通知队列可以删除消息了
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        未加入这句话,队列不会删除已处理完的消息,当脚本挂掉时,会把分配给当前队列的所有消息再次重新分配给其他队列,会导致消息会重复处理
         */
        $channel->basic_consume($queue_name, '', false, true, false, false, $callback);

        while(count($channel->callbacks)) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }

    /**
     * 主题交换机消费者
     * 基于路由key模式侦听消息
     */
    protected function topic_consume()
    {
        // 连接
        $connection = new AMQPStreamConnection($this->config['host'], $this->config['port'], $this->config['login'], $this->config['password']);
        $channel = $connection->channel();

        // 系统创建一个临时队列
        list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);
        Log::debug('$topic_consume_name',$queue_name);

        //绑定临时队列到交换机上
        $channel->queue_bind($queue_name, 'topic_logs', "baidu.#");

        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
        $callback = function($msg) {
            echo " [x] Received ", $msg->body, "\n";
        };

        /**
         * basic_consume 方法    从队列中读取数据
         * @param string $queue 指定队列
         * @param string $consumer_tag
         * @param bool $no_local
         * @param bool $no_ack    消费者处理完消息后,是否不需要告诉队列已经处理完成,true 不需要 false 需要,
         * true
        默认情况下,队列会把消息公平分配到各个消费者中,然后一次性把消息交给消费者,如果消费者处理了一半挂了,那么消息就丢失了
         * false
        默认情况下,队列会把消息公平的分配给各个消费者,然后一个一个的把消息分配到消费者脚本中,脚本处理完成后,告诉队列,队列会删除这个消息,并且接着给下一个消息,
        当脚本挂掉,不会丢失消息,队列会把未完成的消息分配给其他消费者
        在 callback 函数中需要加入这句话,处理完后通知队列可以删除消息了
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        未加入这句话,队列不会删除已处理完的消息,当脚本挂掉时,会把分配给当前队列的所有消息再次重新分配给其他队列,会导致消息会重复处理
         */
        $channel->basic_consume($queue_name, '', false, true, false, false, $callback);

        while(count($channel->callbacks)) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }

    /**
     * 主题交换机消费者
     */
    protected function topic_consume_2()
    {
        // 连接
        $connection = new AMQPStreamConnection($this->config['host'], $this->config['port'], $this->config['login'], $this->config['password']);
        $channel = $connection->channel();

        // 系统创建一个临时队列
        list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);
        Log::debug('$topic_consume_name',$queue_name);

        //绑定临时队列到交换机上
        $channel->queue_bind($queue_name, 'topic_logs', "ali.#");

        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
        $callback = function($msg) {
            echo " [x] Received ", $msg->body, "\n";
        };

        /**
         * basic_consume 方法    从队列中读取数据
         * @param string $queue 指定队列
         * @param string $consumer_tag
         * @param bool $no_local
         * @param bool $no_ack    消费者处理完消息后,是否不需要告诉队列已经处理完成,true 不需要 false 需要,
         * true
        默认情况下,队列会把消息公平分配到各个消费者中,然后一次性把消息交给消费者,如果消费者处理了一半挂了,那么消息就丢失了
         * false
        默认情况下,队列会把消息公平的分配给各个消费者,然后一个一个的把消息分配到消费者脚本中,脚本处理完成后,告诉队列,队列会删除这个消息,并且接着给下一个消息,
        当脚本挂掉,不会丢失消息,队列会把未完成的消息分配给其他消费者
        在 callback 函数中需要加入这句话,处理完后通知队列可以删除消息了
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        未加入这句话,队列不会删除已处理完的消息,当脚本挂掉时,会把分配给当前队列的所有消息再次重新分配给其他队列,会导致消息会重复处理
         */
        $channel->basic_consume($queue_name, '', false, true, false, false, $callback);

        while(count($channel->callbacks)) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }
}