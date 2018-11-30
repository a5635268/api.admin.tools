<?php
/**
 * 生成签名类
 *
 * @version 3.0.3
 * @author open.qq.com
 * @copyright © 2012, Tencent Corporation. All rights reserved.
 * @ History:
 *               3.0.3 | nemozhang | 2012-08-28 16:40:20 | support cpay callback sig verifictaion.
 *               3.0.2 | sparkeli | 2012-03-06 17:58:20 | add statistic fuction which can report API's access time and number to background server
 *               3.0.1 | nemozhang | 2012-02-14 17:58:20 | resolve a bug: at line 108, change  'post' to  $method
 *               3.0.0 | nemozhang | 2011-12-12 11:11:11 | initialization
 */

namespace libs;

/**
 * 生成签名类
 */
class SnsSigCheck
{
    /**
     * 生成签名: 后端和后端调用就这个
     * http://wiki.open.qq.com/wiki/%E8%85%BE%E8%AE%AF%E5%BC%80%E6%94%BE%E5%B9%B3%E5%8F%B0%E7%AC%AC%E4%B8%89%E6%96%B9%E5%BA%94%E7%94%A8%E7%AD%BE%E5%90%8D%E5%8F%82%E6%95%B0sig%E7%9A%84%E8%AF%B4%E6%98%8E
     * @param string $method 请求方法 "get" or "post"
     * @param string $url_path
     * @param array $params 表单参数
     * @param string $secret 密钥
     * @return string
     */
    static public function makeSig($method, $url_path, $params, $secret)
    {
        $mk = self::makeSource($method, $url_path, $params);
        $my_sign = hash_hmac("sha1", $mk, strtr($secret, '-_', '+/'), true);
        $my_sign = base64_encode($my_sign);

        return $my_sign;
    }

    static private function makeSource($method, $url_path, $params)
    {
        $strs = strtoupper($method) . '&' . rawurlencode($url_path) . '&';

        ksort($params);
        $query_string = array();
        foreach ($params as $key => $val) {
            array_push($query_string, $key . '=' . $val);
        }
        $query_string = join('&', $query_string);

        return $strs . str_replace('~', '%7E', rawurlencode($query_string));
    }

    /**
     * 验证不通过，返回false; 后端和前端调用就用这个
     * @param $data
     * @return bool
     */
    static public function verificationData($data)
    {
        if(empty($data['sign'])){
            return false;
        }
        $apiSecret = $data['sign'];
        unset($data['sign']);
        krsort($data);
        config('jwt.key','doulong');
        $key = md5(md5(json_encode($data , JSON_UNESCAPED_SLASHES)) . config('jwt.key'));
        if ($apiSecret !== $key) {
            return false;
        }
        $data['sign'] = $apiSecret;
        return $data;
    }
}

// end of script