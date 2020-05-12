<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

// 命令都是可以自动注册的，此处只是为了重构原生命令才特别指定的

return [
    'app\\common\\command\\create\\Controller' ,
    'app\\common\\command\\create\\Command' ,
    'app\\common\\command\\create\\Model' ,
    'app\\common\\command\\create\\Validate' ,
    'app\\common\\command\\create\\Middleware' ,
];
