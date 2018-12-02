<?php

namespace GrpcServer;

use think\Exception;

class ClientFactory
{
    public static $config;
    public static $client;

    public function __construct(ConfigLoad $configLoad)
    {
        self::$config = $configLoad;
    }

    public static function createClient($name, $channel = null, $opts = [])
    {
        if (isset(static::$client[$name])) {
            return static::$client[$name];
        }
        if (!static::$config) {
            new static(new ConfigLoad());
        }
        if (!isset(self::$config[$name])) {
            throw new Exception("没有找到这个服务");
        }
        $clientconfig             = self::$config[$name];
        $clientconfig['hostname'] = self::$config->getHostname($name);
        static::$client[$name] = $Client = new ClientServer($name, $opts, $channel, $clientconfig);
        return $Client;
    }
}