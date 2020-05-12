<?php
use think\facade\Env;

return [
    // 加密算法
    'algorithm' => 'HS256',
    // HMAC算法使用的加密字符串
    'key' => 'CgVOOkN5CXoGKY19m',
    // RSA算法使用的私钥文件路径
    'private_key_path' =>  Env::get('config_path') . 'rsa_private_key.pem',
    // RSA算法使用的公钥文件路径
    'public_key_path' => Env::get('config_path') . 'rsa_public_key.pem',
    // 误差时间，单位秒
    'deviation' => 60,
    // access_token过期的时间, 三天过期, 4320
    'access_ttl' =>  259200 ,
    // refresh过期的时间, 单位分钟
    'refresh_ttl' => 21600
];
