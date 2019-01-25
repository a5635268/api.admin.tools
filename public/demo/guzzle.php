<?php
namespace command;

use app\common\Command\Base;
use libs\Encrypt;
use libs\HttpClient;
use think\console\Input;
use think\console\Output;
use think\console\input\Argument;
use think\console\input\Option;
use libs\Log;

use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Promise;
use think\facade\Debug;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;

class Test extends Base
{
    protected function configure()
    {
        $this->setName('test')
            ->addOption('test', 't', Option::VALUE_NONE, 'this is a value_none option')
            ->addOption('required', null, Option::VALUE_REQUIRED, 'this is a value_required option')
            ->addOption('optional', null, Option::VALUE_OPTIONAL, 'this is a value_optional option')
            // VALUE_IS_ARRAY 暂未支持该方法
            // ->addOption('isarray', null, Option::VALUE_IS_ARRAY, 'this is a value_is_array option')
            // 必选参数一定要在可选参数之前
            // ->addArgument('required', Argument::REQUIRED, "argument::required")
            ->addArgument('optional', Argument::OPTIONAL, "argument::optional")
            // 暂未支持数组
            //->addArgument('array', Argument::IS_ARRAY, " argument::is_array")
            ->setDescription('用于测试');
    }


    protected function execute(Input $input , Output $output)
    {
        return $this->test();
        $arguments =  array_filter($input->getArguments(true));
        if (empty($arguments)) {
            // return $output->error('please enter $arguments ^_^');
        }
        $options = array_filter($input->getOptions(true));
        if (empty($options)) {
            return $output->error('please enter options ^_^');
        }
        try {
            $input->getOption('test') && $this->test();
        } catch (Exception $ex) {
            return Log::err(__METHOD__ , $options , $ex->getMessage());
        }
    }


    private function test()
    {
        return $this->synchronous1();
        return $this->async1();
        return $this->synchronousPost();
        return $this->concurrent2();
        return $this->concurrent1();
        return $this->httpTest();
        return $this->carTop();
    }


    // 并发2,不确定多少个链接时
    private function concurrent2()
    {
        $options = [
            'base_uri' => 'http://xgservice.com' ,
            'timeout'  => 11 ,
        ];
        $client = new Client($options);
        $requests = function ($total) {
            $uri = '/test';
            for ($i = 0; $i < $total; $i++) {
                yield new Request('GET', $uri);
            }
        };
        $pool = new Pool($client, $requests(10), [
            'concurrency' => 10,
            'fulfilled' => function ($response, $index) {
                // this is delivered each successful response
                d([$index],$response);
            },
            'rejected' => function ($reason, $index) {
                // this is delivered each failed request
                echo '-----------------------------';
                d(['error' => $index] , $reason);
                echo '-----------------------------';
            },
        ]);

        // Initiate the transfers and create a promise
        $promise = $pool->promise();

        Debug::remark('begin');
        // Force the pool of requests to complete.
        $promise->wait();
        Debug::remark('end');
        $consuming =  Debug::getRangeTime('begin','end').'s';
        $mem = Debug::getRangeMem('begin','end');
        dd([$consuming,$mem]);
    }


    // 并发1,确定多少个链接时
    private function concurrent1()
    {
        $options = [
            'base_uri' => 'http://xgservice.com' ,
            'timeout'  => 11 ,
        ];
        $client = new Client($options);

        // Initiate each request but do not block
        // 在确定发送多少个连接时，可以这样用
        $promises = [
            'image' => $client->getAsync('/test'),
            'png'   => $client->getAsync('/test'),
            'jpeg'  => $client->getAsync('/test'),
            'webp'  => $client->getAsync('/test')
        ];

        Debug::remark('begin');
        // Wait on all of the requests to complete. Throws a ConnectException
        // if any of the requests fail，如果里面有某个请求错误，就立即抛出
        $results = Promise\unwrap($promises);
        Debug::remark('end');
        $consuming =  Debug::getRangeTime('begin','end').'s';
        $mem = Debug::getRangeMem('begin','end').'kb';
        dd($results['image']);
        dd([$consuming,$mem]);

        // Wait for the requests to complete, even if some of them fail
        // 返回的是Response对象集合
        $results = Promise\settle($promises)->wait();
        dd($results);
    }

    // 异步请求,感觉没什么用
    private function async1()
    {
        $options = [
            'base_uri' => 'http://xgservice.com' ,
            'timeout'  => 12.0 ,
        ];
        $client = new Client($options);
        $promise = $client->getAsync('/test');
        $promise->then(
            function (ResponseInterface $res){
                d($res);
            } ,
            function (RequestException $e){
                echo $e->getMessage() . "\n";
                echo $e->getRequest()->getMethod();
            }
        );
        $promise->wait();
        /* Debug::remark('begin');
         $promise->wait();
         Debug::remark('end');
         $consuming =  Debug::getRangeTime('begin','end').'s';
         $mem = Debug::getRangeMem('begin','end');
         dd([$consuming,$mem]);*/
    }


    private function synchronousPost()
    {
        $options = [
            'base_uri' => 'http://xgservice.com' ,
        ];
        $client = new Client($options);
        $response = $client->request('POST', '/test', [
            'json' => ['foo' => 'bar']
        ]);
        $res = $response->getBody();
        echo $res;die; // 会自动转换为字符串;
        // $res = $res->getContents(); // 手动转也行
    }

    // 同步请求： get
    private function synchronous1()
    {
        $http = new HttpClient(['base_uri' => 'https://www.baidu.com/']);
        $r = $http->get('/test',['hah' => 'hzzz','ddd' => 'ddbbb']);
        dd($r);
    }


    private function httpTest()
    {
        $client = new Client(
            [
                // Base URI is used with relative requests
                'base_uri' => 'http://httpbin.org' ,
                // You can set any number of default request options.
                'timeout'  => 2.0 ,
            ]
        );

    }
}