<?php
/**
 * Created by PhpStorm.
 * User: CTDJ1803111
 * Date: 2018/12/11
 * Time: 15:39
 */

namespace app\common\middleware;

use app\common\facade\JWT;
use app\home\model\user\User;
use app\member\model\Member;
use app\model\WxMember;
use \Exception;
use \Firebase\JWT\ExpiredException;
use libs\CacheKeyMap;
use traits\ResponsDataBuild;

class AuthCheck
{
    use ResponsDataBuild;

    public function handle($request, \Closure $next)
    {
        $params = $request->param();
        $token = $params['access_token'] ?? "";
        if(empty($token)){
            $request->member_id = 0;
            return $next($request);
        }
        try{
            $data = JWT::decode($token);
            $request->member_id = $data->member_id;
            $request->session_key = $data->session_key;
            $member = WxMember::where('id', $request->member_id)
                ->cache(true)
                ->find();
            if(!$member || $member->isEmpty()){
                return $this->returnError(2);
            }
            if($member->status == 2){
                return $this->returnError(1002);
            }
        }catch (ExpiredException $ex){
            return $this->returnError(24);
        }catch (Exception $ex) {
            return $this->returnError(2);
        }
        return $next($request);
    }
}
