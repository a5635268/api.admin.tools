<?php

namespace app\common\middleware;

use app\common\controller\Base;
use think\exception\ValidateException;

/**
 * 全部验证器中间件，验证器命名规则：\app\index\validate\Publics::$scene = $request->action()
 * Class Validate
 * @package app\common\middleware
 */
class Validate extends Base
{
    public function handle($request, \Closure $next)
    {
        $params = $request->param();
        $module = $request->module();
        $controller = ucfirst($request->controller());
        $scene    = $request->action();
        $validate = "app\\" . $module . "\\validate\\" . $controller;
        //仅当验证器存在时 进行校验
        if (class_exists($validate)) {
            $v = $this->app->validate($validate);
            if ($v->hasScene($scene)) {
                try{
                    $this->validate($params, $validate . '.' . $scene);
                }catch (ValidateException $ex){
                    $this->exitJson($this->validateError($ex->getMessage()));
                }

            }
        }
        return $next($request);
    }
}