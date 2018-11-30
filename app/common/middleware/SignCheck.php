<?php

namespace app\common\middleware;

use app\common\controller\Base;
use libs\SnsSigCheck;

class SignCheck extends Base
{
    public function handle($request, \Closure $next)
    {
        $params = $request->param();
        $res = SnsSigCheck::verificationData($params);
        if(false == $res){
            $this->exitJson($this->returnError(19));
        }
        return $next($request);
    }
}