<?php
/**
 * 装饰器模式Decorator:
 * 动态的给一个对象添加一些额外的职责。就增加功能来说，Decorator模式相比生成子类更为灵活
 *
 ** 实现步骤：
 * 1. 通过抽象构件角色规范需要动态扩展的接口方法；
 * 2. 具体构件角色实现抽象构件角色规范的接口方法；
 * 3. 抽象装饰角色持有指向构件对象的句柄(通过构造方法把构件对象传进来);
 * 4. 然后通过具体的装饰角色通过重写扩展接口方法；
 */

/**
 * 抽象构件(Component)角色：
 * 定义一个对象接口，以规范准备接收附加职责的对象，从而可以给这些对象动态地添加职责。
 */
interface Component {
  /**
   * 示例方法
   */
  public function operation();
}

/**
 * 具体构件(Concrete Component)角色：
 * 定义一个将要接收附加职责的类。
 */
class ConcreteComponent implements Component{

  public function operation() {
    echo 'Concrete Component operation <br />';
  }
}

/**
 * 装饰(Decorator)角色：
 * 持有一个指向Component对象的指针，并定义一个与Component接口一致的接口。
 */
abstract class Decorator implements Component{

  //指向构件对象的指针
  protected  $_component;

  public function __construct(Component $component) {
    $this->_component = $component;
  }

  public function operation() {
    $this->_component->operation();
  }
}

/**
 * 具体装饰(Concrete Decorator)角色：负责给构件对象增加附加的职责。
 *
 * 具体装饰类A
 */
class ConcreteDecoratorA extends Decorator {
  public function __construct(Component $component) {
    parent::__construct($component);
  }

  public function operation() {
    parent::operation();    //调用装饰类的操作
    $this->addedOperationA();   //新增加的操作
  }

  /**
   * 新增加的操作A，即装饰上的功能
   */
  public function addedOperationA() {
    echo 'Add Operation A <br />';
  }
}

/**
 * 具体装饰类B
 */
class ConcreteDecoratorB extends Decorator {
  public function __construct(Component $component) {
    parent::__construct($component);
  }

  public function operation() {
    parent::operation();
    $this->addedOperationB();
  }

  /**
   * 新增加的操作B，即装饰上的功能
   */
  public function addedOperationB() {
    echo 'Add Operation B <br />';
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
    $component = new ConcreteComponent();
    $decoratorA = new ConcreteDecoratorA($component);
    $decoratorB = new ConcreteDecoratorB($decoratorA);

    $decoratorB->operation();
  }

}

Client::main();