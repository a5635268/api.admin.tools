<?php
/**
 * 外观模式（Facade）
 * 为子系统中的一组接口提供一个一致的界面，Facade模式定义了一个高层次的接口，使得子系统更加容易使用，外部与子系统的通信是通过一个门面(Facade)对象进行
 */

/**
 * 子系统角色（Subsystem classes）：实现子系统的功能，并处理由Facade对象指派的任务。
 * 对子系统而言，facade和client角色是未知的，没有Facade的任何相关信息；即没有指向Facade的实例。
 */
class Camera {

  /**
   * 打开录像机
   */
  public function turnOn() {
    echo 'Turning on the camera.<br />';
  }

  /**
   * 关闭录像机
   */
  public function turnOff() {
    echo 'Turning off the camera.<br />';
  }

  /**
   * 转到录像机
   * @param <type> $degrees
   */
  public function rotate($degrees) {
    echo 'rotating the camera by ', $degrees, ' degrees.<br />';
  }
}

class Light {

  /**
   * 开灯
   */
  public function turnOn() {
    echo 'Turning on the light.<br />';
  }

  /**
   * 关灯
   */
  public function turnOff() {
    echo 'Turning off the light.<br />';
  }

  /**
   * 换灯泡
   */
  public function changeBulb() {
    echo 'changing the light-bulb.<br />';
  }
}

class Sensor {

  /**
   * 启动感应器
   */
  public function activate() {
    echo 'Activating the sensor.<br />';
  }

  /**
   * 关闭感应器
   */
  public function deactivate() {
    echo 'Deactivating the sensor.<br />';
  }

  /**
   * 触发感应器
   */
  public function trigger() {
    echo 'The sensor has been trigged.<br />';
  }
}

class Alarm {

  /**
   * 启动警报器
   */
  public function activate() {
    echo 'Activating the alarm.<br />';
  }

  /**
   * 关闭警报器
   */
  public function deactivate() {
    echo 'Deactivating the alarm.<br />';
  }

  /**
   * 拉响警报器
   */
  public function ring() {
    echo 'Ring the alarm.<br />';
  }

  /**
   * 停掉警报器
   */
  public function stopRing() {
    echo 'Stop the alarm.<br />';
  }
}

/**
 * 外观角色（Facade）：是模式的核心，他被客户client角色调用，知道各个子系统的功能。同时根据客户角色已有的需求预订了几种功能组合
 */
class SecurityFacade {
  /* 录像机 */
  private $_camera1, $_camera2;

  /* 灯 */
  private $_light1, $_light2, $_light3;

  /* 感应器 */
  private $_sensor;

  /* 警报器 */
  private $_alarm;

  public function __construct() {
    $this->_camera1 = new Camera();
    $this->_camera2 = new Camera();

    $this->_light1 = new Light();
    $this->_light2 = new Light();
    $this->_light3 = new Light();

    $this->_sensor = new Sensor();
    $this->_alarm = new Alarm();
  }

  public function activate() {
    $this->_camera1->turnOn();
    $this->_camera2->turnOn();

    $this->_light1->turnOn();
    $this->_light2->turnOn();
    $this->_light3->turnOn();

    $this->_sensor->activate();
    $this->_alarm->activate();
  }

  public  function deactivate() {
    $this->_camera1->turnOff();
    $this->_camera2->turnOff();

    $this->_light1->turnOff();
    $this->_light2->turnOff();
    $this->_light3->turnOff();

    $this->_sensor->deactivate();
    $this->_alarm->deactivate();
  }
}


/**
 * 客户端调用facade角色获得完成相应的功能。
 */
class Client {

  private static $_security;
  /**
   * Main program.
   */
  public static function main() {
    self::$_security = new SecurityFacade();
    self::$_security->activate();
  }
}

Client::main();