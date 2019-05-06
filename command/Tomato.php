<?php

namespace app\command;
use app\common\command\Base;
use think\console\Input;
use think\console\Output;
use think\console\input\Argument;
use think\console\input\Option;
use think\facade\Debug;
use Swoole\Coroutine\Channel as chan;
use libs\Log;

class Tomato extends Base
{
    protected $startDate = null;
    protected $endDate = null;
    protected $offset = 0;
    protected $limit = 10;
    protected $chan = null;
    protected $isManual = true;

    protected function configure()
    {
        $this->setName('tomato')
            ->addArgument('func' , Argument::OPTIONAL , "本命令行的方法名" , "main")
            ->addOption('start' , 's' , Option::VALUE_OPTIONAL , '开始时间，默认是今天' , null)
            ->addOption('end' , 'e' , Option::VALUE_OPTIONAL , '结束时间，默认是明天' , null)
            ->setDescription('Tomato report');
    }


    protected function execute(Input $input , Output $output)
    {
        $this->output = $output;
        $this->input = $input;
        $func = $input->getArgument('func');
        $start = $input->getOption('start');
        $end = $input->getOption('end');
        $this->startDate = is_null($start) ? date('Y-m-d' , NOW) : $start;
        $this->endDate = is_null($end) ? date('Y-m-d' , strtotime('tomorrow')) : $end;
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
            $this->output->error($ex->getMessage());
        }
    }


    protected function main()
    {
        // 用于计算当日番茄总共用了多少时间，大番茄几个，小番茄几个，非家务番茄几个。
        // 还可以计算一周的运动番茄有几个。随便测试一下tp的表格输出。
        $total = 2;
        $this->chan = new chan($total);
        foreach ([true , false] as $isManual) {
            $this->isManual = $isManual;
            for ($this->offset = 0;$this->offset <= $total;$this->offset ++) {
                go([$this , 'getPomos']);
            }
        }
        while ($pomos = $this->chan->pop(0.5)){
            $func = function () use($pomos){
                foreach ($pomos as $item){
                    $this->insert($item);
                }
                print_r($this->chan->stats());
            };
            go($func);
        }
    }

    private function insert($item)
    {
        $option = yaconf('mysql');
        $insertFunc = function()use($option,$item){
            $swooleMysql = new \Swoole\Coroutine\MySQL();
            $swooleMysql->connect($option);
            $sql = "INSERT INTO  `tomato` (`uuid`, `description`, `start_time`, `end_time`, `duration`, `is_abandoned`, `is_manual`) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $startTime = strtotime($item['local_started_at']);
            $endTime = strtotime($item['local_ended_at']);
            $data = [
                'uuid' => $item['uuid'],
                'description' => $item['description'],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'duration' => intval($endTime-$startTime),
                'is_abandoned' => intval($item['abandoned']),
                'is_manual' => intval($item['manual'])
            ];
            $stmt = $swooleMysql->prepare($sql);
             if ($stmt == false)
             {
                 return var_dump($swooleMysql->errno, $swooleMysql->error);
             }
            $ret2 = $stmt->execute($data);
            if(false == $ret2){
                return var_dump($swooleMysql->errno, $swooleMysql->error);
            }
        };
        go($insertFunc);
    }

    private function getSaber()
    {
        $key = yaconf('tomato.key');
        $options = [
            'base_uri' => 'https://api.pomotodo.com' ,
            'headers'  => [
                'Authorization' => "token " . $key ,
            ]
        ];
        $saber = \Swlib\Saber::create($options);
        return $saber;
    }

    private function getPomos()
    {
        $params = [
            'offset'             => $this->offset == 0 ? 0 : $this->offset * $this->limit ,
            'limit'              => $this->limit ,
            'manual'             => $this->isManual ,
            'started_later_than' => $this->startDate ,
            'ended_earlier_than' => $this->endDate
        ];
        $api = '/1/pomos?' . http_build_query($params);
        try{
            $result = $this->getSaber()->get($api)->getParsedJsonArray();
            $result && $this->chan->push($result);
        }catch (\Throwable $e){
            $this->output->error($e->getMessage());
        }

    }
}
