<?php

namespace libs;
use traits\Singleton;

class Di
{
    use Singleton;
    private $container = [];

    public function set($key , $obj , ...$arg): void
    {
        /*
         * 注入的时候不做任何的类型检测与转换
         */
        $this->container[$key] = [
            "obj"    => $obj ,
            "params" => $arg ,
        ];
    }

    function delete($key): void
    {
        unset($this->container[$key]);
    }

    function clear(): void
    {
        $this->container = [];
    }

    /**
     * @param $key
     * @return null
     * @throws \Throwable
     */
    function get($key)
    {
        if(!isset($this->container[$key])){
            return null;
        }
        $obj = $this->container[$key]['obj'];
        $params = $this->container[$key]['params'];
        if (is_object($obj) || is_callable($obj)) {
            return $obj;
        }
        if (!(is_string($obj) && class_exists($obj))){
            return $obj;
        }
        try {
            $this->container[$key]['obj'] = new $obj(...$params);
            return $this->container[$key]['obj'];
        } catch (\Throwable $throwable) {
            throw $throwable;
        }
    }
}