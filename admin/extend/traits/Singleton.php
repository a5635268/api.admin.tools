<?php

namespace traits;

/**
 * 实现单例调用
 * Trait Singleton
 * @package traits
 */
trait Singleton
{
    private static $instance;

    static function getInstance(...$args)
    {
        if(!isset(self::$instance)){
            self::$instance = new static(...$args);
        }
        return self::$instance;
    }
}