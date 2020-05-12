<?php
/**
 * 目的：
 * 命令模式是对命令的封装。命令模式把发出命令的责任和执行命令的责任分割开，委派给不同的对象。
 * 命令模式允许请求的一方和接收的一方独立开来，使得请求的一方不必知道接收请求的一方的接口，更不必知道请求是怎么被接收，以及操作是否被执行、何时被执行，以及是怎么被执行的。
 *
 * 实现步骤：
 * 1. 抽象命令规范具体命令的接口，实现执行方法；
 * 2. 具体命令内部提供一个指向接收者的实例属性，通过该属性对象调用其方法；
 * 3. 请求者内部提供一个指向具体命令的实例属性，通过该属性对象调用其方法；
 *
 * 请求者 -> 具体命令 -> 接收者（真正的方法，其实是接收者的）
 */


/**
 * 命令（Command）角色：声明了一个给所有具体命令类的抽象接口。这是一个抽象角色。
 */
interface Command {

  /**
   * 执行方法
   */
  public function execute();
}

/**
 * 具体命令（ConcreteCommand）角色：
 * 定义一个接受者和行为之间的弱耦合；实现Execute()方法，负责调用接收到的相应操作。Execute()方法通常叫做执行方法。
 */
class ConcreteCommand implements Command {

  private $_receiver;

  public function __construct(Receiver $receiver) {
    $this->_receiver = $receiver;
  }

  /**
   * 执行方法
   */
  public function execute() {
    $this->_receiver->action();
  }
}

/**
 * 接收者（Receiver）角色：
 * 负责具体实施和执行一个请求。任何一个类都可以成为接收者，实施和执行请求的方法叫做行动方法。
 */
class Receiver {

  /* 接收者名称 */
  private $_name;

  public function __construct($name) {
    $this->_name = $name;
  }

  /**
   * 行动方法
   */
  public function action() {
    echo $this->_name, ' do action.<br />';
  }
}

/**
 * 请求者（Invoker）角色：
 * 负责调用命令对象执行请求，相关的方法叫做行动方法。
 */
class Invoker {

  private $_command;

  public function __construct(Command $command) {
    $this->_command = $command;
  }

  public function action() {
    $this->_command->execute();
  }
}

/**
 * 客户（Client）角色：
 * 创建了一个具体命令(ConcreteCommand)对象并确定其接收者。
 */
class Client {

  /**
   * Main program.
   */
  public static function main() {
    $receiver = new Receiver('dir');
    $command = new ConcreteCommand($receiver);
    $invoker = new Invoker($command);
    $invoker->action();
  }
}

/**
  1、Client创建一个ConcreteCommand对象并指定它的Receiver对象
  2、某Invoker对象存储该ConcreteCommand对象
  3、该Invoker通过调用Command对象的execute操作来提交一个请求。若该命令是可撤消的，ConcreteCommand就在执行execute操作之前存储当前状态以用于取消命令。
  4、ConcreteCommand对象对调用它的Receiver的一些操作以执行该请求。
 */
Client::main();
