<?php

namespace app\common\middleware;
use libs\SnsSigCheck;
use traits\ResponsDataBuild;

class SignCheck
{
    use ResponsDataBuild;

    public function handle($request, \Closure $next)
    {
        $method = $request->method();
        $action = $request->action();
        // GET一般为取数据，这里不验证，因为有转发
        // 表单类的才有签名
        if($method === 'GET' || $action === 'paycallback'){
            return $next($request);
        }
        $params = $request->param();
        $res = SnsSigCheck::verificationData($params);
        if(false == $res){
            return $this->returnError(19);
        }
        return $next($request);
    }
}
