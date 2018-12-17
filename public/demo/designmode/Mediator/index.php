<?php
/**
 * 中介者模式
 * 用一个中介对象来封装一系列的对象交互。中介者使各对象不需要显式地相互引用，从而使其耦合松散，而且可以独立地改变它们之间的交互。中介者模式又称为调停者模式。
 *
 *
 */
/**
 * 抽象中介者类
 */
abstract class Mediator
{
  static protected $_colleaguesend = array(
    'ConcreteColleague1'=> 'ConcreteColleague2',
    'ConcreteColleague2'=> 'ConcreteColleague3',
    'ConcreteColleague3'=> 'ConcreteColleague1',
  );
  protected  $_colleagues = null; //array
  public  function register(Colleague $colleague) {
    $this->_colleagues[get_class($colleague)] = $colleague;
  }

  public abstract function operation($name, $message);
}
/**
 * 具体中介者类
 */
class ConcreteMediator extends Mediator
{

  public function operation($obj, $message) {
    $className = self::$_colleaguesend[get_class($obj)];
    if ($className == get_class($obj) ) {
      return ;
    }
    if ($this->_colleagues[$className]) {

      $this->_colleagues[$className]->notify($message);
    }
    return ;
  }
}
/**
 * 抽象同事类
 */
abstract class Colleague
{
  protected  $_mediator = null;

  public function __construct($mediator) {

    $this->_mediator = $mediator;
    $mediator->register($this);
  }
  /**
   * 通过中介实现相互调用
   */
  public abstract function send($message);
  /**
   * 具体需要实现的业务逻辑代码
   */
  public abstract function notify($message);
}

/**
 * 具体同事类
 */
class ConcreteColleague1 extends Colleague
{
  public function __construct(Mediator $mediator) {
    parent::__construct($mediator);
  }

  public function send($message) {
    $this->_mediator->operation($this, $message);
  }

  public function notify($message) {
    echo 'ConcreteColleague1 m:', $message, '<br/>';
  }

}

/**
 * 具体同事类
 */
class ConcreteColleague2 extends Colleague
{
  public function __construct(Mediator $mediator) {
    parent::__construct($mediator);
  }

  public function send($message) {
    $this->_mediator->operation($this, $message);
  }
  public function notify($message) {
    echo 'ConcreteColleague2 m:', $message, '<br/>';
  }

}


/**
 * 具体同事类
 */
class ConcreteColleague3 extends Colleague
{
  public function __construct(Mediator $mediator) {
    parent::__construct($mediator);
  }

  public function send($message) {
    $this->_mediator->operation($this, $message);
  }
  public function notify($message) {
    echo 'ConcreteColleague3 m:', $message, '<br/>';
  }

}
$objMediator = new  ConcreteMediator();
$objC1 = new ConcreteColleague1($objMediator);
$objC2 = new ConcreteColleague2($objMediator);
$objC3 = new ConcreteColleague3($objMediator);

$objC1->send("from ConcreteColleague1");
$objC2->send("from ConcreteColleague2");
$objC3->send("from ConcreteColleague3");
/****************************************************/