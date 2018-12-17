<?php
/**
 * 职责链模式
 * 为解除请求的发送者和接收者之间的耦合,而使用多个对象都用机会处理这个请求,将这些对象连成一条链,并沿着这条链传递该请求,直到有一个对象处理它
 *
 * 实现：
 * 通过抽象处理者角色的抽象处理方法约束具体处理者角色都需要实现什么操作，然后通过一个公共方法让client做为串联，把每个具体处理者一级一级的串联一起；
 *
 * 适用于：
 * 不确定的多级操作；如审核；
 *
 */

/**
 * 抽象处理者角色
 */
abstract class Handler{
  protected $_handler = null;

  //↓↓ 设置当前的具体处理者的上一级处理者；
  public function setSuccessor($handler){
    $this->_handler = $handler;
  }

  abstract function handleRequest($request);
}

/**
 * 具体处理者角色,责任角色
 * 本处的职责就是输出0；如果不是自己的职责就传回上一级
 */
class ConcreteHandlerZero extends Handler{
  public function handleRequest($request){
    if ($request == 0) {
      echo "0\n";
    } else {
      $this->_handler->handleRequest($request);
    }
  }
}

class ConcreteHandlerOdd extends Handler{
  public function handleRequest($request){
    if ($request % 2) {
      echo $request . " is odd\n";
    } else {
      $this->_handler->handleRequest($request);
    }
  }
}

class ConcreteHandlerEven extends Handler{
  public function handleRequest($request){
    if (!($request % 2)) {
      echo $request . " is even\n";
    } else {
      $this->_handler->handleRequest($request);
    }
  }
}

// 实例一下
$objZeroHander = new ConcreteHandlerZero();
$objEvenHander = new ConcreteHandlerEven();
$objOddHander = new ConcreteHandlerOdd();

// ↓↓形成责任链,zero -> even -> odd
$objZeroHander->setSuccessor($objEvenHander);
$objEvenHander->setSuccessor($objOddHander);

foreach (array (2 , 3 , 4 , 5 , 0) as $row) {
  // 2 -> $objZeroHander(不是0，不处理) -> $objEvenHander(不是奇数，也不处理) -> $objOddHander(处理)
  $objZeroHander->handleRequest($row);
}