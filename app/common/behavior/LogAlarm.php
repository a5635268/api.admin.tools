<?php

namespace app\common\behavior;

class LogAlarm
{

    #todo: 后续要写进rabbitMq
    public function run($params)
    {
        // 发送邮件通知
        list($type , $message) = $params;

        if ('error' == $type) {
            #todo:
            // mail('admin@mail.com' , '日志报警' , implode(' ' , $message));
        }
    }

}