<?php

/**
 * 抽象原型角色：规定了具体原型对象必须实现的接口（如果要提供深拷贝，则必须具有实现clone的规定）
 */
abstract class ColorPrototype{
  //Methods
  abstract function copy();
}

/**
 * 具体原型角色（ConcretePrototype）：从抽象原型派生而来，是客户程序使用的对象，即被复制的对象。此角色需要实现抽象原型角色所要求的接口。
 */
class Color extends ColorPrototype{
  //Fields
  private $red;
  private $green;
  private $blue;

  //Constructors
  function __construct($red , $green , $blue){
    $this->red = $red;
    $this->green = $green;
    $this->blue = blue;
  }

  /**
   * set red
   * @param unknown_type $red
   */
  public function setRed($red){
    $this->red = $red;
  }

  /**
   * get red
   */
  public function getRed(){
    return $this->red;
  }

  /**
   *set Green
   * @param  $green
   */
  public function setGreen($green){
    $this->green = $green;
  }

  /**
   * get Green
   * @return unknown
   */
  public function getGreen(){
    return $this->green;
  }

  /**
   *set Blue
   * @param  $Blue
   */
  public function setBlue($Blue){
    $this->blue = $Blue;
  }

  /**
   * get Blue
   * @return unknown
   */
  public function getBlue(){
    return $this->blue;
  }

  /**
   * Enter description here...
   * @return unknown
   */
  function copy(){
    return clone $this;
  }

  function display(){
    echo $this->red , ',' , $this->green , ',' , $this->blue , '<br>';
  }
}

/**
 * 原型管理角色：创建具体原型类的对象，并记录每一个被创建的对象。（类似注册器模式）
 */
class ColorManager{
  // Fields
  static $colors = array ();

  // Indexers
  public static function add($name , $value){
    self::$colors[$name] = $value;
  }

  public static function getCopy($name){
    return self::$colors[$name]->copy();
  }
}

/**
 *  Client
 */
class Client{
  public static function Main(){
    //原型：白色
    ColorManager::add("white" , new Color(0 , 0 , 0));

    //红色可以由原型白色对象得到，只是重新修改白色: r
    $red = ColorManager::getCopy('white');
    $red->setRed(255);
    $red->display();

    //绿色可以由原型白色对象得到，只是重新修改白色: g
    $green = ColorManager::getCopy('white');
    $green->setGreen(255);
    $green->display();

    //绿色可以由原型白色对象得到，只是重新修改白色: b
    $Blue = ColorManager::getCopy('white');
    $Blue->setBlue(255);
    $Blue->display();
  }
}

ini_set('display_errors' , 'On');
error_reporting(E_ALL & ~E_DEPRECATED);
Client::Main();
