<?php

namespace libs;
use think\swoole\template\Timer;

class TestTimer extends Timer
{

    public function initialize($args)
    {
    }

    public function run()
    {
        // 此处写
        echo '我是定时器模板' , PHP_EOL;
    }
}