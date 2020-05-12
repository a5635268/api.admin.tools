<?php

return [
    'host' =>  Env::get('rabbitmq.hostname', '172.28.3.45'),
    'port' => Env::get('rabbitmq.port', '5672'),
    'login' => Env::get('rabbitmq.login', 'test'),
    'password' => Env::get('rabbitmq.login', 'test'),
    'vhost' =>  Env::get('rabbitmq.vhost', '/')
];