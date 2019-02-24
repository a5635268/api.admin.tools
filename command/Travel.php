<?php

namespace app\command;

use app\common\command\Base;
use think\console\Input;
use think\console\Output;
use think\console\input\Option;
use GuzzleHttp\Client;
use libs\Log;
use think\Db;

class Travel extends Base
{
    protected function configure()
    {
        $this->setName('travel')
            ->addOption('upload', 'u', Option::VALUE_NONE, 'upload data to travel')
            ->addOption('sync', 's', Option::VALUE_NONE, 'this is a value_none option')
            ->setDescription('upload Passenger Flow to Tourist Administration');
    }

    protected function execute(Input $input , Output $output)
    {
        try {
            $options = array_filter($input->getOptions(true));
            if (empty($options)) {
                return $output->error('please enter options ^_^');
            }
            $input->getOption('upload') && $this->uploadTourist();
            $input->getOption('sync') && $this->sync();
        } catch (Exception $ex) {
            return Log::err(__METHOD__ , $ex->getMessage());
        }
    }


    /**
     * 获取客流数量
     * @param null $sDate
     * @param null $eDate
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getPassengerFlow($sDate = null,$eDate = null)
    {
        $options = [
            'base_uri' => 'http://172.28.3.170:9010' ,
        ];
        $client = new Client($options);
        $sDate = $sDate ? : date("Y-m-d" , NOW);
        $eDate = $eDate ? : date("Y-m-d" , NOW);
        $param = [
            "MallIds"   => "e61fcf1ae4d54b9cb53a742580150dea" ,
            "StartTime" => $sDate ,
            "EndTime" => $eDate ,
            "Period" => "0",
            "ReportKey" => "9dbe86a789114d14b711b07cf72803ac"
        ];
        $response = $client->request(
                'POST' , '/interface/flow/MallReport.html' , [
                'json' => $param
            ]
        );
        $res = $response->getBody();
        $data = json_decode($res->getContents(),true);
        if($data['status'] !== 1000){
            Log::error('客流接口请求错误', $res->getContents());
            return 1000;
        }
        return $data['Report'];
    }

    /**
     * 获取舒适程度
     * @param $num
     * @return string
     */
    private function getLevel($num)
    {
        switch ($num){
            case $num <= 25000:
                return '舒适';
            case $num <= 27000:
                return '较舒适';
            case $num <= 28000:
                return '一般';
            default:
                return '拥挤';
        }
    }

    /**
     * 上传旅游局
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function uploadTourist()
    {
        $url = "http://jq.shanghai12301.com/LYJWebService/PassengerInfo/PassengerInfoWebService.asmx?wsdl";
        $params = [
            "stream_context" => stream_context_create(
                [
                    'ssl' => [
                        'verify_peer'      => false ,
                        'verify_peer_name' => false ,
                    ]
                ]
            )
        ];
        $client = new \SoapClient($url,$params);
        $num = $this->getPassengerFlow();
        $time = date("Y-m-d H:i:s", NOW);
        $num = $num[0]['Mall_Stay'];
        $level = $this->getLevel($num);
        $result = $client->UpdatePassengerInfo("ctgc","ctgc123", $num , $level , $time);
        Log::info('旅游局上传结果',$result,$num,$level);
    }

    private function sync()
    {
        $data = $this->getPassengerFlow();
        $data = current($data);
        $insertData = [
            'in_number'  => $data['Mall_Enter'] ,
            'out_number' => $data['Mall_Exit'] ,
            'day' => $data['TimeLabel'],
            'create_time' => date('Y-m-d H:i:s',NOW)
        ];
        $check = Db::connect('ali96')
            ->table('customer_list')
            ->where(['day' => $data['TimeLabel']])
            ->find();
        if($check){
            Db::connect('ali96')
                ->table('customer_list')
                ->where('day', '=' ,$data['TimeLabel'])
                ->update($insertData);
        }else{
            Db::connect('ali96')
                ->table('customer_list')
                ->where('day', '=' ,$data['TimeLabel'])
                ->insert($insertData);
        }
    }
}