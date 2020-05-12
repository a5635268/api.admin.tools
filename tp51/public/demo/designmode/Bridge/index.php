<?php

/**
 * 抽象化(Abstraction)角色：
 * 给出的定义，并保存一个对实现化对象的引用接口。抽象化角色是对底层实现化角色的表面操作，客户端只需和抽象化角色交互既可
 */
abstract class Abstraction{
  /* 对实现化对象的引用 */
  protected $imp;

  /**
   * 某操作方法
   */
  public function operation(){
    $this->imp->operationImp();
  }
}

/**
 * 修正抽象化(Refined Abstraction)角色:
 * 扩展抽象化角色，改变和修正父类对抽象化的定义。
 */
class RefinedAbstraction extends Abstraction{
  public function __construct(Implementor $imp){
    $this->imp = $imp;
  }

  /**
   * 操作方法在修正抽象化角色中的实现
   */
  public function operation(){
    echo 'RefinedAbstraction operation  ';
    $this->imp->operationImp();
  }
}

/**
 * 实现化(Implementor)角色：
 * 定义实现类的接口，不给出具体的实现。此接口不一定和抽象化角色的接口定义相同，实际上，这两个接口可以完全不同。实现化角色应当只给出底层操作，而抽象化角色应当只给出基于底层操作的更高一层的操作。
 */
abstract class Implementor{
  /**
   * 操作方法的实现化声明
   */
  abstract public function operationImp();
}

/**
 * 具体化角色A
 * 给出实现化角色接口的具体实现
 */
class ConcreteImplementorA extends Implementor{
  /**
   * 操作方法的实现化实现
   */
  public function operationImp(){
    echo 'Concrete implementor A operation <br />';
  }
}

/**
 * 具体化角色B
 * 给出实现化角色接口的具体实现
 */
class ConcreteImplementorB extends Implementor{
  /**
   * 操作方法的实现化实现
   */
  public function operationImp(){
    echo 'Concrete implementor B operation <br />';
  }
}

/**
 * 客户端
 */
class Client{
  /**
   * Main program.
   */
  public static function main(){
//    $abstraction = new RefinedAbstraction(new ConcreteImplementorA());
//    $abstraction->operation();
    $abstraction = new RefinedAbstraction(new ConcreteImplementorB());
    $abstraction->operation();
  }
}

Client::main();

/**
 * 总结：
 * 桥接模式是为了解决同一操作环境应对多维度变化的方案，比如一个登录操作，可以通过第三方登录，可以通过网站登录，还可以通过UCcenter登录；这就是同一个登录操作的不同变化；
 *
 * 实现：
 * 1. 抽象化角色提供一个对象属性和一个该对象属性的对外操作接口
 * 2. 修正抽象化角色提供一个对抽象化角色中保存的对象属性和接口方法做修正的构造方法以及其他公共方法；
 * 3. 接下来就是一些需要桥接的具体化方法，这些具体化角色通过抽象类或接口约束，让其适配于抽象化角色的公共方法调用；
 */