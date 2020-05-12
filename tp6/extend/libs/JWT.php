<?php
declare (strict_types = 1);

namespace libs;

use \Firebase\JWT\JWT as FirebaseJWT;
use \Firebase\JWT\BeforeValidException;
use think\facade\Config;

/**
 * json web token manange
 * 请在try...catch环境使用它
 * Class JWT
 * @package libs
 */
class JWT
{
    private $config;

    public function __construct(array $config = [])
    {
        $this->config = array_merge(Config::get('jwt'),$config);
        $algorithm = $this->config['algorithm'];
        if(!in_array(strtoupper($algorithm),['HS256','RS256'])){
            throw new BeforeValidException('not support');
        }
    }

    /**
     * token解析
     * @param $token
     * @return mixed
     */
    public  function decode(string $token)
    {
        FirebaseJWT::$leeway = $this->config['deviation'];
        if(strtoupper($this->config['algorithm']) == 'RS256'){
            $decoded = FirebaseJWT::decode($token , file_get_contents($this->config['private_key_path']) , 'RS256');
        }else{
            $decoded = FirebaseJWT::decode($token , $this->config['key'] , ['HS256']);
        }
        return $decoded->data;
    }

    /**
     * 生成token
     * @param $data
     * @return string
     */
    public function encode(array $data):string
    {
        if(empty($data)){
            throw new BeforeValidException('data is require');
        }
        $token = [
            // 签发时间
            'iat' => NOW,
            // 在什么时间之后该jwt才可用
            'nbf' => NOW ,
            //过期时间-10min
            'exp' => NOW + $this->config['access_ttl'] * 60,
            'data' => $data
        ];
        if(strtoupper($this->config['algorithm']) == 'RS256'){
            $jwt = FirebaseJWT::encode($token, file_get_contents($this->config['public_key_path']) , 'RS256');
        }else{
            $jwt = FirebaseJWT::encode($token, $this->config['key']);
        }
        return $jwt;
    }

    /**
     * 刷新token
     * #todo: 后续实现,原理生成两个token，一个access_token，一个refresh_token,access过期就用refresh来刷新,如果连refresh也过期了才让其从新登陆，现在问题是如何让新的token生成的时候，就token立即失效？ 难道要加入redis的存储介质？
     * @param $token
     * @return bool
     */
    public function refresh($token)
    {
        return $token;
    }
}
