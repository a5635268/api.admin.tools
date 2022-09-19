<?php

namespace app\common\middleware;
use Closure;
use think\facade\Env;
use traits\ResponsDataBuild;

// 建议再修改一下sign的，sign的更好。
class ApiVerify
{
    use ResponsDataBuild;

    const INTERVAL = 3; //秒

    public function handle($request, Closure $next)
    {
        $apiSecret = Env::get('API_SECRET', '');
        $ptoken = $request->header('p-token');
        if($ptoken == $apiSecret){
            // 调试工具调试的时候可以直接过
            return $next($request);
        }
        $postData = $request->post();
        if (empty($postData) || !isset($postData['_sign']) || !isset($postData['_time'])) {
            return $this->returnError(19);
        }
        $inputApiSecret = $postData['_sign'];
        $inputTime = $postData['_time'];
        if (NOW - $inputTime > self::INTERVAL) {
            return $this->returnError(19);
        }
        unset($postData['_sign'], $postData['_time']);
        array_walk($postData, function (&$item) {
            $item = is_array($item) ? json_encode($item) : (string) $item;
        });
        // 先简单加密后面改成auth认证的
        $key = md5(md5(json_encode($postData , 320)) .$apiSecret);
        if ($inputApiSecret !== $key) {
            Log::err('认证授权失败', $postData, $key);
            return $this->returnError(19);
        }
        return $next($request);
    }
}
