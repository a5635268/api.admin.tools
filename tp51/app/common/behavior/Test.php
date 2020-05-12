<?php

namespace app\common\behavior;

use libs\Redis;

class Test
{
    // 支持自动注入
    public function run($params, Redis $redis)
    {
/*
        echo '默认的run方法，传对象进来';
        $action = current($params);
        $actionName = end($params);
        $className = get_class($action);
        dump($redis);
        dump($actionName);
*/

    }

    public function actionBegin($params)
    {

/*        echo '标签的驼峰命名法也能触发,并且优先级为最高';
        dump($params);*/
    }

}