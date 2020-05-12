<?php
/**
 * 适配器模式
 * 把多个类的不同方法，都通过接口适配为同个操作方法名（包括参数都一样）;比如在框架中memcache和redis的操作方法其实都一样的，具体用哪个在配置文件中指定一下就可以，不用再改代码；
 */

// 这个是原有的类型
class OldCache{
  public function __construct(){
    echo "OldCache construct<br/>";
  }

  public function store($key , $value){
    echo "OldCache store<br/>";
  }

  public function remove($key){
    echo "OldCache remove<br/>";
  }

  public function fetch($key){
    echo "OldCache fetch<br/>";
  }
}


interface Cacheable{
  public function set($key , $value);

  public function get($key);

  public function del($key);
}

class OldCacheAdapter implements Cacheable{
  private $_cache = null;

  public function __construct(){
    $this->_cache = new OldCache();
  }

  //在适配器之外的方法也增加一个对外的扩展;
  public function __call($method,$args){
    if(methood_exists($this->_cache,$method)){
      return call_user_func_array(array ($this->_cache,$method),$args);
    }
  }

  public function set($key , $value){
    return $this->_cache->store($key , $value);
  }

  public function get($key){
    return $this->_cache->fetch($key);
  }

  public function del($key){
    return $this->_cache->remove($key);
  }
}

$objCache = new OldCacheAdapter();
$objCache->set("test" , 1);
$objCache->get("test");
$objCache->del("test" , 1);