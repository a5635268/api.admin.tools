<?php
namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;

class Base extends Command
{
    protected function configure()
    {
        $this->setName('base')
            ->setDescription('用于继承');
    }

    protected function execute(Input $input , Output $output)
    {
        $output->info('继承用的base命令,所有自定义命令都要继承该方法，便于后期的扩展和维护');
    }
}