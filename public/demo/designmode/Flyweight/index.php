<?php
/**
 * 享元角色(Flyweight):
 * 运用共享技术有效的支持大量细粒度的对象，享元模式变化的是对象的存储开销
 * 相对于其它模式，Flyweight模式在PHP实现似乎没有太大的意义，因为PHP的生命周期就在一个请求，请求执行完了，php占用的资源都被释放。我们只是为了学习而简单做了介绍。
 */


/**
 * 抽象享元(Flyweight)角色：此角色是所有的具体享元类的超类，为这些类规定出需要实现的公共接口。
 */
abstract class Flyweight {

  /**
   * 示意性方法
   * @param string $state 外部状态
   */
  abstract public function operation($state);
}

/**
 * 具体享元(ConcreteFlyweight)角色：
 * 实现Flyweight接口，并为内部状态（如果有的话）拉回存储空间。
 * ConcreteFlyweight对象必须是可共享的。它所存储的状态必须是内部的
 */
class ConcreteFlyweight extends Flyweight {

  private $_intrinsicState = null;

  /**
   * 构造方法
   * @param string $state  内部状态
   */
  public function __construct($state) {
    $this->_intrinsicState = $state;
  }

  public function operation($state) {
    echo 'ConcreteFlyweight operation, Intrinsic State = ' . $this->_intrinsicState
      . ' Extrinsic State = ' . $state . '<br />';
  }

}

/**
 * 不共享的具体享元（UnsharedConcreteFlyweight）角色：并非所有的Flyweight子类都需要被共享。Flyweigth使共享成为可能，但它并不强制共享。
 */
class UnsharedConcreteFlyweight extends Flyweight {

  private $_flyweights;

  /**
   * 构造方法
   * @param string $state  内部状态
   */
  public function __construct() {
    $this->_flyweights = array();
  }

  public function operation($state) {
    foreach ($this->_flyweights as $flyweight) {
      $flyweight->operation($state);
    }
  }

  public function add($state, Flyweight $flyweight) {
    $this->_flyweights[$state] = $flyweight;
  }

}

/**
 * 享元工厂(FlyweightFactory)角色：负责创建和管理享元角色。本角色必须保证享元对象可能被系统适当地共享
 */
class FlyweightFactory {

  private $_flyweights;

  public function __construct() {
    $this->_flyweights = array();
  }

  public function getFlyweigth($state) {
    if (is_array($state)) { //  复合模式
      $uFlyweight = new UnsharedConcreteFlyweight();
      foreach ($state as $row) {
        $uFlyweight->add($row, $this->getFlyweigth($row));
      }
      return $uFlyweight;
    }

    if (is_string($state)) {
      if (isset($this->_flyweights[$state])) {
        return $this->_flyweights[$state];
      } else {
        return $this->_flyweights[$state] = new ConcreteFlyweight($state);
      }
    }

    return null;
  }

}

/**
 * 客户端
 */
class Client {

  /**
   * Main program.
   */
  public static function main() {
    $flyweightFactory = new FlyweightFactory();
    $flyweight = $flyweightFactory->getFlyweigth('state A');
    $flyweight->operation('other state A');

    $flyweight = $flyweightFactory->getFlyweigth('state B');
    $flyweight->operation('other state B');

    /* 复合对象*/
    $uflyweight = $flyweightFactory->getFlyweigth(array('state A', 'state B'));
    $uflyweight->operation('other state A');
  }

}

Client::main();