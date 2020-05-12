<?php
/**
 * Created by PhpStorm.
 * User: CTDJ1803111
 * Date: 2018/12/11
 * Time: 15:39
 */

namespace app\common\middleware;

use app\common\facade\JWT;
use traits\ResponsDataBuild;
use \Firebase\JWT\ExpiredException;
use  \Exception;

class TokenCheck
{
    use ResponsDataBuild;

    public function handle($request, \Closure $next)
    {
        $params = $request->param();
        $request->member_id = 0;
        $token = $params['access_token'] ?? '';
        if(empty($token)){
            return $this->returnError(22);
        }
        try{
            $data = JWT::decode($token);
            $request->member_id = $data->member_id;
            $request->session_key = $data->session_key;
        }catch (ExpiredException $ex){
            return $this->returnError(24);
        }catch (Exception $ex) {
            return $this->returnError(2,[],$ex->getMessage());
        }
        return $next($request);
    }
}
