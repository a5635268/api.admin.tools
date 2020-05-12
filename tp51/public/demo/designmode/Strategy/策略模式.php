<?php

/**
 * 抽象策略角色，以接口实现
 */
interface Strategy{
  /**
   * 算法接口
   */
  public function algorithmInterface();
}

/**
 * 具体策略角色A
 */
class ConcreteStrategyA implements Strategy{
  public function algorithmInterface(){
    echo 'algorithmInterface A<br />';
  }
}

/**
 * 具体策略角色B
 */
class ConcreteStrategyB implements Strategy{
  public function algorithmInterface(){
    echo 'algorithmInterface B<br />';
  }
}

/**
 * 具体策略角色C
 */
class ConcreteStrategyC implements Strategy{
  public function algorithmInterface(){
    echo 'algorithmInterface C<br />';
  }
}

/**
 * 环境角色
 */
class Context{
  /* 引用的策略 */
  private $_strategy;

  public function __construct(Strategy $strategy){
    $this->_strategy = $strategy;
  }

  public function contextInterface(){
    $this->_strategy->algorithmInterface();
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

    //这里可以根据业务逻辑的上下文，决定使用什么策略完成；
    $strategyA = new ConcreteStrategyA();
    $context = new Context($strategyA);
    $context->contextInterface();

    $strategyB = new ConcreteStrategyB();
    $context = new Context($strategyB);
    $context->contextInterface();

    $strategyC = new ConcreteStrategyC();
    $context = new Context($strategyC);
    $context->contextInterface();
  }
}

Client::main();

/**
 * 应用场景：
 * 比如，环境角色为消费付款，策略角色为微信，支付宝，银联；
 * 通过传入不同的策略角色实现同一环境角色的付款行为；
 *
 * 实现步骤：
 * 1. 先定义环境角色，在这个环境角色中的某一行为有哪些策略可以实现？
 * 2. 然后再分别定义策略角色!对了，像这种有不同的操作只为了实现同一目的的，都要通过抽象类或接口来约束它们
 * 3. 最后用不同的策略实现不同的行为！
 */