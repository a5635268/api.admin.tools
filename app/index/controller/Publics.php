<?php
namespace app\index\controller;

use app\common\controller\Base;
use app\common\facade\JWT;

class Publics extends Base
{
    public function miss()
    {
        return '路由不存在';
    }

    public function test()
    {
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NDM0MDIxNzYsIm5iZiI6MTU0MzQwMjE3NiwiZXhwIjoxNTQzNDA5Mzc2LCJkYXRhIjp7InVpZCI6MSwidXNlcm5hbWUiOiJ6aG91eGlhb2dhbmcifX0.1Bydn7fvF7ui422SX7SaV2HQ1ML1i-Og_2BpmUvCQoE';
        $res = JWT::decode($token);

        header("Content-type: text/html; charset=utf-8");
        echo "<pre>";
        print_r($res);
        echo "<pre/>";
        die;


        $token = $this->encode();
        $decoded = JWT::decode($token , KEY , ['HS256']);
        print_r($decoded);
    }


    public function encode()
    {
        $token = [
            'iat' => NOW, //签发时间
            'nbf' => NOW , //在什么时间之后该jwt才可用
            'exp' => NOW + 6000, //过期时间-10min
            'data' => [
                'userid' => 1,
                'username' => 'zhouxiagoang'
            ]
        ];
        $jwt = JWT::encode($token, KEY);
        return $jwt;
    }
}