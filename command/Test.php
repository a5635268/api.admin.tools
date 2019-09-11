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
            ->addArgument('func', Argument::OPTIONAL, "方法名")
            ->addArgument('car_num', Argument::OPTIONAL, "车牌号",'A12345')
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

    /**
     * 1. 全现金
     * 2. 全积分
     * 3. 全优惠劵
     * 4. 积分+现金
     * 5. 优惠劵+现金
     * php think test pay
     */
    public function pay()
    {
        $options = [
            'headers' => [
                'user' => 'ktapi',
                'pwd' => '0206D7',
            ]
        ];
        $http = new HttpClient($options);
        $api = '/api/ParkingPay/PayParkingFee';

        $freeDetail[] = [
            "type" => 0, //0:积分抵扣，1:抵扣券
            'money' => '200', // 减免金额，分
            'time' => 0, // 减免时间
            'code' => 2,
        ];
        //$freeDetail = [];
        $data = [
            'parkCode' => '1',
            'orderNo' => '000120190813173553623C793WN',
            'amount' => 100,
            'discount' => 0,
            'payType' => 4,
            'payMethod' => 4,
            'freeMoney' => 200, // 减免金额， 分
            'freeTime' => 0, // 分钟
            'freeDetail' => $freeDetail
        ];
        Log::debug('停车支付',$data);
        // parkingTime: 总停车时间
        $res = $http->post($api,['data'=>$this->encrypt($data)]);
        print_r($res);
    }


    // php think test search
    public function search()
    {
        $carNum = $this->input->getArgument('car_num');
        $options = [
            'headers' => [
                'user' => 'ktapi',
                'pwd' => '0206D7',
            ]
        ];
        $http = new HttpClient($options);
        $api = '/api/ParkingPay/GetParkingPaymentInfo';
        $data = [
            'parkCode' => 3688,
            'plateNo' => $carNum,
            'cardNo' => null,
            'deviceCode' => null
        ];
        $res = $http->post($api,['data'=>$this->encrypt($data)]);
        print_r($res);
    }

    public function list()
    {
        $options = [
            'headers' => [
                'user' => 'ktapi',
                'pwd' => '0206D7',
            ]
        ];
        $http = new HttpClient($options);
        $api = '/api/ParkingInfo/SearchParkingInfo';
        $data = [
            'parkCode' => 1
        ];
        $res = $http->post($api,['data'=>$this->encrypt($data)]);
        print_r($res);
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
