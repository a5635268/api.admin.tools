<?php

namespace app\common\middleware;

use app\common\validate\BaseValidate;
use app\home\validate\User\Publics;
use think\exception\ValidateException;

/**
 * 全部验证器中间件，验证器命名规则：\app\index\validate\Publics::$scene = $request->action()
 * Class Validate
 * @package app\common\middleware
 */
class Validate extends BaseValidate
{

    public function handle($request, \Closure $next)
    {
        $controller = ucfirst($request->controller());
        $scene    = $request->action();
        $validate = "app\\validate\\" . str_replace('.','\\',$controller);
        $data = array_merge($request->param(), $request->route());
        //仅当验证器存在时 进行校验
        if (class_exists($validate)) {
            if (validate($validate)->hasScene($scene)) {
                try{
                   $this->validate($data, $validate . '.' . $scene);
                }catch (ValidateException $ex){
                    return $this->validateError($ex->getMessage());
                }
            }
        }
        return $next($request);
    }
}
