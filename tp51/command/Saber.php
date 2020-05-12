<?php

namespace app\command;
use app\common\command\Base;
use think\console\Input;
use think\console\Output;
use think\console\input\Argument;
use think\facade\Debug;
use Swlib\SaberGM;
use think\console\input\Option;
use Swoole\Coroutine\Channel as chan;
use GuzzleHttp\Client;

/**
 * https://github.com/swlib/saber
 * 用在爬虫中：
 * 1. 网络代理
 * 2. 自动重试
 * 3. 单次并发控制
 *
 * 用在测试中：
 * 1. 极限压力测试
 * 2. 并发请求
 * Class Saber
 * @package app\command
 */
class Saber extends Base
{
    protected function configure()
    {
        $this->setName('saber')
            ->addArgument('func' , Argument::OPTIONAL , "本命令行的方法名" , "test")
            ->setDescription('swlib/saber');
    }

    protected function execute(Input $input , Output $output)
    {
        $this->output = $output;
        $this->input = $input;
        $func = $input->getArgument('func');
        try {
            if (!method_exists($this , $func)) {
                return $output->error("没有<" . $func . ">方法");
            }
            Debug::remark('begin');
            go([$this , $func]);
            Debug::remark('end');
            $result = '让出CPU: ' . Debug::getRangeTime('begin' , 'end') . 's';
            $this->output->info($result);
        } catch (Exception $ex) {
            echo PHP_EOL;
            d($ex->getMessage());
        }
    }

    private function test2()
    {
        $key = yaconf('tomato.key');
        $res = SaberGM::get('https://api.pomotodo.com/1/account' , ['Authorization' => '']);
        echo $res;
    }

    // 自动重试
    protected function retry()
    {
        $uri = 'http://eu.httpbin.org/basic-auth/foo/bar';
        $res = SaberGM::get(
            $uri , [
                     'exception_report' => 0 ,
                     'retry'            => function (\Swlib\Saber\Request $request){
                         echo "retry..." , $request->_retried_time , PHP_EOL;
                         $request->withBasicAuth('foo' , 'bar');
                     } ,
                     'retry_time'       => 3
                 ]
        );
        echo $res;
    }

    // 拦截器
    protected function intercept()
    {
        /**
         * 执行顺序： bebore -> after -> echo result
         */
        echo SaberGM::get(
            'http://httpbin.org/get' , [
            // 协程触发前执行
            'before' => function (\Swlib\Saber\Request $request){
                $uri = $request->getUri();
                echo "log: request $uri now...\n";
            } ,
            'after'  => function (\Swlib\Saber\Response $response){
                if ($response->success) {
                    echo "log: success!\n";
                } else {
                    echo "log: failed\n";
                }
                echo "use {$response->time}s";
                print_r($response);
            }
        ]
        );
    }
}
