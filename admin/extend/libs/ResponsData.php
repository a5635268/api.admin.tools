<?php

namespace libs;

use traits\ResponsDataBuild;

class ResponsData{
    use ResponsDataBuild;

    public function __call($method,$args)
    {
        method_exists($this , $method) || exception(__CLASS__ . ':' . $method . '不存在');
        return call_user_func_array([$this , $method] , $args);
    }
}