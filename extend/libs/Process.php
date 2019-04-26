<?php

namespace libs;

class Process
{
    // 主进程的pid
    public $mpid = 0;
    //
    public $works = [];
    // 创建多少个子进程
    public $max_precess = 1;
    public $new_index = 0;

    public function __construct()
    {
        try {
            // 给当前主进程命名
            swoole_set_process_name(sprintf('php-ps:%s' , 'master'));
            // 获得当前主进程的PID
            $this->mpid = posix_getpid();
            // 创建子进程并运行
            $this->run();
            // 运行结束后回收并重启
            $this->processWait();
        } catch (\Exception $e) {
            die('ALL ERROR: ' . $e->getMessage());
        }
    }

    public function run()
    {
        for ($i = 0;$i < $this->max_precess;$i ++) {
            $this->CreateProcess();
        }
    }

    public function CreateProcess($index = null)
    {
        // 创建子进程，用于在检查主进程是否还活着
        $process = new \swoole_process(
            function (\swoole_process $worker) use ($index){

                // 设置进程名称
                if (is_null($index)) {
                    $index = $this->new_index;
                    $this->new_index ++;
                }
                swoole_set_process_name(sprintf('php-ps:%s' , $index));

                // 120秒后重启子进程
                for ($j = 0;$j < 120;$j ++) {
                    $this->checkMpid($worker);
                    echo "msg: {$j}\n";
                    sleep(1);
                }
            } , false , false
        );
        $pid = $process->start();
        $this->works[$index] = $pid;
        return $pid;
    }

    // 检查主进程是否还在
    public function checkMpid(&$worker)
    {
        // 检查主进程是否存在
        if (!\swoole_process::kill($this->mpid , 0)) {
            // 如果不存在的话就退出子进程
            $worker->exit();
            // 这句提示,实际是看不到的.需要写到日志中
            echo "Master process exited, I [{$worker['pid']}] also quit\n";
        }
    }

    public function rebootProcess($ret)
    {
        $pid = $ret['pid'];
        $index = array_search($pid , $this->works);
        if ($index !== false) {
            $index = intval($index);
            // 名称设置为一样
            $new_pid = $this->CreateProcess($index);
            echo "rebootProcess: {$index}={$new_pid} Done\n";
            return;
        }
        throw new \Exception('rebootProcess Error: no pid');
    }

    public function processWait()
    {
        while (1) {
            // 如果子进程存在
            if (count($this->works)) {
                // 等待其结束后回收
                $ret = \swoole_process::wait();
                if ($ret) {
                    // 回收成功后重启子进程
                    $this->rebootProcess($ret);
                }
            } else {
                break;
            }
        }
    }
}