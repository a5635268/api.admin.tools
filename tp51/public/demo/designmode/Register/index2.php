<?php

class Register{
  protected static $objects;

  public static function set($alias , $object){
    self::$objects[$alias] = $object;
  }

  public static function get($alias){
    return self::$objects[$alias];
  }

  public static function _unset($alias){
    unset(self::$objects[$alias]);
  }
}

Register::set('rand' , RandFactory::factory());
$object = Register::get('rand');
print_r($object);