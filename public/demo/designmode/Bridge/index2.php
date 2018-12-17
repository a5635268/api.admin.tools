<?php
/**
  模拟毛笔：
    现需要提供大中小3种型号的画笔，能够绘制5种不同颜色，如果使用蜡笔，我们需要准备3*5=15支蜡笔，也就是说必须准备15个具体的蜡笔类。而如果使用毛笔的话，只需要3种型号的毛笔，外加5个颜料盒，用3+5=8个类就可以实现15支蜡笔的功能。
    实际上，蜡笔和毛笔的关键一个区别就在于笔和颜色是否能够分离。即将抽象化(Abstraction)与实现化(Implementation)脱耦，使得二者可以独立地变化"。关键就在于能否脱耦。蜡笔的颜色和蜡笔本身是分不开的，所以就造成必须使用15支色彩、大小各异的蜡笔来绘制图画。而毛笔与颜料能够很好的脱耦，各自独立变化，便简化了操作。在这里，抽象层面的概念是："毛笔用颜料作画"，而在实现时，毛笔有大中小三号，颜料有红绿蓝黑白等5种，于是便可出现3×5种组合。每个参与者（毛笔与颜料）都可以在自己的自由度上随意转换。
    蜡笔由于无法将笔与颜色分离，造成笔与颜色两个自由度无法单独变化，使得只有创建15种对象才能完成任务。
    Bridge模式将继承关系转换为组合关系，从而降低了系统间的耦合，减少了代码编写量。
 */

/******************************Abstraction **************************/
/**
 *
 * Abstraction抽象类的接口
 * @author guisu
 *
 */
abstract class BrushPenAbstraction {
  protected $_implementorColor = null;

  /**
   *
   * Enter description here ...
   * @param Color $color
   */
  public function setImplementorColor(ImplementorColor $color) {
    $this->_implementorColor = $color;
  }
  /**
   *
   * Enter description here ...
   */
  public abstract function operationDraw();
}
/******************************RefinedAbstraction **************************/
/**
 *
 * 扩充由Abstraction;大毛笔
 * @author guisu
 *
 */
class BigBrushPenRefinedAbstraction extends BrushPenAbstraction {
  public function operationDraw() {
    echo 'Big and ', $this->_implementorColor->bepaint (), ' drawing';
  }
}
/**
 *
 * 扩充由Abstraction;中毛笔
 * @author guisu
 *
 */
class MiddleBrushPenRefinedAbstraction extends BrushPenAbstraction {
  public function operationDraw() {
    echo 'Middle and ', $this->_implementorColor->bepaint (), ' drawing';
  }
}
/**
 *
 * 扩充由Abstraction;小毛笔
 * @author guisu
 *
 */
class SmallBrushPenRefinedAbstraction extends BrushPenAbstraction {
  public function operationDraw() {
    echo 'Small and ', $this->_implementorColor->bepaint(), ' drawing';
  }
}

/******************************Implementor **************************/
/**
 * 实现类接口(Implementor)
 *
 * @author mo-87
 *
 */
class ImplementorColor {
  protected $value;

  /**
   * 着色
   *
   */
  public  function bepaint(){
    echo $this->value;
  }
}
/******************************oncrete Implementor **************************/
class oncreteImplementorRed extends ImplementorColor {
  public function __construct() {
    $this->value = "red";
  }
  /**
   * 可以覆盖
   */
  public function bepaint() {
    echo $this->value;
  }
}

class oncreteImplementorBlue extends ImplementorColor {
  public function __construct() {
    $this->value = "blue";
  }
}

class oncreteImplementorGreen extends ImplementorColor {
  public function __construct() {
    $this->value = "green";
  }
}

class oncreteImplementorWhite extends ImplementorColor {
  public function __construct() {
    $this->value = "white";
  }
}

class oncreteImplementorBlack extends ImplementorColor {
  public function __construct() {
    $this->value = "black";
  }
}
/**
 *
 * 客户端程序
 * @author guisu
 *
 */
class Client {
  public static function Main() {
    //小笔画红色
    $objRAbstraction = new SmallBrushPenRefinedAbstraction();
    $objRAbstraction->setImplementorColor(new oncreteImplementorRed());
    $objRAbstraction->operationDraw();
  }
}
Client::Main();