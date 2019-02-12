<?php
namespace command;

use app\common\Command\Base;
use think\console\Input;
use think\console\Output;
use think\console\input\Argument;
use think\console\input\Option;
use libs\Log;

use app\index\model\Game;

// extends Base
class Test
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


    public function test()
    {

    }
}