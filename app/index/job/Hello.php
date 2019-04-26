<?php
// 消费者类
namespace app\index\job;

use think\queue\Job;

class Hello {

    /**
     * fire方法是消息队列默认调用的方法
     * @param Job            $job      当前的任务对象
     * @param array|mixed    $data     发布任务时自定义的数据
     */
    public function fire(Job $job,$data)
    {
        // 有些消息在到达消费者时,可能已经不再需要执行了
        $isJobStillNeedToBeDone = $this->checkDatabaseToSeeIfJobNeedToBeDone($data);
        if(!$isJobStillNeedToBeDone){
            $job->delete();
            return;
        }

        $isJobDone = $this->doHelloJob($data);

        if ($isJobDone) {
            // 如果任务执行成功， 记得删除任务
            $job->delete();
            print("<info>Hello Job has been done and deleted"."</info>\n");
        }else{
            if ($job->attempts() > 3) {
                //通过这个方法可以检查这个任务已经重试了几次了
                print("<warn>Hello Job has been retried more than 3 times!"."</warn>\n");

                $job->delete();

                // 也可以重新发布这个任务
                //print("<info>Hello Job will be availabe again after 2s."."</info>\n");
                //$job->release(2); //$delay为延迟时间，表示该任务延迟2秒后再执行
            }
        }
    }

    public function failed($data){
        // ...任务达到最大重试次数后，失败了
        // 记录日志
    }

    /**
     * 有些消息在到达消费者时,可能已经不再需要执行了
     * @param array|mixed    $data     发布任务时自定义的数据
     * @return boolean                 任务执行的结果
     */
    private function checkDatabaseToSeeIfJobNeedToBeDone($data){
        return true;
    }


    private function doHelloJob($data) {
        // 根据消息中的数据进行实际的业务处理...

        print("<info>Hello Job Started. job Data is: ".var_export($data,true)."</info> \n");
        print("<info>Hello Job is Fired at " . date('Y-m-d H:i:s') ."</info> \n");

        sleep(20);

        print("<info>Hello Job is Done!"."</info> \n");

        return true;
    }
}