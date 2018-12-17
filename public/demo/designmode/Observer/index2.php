<?php
/*
 * 事件机制
 * 3.1.4观察者模式在PHP中的应用场合:在web开发中观察者应用的方面很多
 *        典型的:用户注册(验证邮件，用户信息激活)，购物网站下单时邮件/短信通知等
 * 3.1.5php内部的支持
 *        SplSubject 接口，它代表着被观察的对象，
 *        其结构：
 *        interface SplSubject
 *        {
 *            public function attach(SplObserver $observer);
 *            public function detach(SplObserver $observer);
 *            public function notify();
 *        }
 *        SplObserver 接口，它代表着充当观察者的对象，
 *        其结构：
 *        interface SplObserver
 *        {
 *            public function update(SplSubject $subject);
 *        }
 */

/**
 * 用户登陆-诠释观察者模式
 */
class Login implements SplSubject {
  private $storage;
  public $status;
  public $ip;
  const LOGIN_ACCESS = 1;
  const LOGIN_WRONG_PASS = 2;
  const LOGIN_USER_UNKNOWN = 3;

  function __construct(){
    $this->storage = new SplObjectStorage();
  }

  function attach (SplObserver $observer) {
    $this->storage->attach($observer);
  }

  function detach(SplObserver $observer){
    $this->storage->detach($observer);
  }

  function notify(){
    foreach ($this->storage as $obs) {
      $obs->update($this);
    }
  }

  //执行登陆
  function handleLogin()
  {
    $ip = rand(1,100);
    switch (rand(1, 3)) {
      case 1:
        $this->setStatus(self::LOGIN_ACCESS, $ip);
        $ret = true;
        break;
      case 2:
        $this->setStatus(self::LOGIN_WRONG_PASS, $ip);
        $ret = false;
        break;
      case 3:
        $this->setStatus(self::LOGIN_USER_UNKNOWN, $ip);
        $ret = false;
        break;
    }
    /**
     * handle event
     */
    $this->notify();
    return $ret;
  }


  /**
   * @param $status
   * set login status
   */
  function setStatus($status,$ip)
  {
    $this->status = $status;
    $this->ip = $ip;
  }

  /**
   * @return mixed
   * get login status
   */
  function getStatus()
  {
    return $this->status;
  }

}

/**
 * 只针对登陆的贯观察者
 * Class LoginObserver
 */
abstract class LoginObserver implements SplObserver {
  private $login;

  function __construct(Login $login){
    $this->login = $login;
    $login->attach($this);
  }

  /**
   * 对外统一的访问点
   * @param SplSubject $subject
   */
  function update( SplSubject $subject ){
    if($subject === $this->login){
      $this->doUpdate($subject);
    }
  }

  abstract function doUpdate( Login $login );
}

/**
 * Class EmailObserver
 */
class EmailObserver extends LoginObserver{

  //不同功能的观察者实现不同的功能
  function doUpdate( Login $login ){
    $status = $login->getStatus();
    if($status == Login::LOGIN_ACCESS){
      //            $this->sendMail('用户ip:'.$observable->ip.'登陆成功!');
      echo __CLASS__.'用户ip:'.$login->ip.'登陆成功!'.'------------------';
    }
    if($status == Login::LOGIN_WRONG_PASS){
      //            $this->sendMail('用户ip:'.$observable->ip.'登陆失败，密码错误!');
      echo __CLASS__.'用户ip:'.$login->ip.'登陆失败，密码错误!'.'------------------';
    }
    if($status == Login::LOGIN_USER_UNKNOWN){
      //            $this->sendMail('用户ip:'.$observable->ip.'登陆失败，无此用户!');
      echo __CLASS__.'用户ip:'.$login->ip.'登陆失败，无此用户!'.'------------------';
    }
  }
}

//实例化登陆信息
$login = new Login();
//实现发邮件观察者
new EmailObserver($login);
//开始登陆
$login->handleLogin();

//在主题对象中某一事件一被触发就调用通知方法通知所有观察者对象