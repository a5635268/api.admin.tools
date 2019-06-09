<?php
namespace command;

use app\common\command\Base;
use think\console\Input;
use think\console\Output;
use think\console\input\Argument;
use think\console\input\Option;
use libs\Log;
use think\facade\Debug;

// extends Base
class Test extends Base
{
    protected function configure()
    {
        $this->setName('test')
            ->addOption('test', 't', Option::VALUE_NONE, '--test val 只判断是否有这个选项，不取val出来')
            ->addOption('required', null, Option::VALUE_REQUIRED, '--required 以后一定要填值')
            ->addOption('optional', null, Option::VALUE_OPTIONAL, '--optional 该值可填可不填，填的话能取出该值出来','defaultval')
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

    public function test()
    {
        \libs\Test::getInstance('zhou',25)->funck('a','b','c');
    }
}