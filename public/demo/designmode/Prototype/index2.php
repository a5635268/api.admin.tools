<?php

/**
 * 实现：
 * 通过抽象原型约束具体原型的拷贝方法，通过具体原型深浅拷贝出不同实例；可以加入原型管理器，用于创建原型实例并记录原型实例（注册器）
 *
 * 原理：
 * 原型模式可以快速的创建一个对象而不需要提供专门的new()操作就可以快速完成对象的创建，这无疑是一种非常有效的方式，快速的创建一个新的对象。
 * 有一个基础对象，就做简单的修改就成为别的对象可以用
 *
 * 深拷贝与浅拷贝：
 * 浅拷贝：类似引用赋值，共用一个内存地址
 * 深拷贝：类似普通复制，指向不同的内存地址
*/

/**
 * 抽象原型角色
 */
interface Prototype {
  public function copy();
}

/**
 * 具体原型角色
 */
class ConcretePrototype implements Prototype{

  private  $_name;

  public function __construct($name) {
    $this->_name = $name;
  }

  public function setName($name) {
    $this->_name = $name;
  }

  public function getName() {
    return $this->_name;
  }

  public function copy() {
    /** 深拷贝 */
    return  clone  $this;

    /** 浅拷贝 */
    //return  $this;
  }
}


class Client {
  /**
   * Main program.
   */
  public static function main() {
    $object1 = new ConcretePrototype(11);
    $object_copy = $object1->copy();

    var_dump($object1->getName()); //11
    var_dump($object_copy->getName()); //11

    $object1->setName(22);
    var_dump($object1->getName()); //22
    var_dump($object_copy->getName()); //11
  }
}

Client::main();