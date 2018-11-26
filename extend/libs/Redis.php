<?php

namespace libs;
use think\cache\driver\Redis as tpRedis;

/**
 * 定制化的redis
 * Class Redis
 * @package libs
 */
class Redis extends tpRedis{

    protected static $_instance = null;

    /**
     * 获取单例redis对象，一般用此方法实例化
     * @return Redis|null
     */
    public static function getInstance()
    {
        if(!is_null(self::$_instance)){
            return self::$_instance;
        }
        self::$_instance = new self();
        return self::$_instance;
    }

    /**
     * 架构函数
     * Redis constructor.
     */
    public function __construct()
    {
        $options = config('cache.redis');
        $options['prefix'] = CacheKeyMap::$prefix;
        parent::__construct($options);
    }

    /**
     * 覆写，实际的缓存标识以CacheKeyMap来管理
     * @access protected
     * @param  string $name 缓存名
     * @return string
     */
    protected function getCacheKey($name)
    {
        return $name;
    }

    /**
     * redis排重锁
     * @param $key
     * @param $expires
     * @param int $value
     * @return mixed
     */
    public function redisLock($key, $expires, $value = 1)
    {
        //在key不存在时,添加key并$expires秒过期
        return $this->handler()->set($key, $value, ['nx', 'ex' => $expires]);
    }

    /**
     * 调用缓存类型自己的高级方法
     * @param $method
     * @param $args
     * @return mixed|void
     * @throws \Exception
     */
    public function __call($method,$args){
        if(method_exists($this->handler, $method)){
            return call_user_func_array(array($this->handler,$method), $args);
        }else{
            exception(__CLASS__.':'.$method.'不存在');
            return;
        }
    }
}