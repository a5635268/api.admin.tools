<?php

namespace GrpcServer;

use think\Exception;

class ClientServer extends \Grpc\BaseStub
{

    protected $config;
    protected $name;

    public function __construct($name, $opts, $channel, $config)
    {
        $this->config = $config;
        $this->name   = $name;
        $opts         = array_merge($opts, [
            'credentials' => \Grpc\ChannelCredentials::createInsecure()
        ]);
        parent::__construct($config['hostname'], $opts, $channel);
    }

    public function __call($name, $param)
    {
        $num = count($param);
        if ($num <= 0) {
            throw new Exception('最少需要一个参数');
        }
        if ($num > 3) {
            throw new Exception('最多只能有3个参数');
        }
        if ($num < 3) {
            $param = array_merge($param, array_fill($num, 3 - $num, []));
        }
        $response = $this->config['method'][$name]['response'];

        // userservice的原生类生成有点问题，这里做单独区分
        if(strtolower($this->name) == 'userservice'){
            $this->name = 'UserService';
        }
        $method = '/' . $this->name . '/' . $name;
        return $this->_simpleRequest($method,
            $param[0],
            [$response, 'decode'],
            $param[1], $param[2]);
    }


}