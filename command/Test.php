<?php
namespace command;

use app\common\command\Base;
use libs\HttpClient;
use libs\OpenSSLHelper;
use think\console\Input;
use think\console\Output;
use think\console\input\Argument;
use think\console\input\Option;
use libs\Log;
use think\facade\Debug;

class Test extends Base
{
    protected function configure()
    {
        $this->setName('test')
            ->addArgument('func', Argument::OPTIONAL, "argument::optional")
            ->setDescription('用于测试');
    }

    protected function execute(Input $input , Output $output)
    {
        $this->output = $output;
        $this->input = $input;
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

    // php think test pay
    public function pay()
    {

    }

    // php think test search
    public function search()
    {
        $options = [
            'headers' => [
                'Content-Type' => 'application/json;charset=utf-8',
                'user' => 'ktapi',
                'pwd' => '0206D7',
            ]
        ];
        $http = new HttpClient($options);
        $api = '/api/ParkingPay/GetParkingPaymentInfo';
        $data = [
            'parkCode' => 1,
            'plateNo' => 'A12345',
            'cardNo' => null,
            'deviceCode' => null
        ];
        $res = $http->post($api,['data'=>$this->encrypt($data)]);
        print_r($res['data'][0]);
    }

    private function encrypt($data)
    {
        $data = json_encode($data);
        $key = "EE3A000B506B74B222D11484";
        $iv = date('Ymd');
        $openssl = new OpenSSLHelper($key, 'DES-EDE3-CBC', $iv);
        $hexStr = $openssl->encrypt($data);
        return $hexStr;
    }
}
