<?php

namespace libs;

class ProcessTest
{
    public function __construct(string $processName,$arg = null)
    {
        $this->arg = $arg;
        $this->processName = $processName;
        $this->swooleProcess = new \swoole_process([$this,'__start']);
    }

    public function __start(Process $process)
    {
        if(PHP_OS != 'Darwin'){
            $process->name($this->getProcessName());
        }
        try{
            $this->run($this->arg);
        }catch (\Throwable $throwable){
            echo $throwable->getMessage();
        }
    }

   public function run($arg)
    {
        while (1){
            \co::sleep(5);
            echo 22222 . PHP_EOL;
        }
    }
}