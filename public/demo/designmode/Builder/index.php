<?php
/**
 * 指导者:收银员
 *
 */
class DirectorCashier
{
  /**
   * 收银餐馆员工返回的食物
   */
  public function buildFood(Builder $builder) {
    $builder->buildPart1();
    $builder->buildPart2();
  }
}

/**
 * 抽象建造者
 *
 */
abstract class Builder
{
  /**
   * 创建产品的第一部分
   */
  public abstract function buildPart1();

  /**
   *
   * 创建产品的第二部分
   */
  public abstract function buildPart2();

  /**
   *
   *  返回产品
   */
  public abstract function getProduct();
}

/**
 * 具体建造者类:餐馆员工,返回的套餐是：汉堡两个+饮料一个
 */
class ConcreteBuilder1 extends Builder
{
  protected $_product = null;//产品对象
  function __construct(){
    $this->_product = new Product();
  }

  /**
   * 创建产品的第一部分::汉堡=2
   */
  public  function buildPart1()
  {
    $this->_product->add('Hamburger',2);
  }
  /**
   *
   * 创建产品的第二部分：
   */
  public  function buildPart2()
  {
    $this->_product->add('Drink', 1);
  }
  /**
   * 返回产品对象 :
   *
   */
  public function  getProduct()  {
    return  $this->_product;
  }
}

/**
 * 具体建造者类:餐馆员工，汉堡1个+饮料2个
 *
 */
class ConcreteBuilder2 extends Builder
{
  protected $_product = null;//产品对象
  function __construct(){
    $this->_product = new Product();
  }

  /**
   * 创建产品的第一部分:汉堡
   */
  public  function buildPart1()
  {
    $this->_product->add('Hamburger', 1);
  }
  /**
   *
   * 创建产品的第二部分:drink=2
   */
  public  function buildPart2()
  {
    $this->_product->add('Drink', 2);
  }
  /**
   * 返回产品对象 :
   *
   */
  public function  getProduct()  {
    return  $this->_product;
  }
}

/**
 * 产品类
 */
class Product
{
  public $products = array();
  /**
   * 添加具体产品
   */
  public function add($name,  $value) {
    $this->products[$name] = $value;
  }
  /**
   * 给顾客查看产品
   */
  public function showToClient()
  {
    foreach ($this->products as $key => $v) {
      echo $key , '=' , $v ,'<br>';
    }
  }
}


########## client

class Client
{
  /**
   * 顾客购买套餐
   *
   */
  public  function buy($type) {
    //指导者，收银员
    $director  = new DirectorCashier();

    //餐馆员工，收银员
    $class = new ReflectionClass('ConcreteBuilder' .$type );
    $concreteBuilder  = $class->newInstanceArgs();

    //收银员组合员工返回的食物
    $director->buildFood($concreteBuilder);
    
    //返回给顾客
    $concreteBuilder->getProduct()->showToClient();
  }
}

//测试
ini_set('display_errors', 'On');
$c = new Client();
$c->buy(1);//购买套餐1
$c->buy(2);//购买套餐1

/**
 *  总结：
 *  1. 通过指导者 -> 指导不同的建造者（通过接口或接口约束） -> 根据具体产品角色（也可以是不同的具体产品角色通过抽象产品来约束）来建造具体的产品输出
 *  2. 建造者模式与工厂模式类似，他们都是创建型模式，适用的场景也很相似。一般来说，如果产品的建造很复杂，那么请用工厂模式；如果产品的建造更复杂，那么请用建造者模式。
 */

/*
• 抽象建造者角色（Builder）：为创建一个Product对象的各个部件指定抽象接口，以规范产品对象的各个组成成分的建造。一般而言，此角色规定要实现复杂对象的哪些部分的创建，并不涉及具体的对象部件的创建。
• 具体建造者（ConcreteBuilder）
1）实现Builder的接口以构造和装配该产品的各个部件。即实现抽象建造者角色Builder的方法。
2）定义并明确它所创建的表示，即针对不同的商业逻辑，具体化复杂对象的各部分的创建
3）提供一个检索产品的接口
4）构造一个使用Builder接口的对象即在指导者的调用下创建产品实例

指导者（Director）：调用具体建造者角色以创建产品对象的各个部分。指导者并没有涉及具体产品类的信息，真正拥有具体产品的信息是具体建造者对象。它只负责保证对象各部分完整创建或按某种顺序创建。

产品角色（Product）：建造中的复杂对象。它要包含那些定义组件的类，包括将这些组件装配成产品的接口。
 */