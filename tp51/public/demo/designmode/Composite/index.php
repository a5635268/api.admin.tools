<?php
/**
 * 组合模式:树形菜单
 * 将对象组合成树形结构以表示"部分-整体"的层次结构,使得客户对单个对象和复合对象的使用具有一致性
 *
 *  * 实现步骤：
 * 1. 枝为联结，叶为内容，本质还是PHP关联数组的形式；
 * 2. 准备一个枝管理器（添加，删除，获得当前枝，整合枝），叶管理器（添加叶，输出叶）;
 */
/**
 * 抽象构件角色（component）
 * 是组合中的对象声明接口，在适当的情况下，实现所有类共有接口的默认行为。声明一个接口用于访问和管理Component子部件。
 * 这个接口可以用来管理所有的子对象。(可选)在递归结构中定义一个接口，用于访问一个父部件，并在合适的情况下实现它。
 *
 */
abstract class MenuComponent
{
  public function add($component){}
  public function remove($component){}

  //该方法为子类必须实现的抽象方法
  abstract public function displayOperation();
}

/**
 * 树枝构件角色（Composite）
 * 在组合树中表示叶节点对象，叶节点没有子节点。并在组合中定义图元对象的行为。
 */
class MenuComposite extends MenuComponent
{
  private $_items = array();
  private $_name = null;
  private $_align = '';
  public function __construct($name) {
    $this->_name = $name;
  }
  public function add($component) {
    $this->_items[$component->getName()] = $component;
  }
  public function remove($component) {
    $key = array_search($component,$this->_items);
    if($key !== false) unset($this->_items[$key]);
  }
  public function getItems() {
    return $this->_items;
  }

  //输出当前的枝和叶
  public function displayOperation($align = '|') {
    //获得当前的枝
    $items = $this->getItems();
    if($items) {
      $align .= ' _ _ ';
    }else{
      $align .='';
    }
    echo $this->_name, " \n";

    foreach($items as $name=> $item) {
      echo $align;
      //由于ADD传入的对象为枝或叶的对象，不同的displayOperation是不同的，如果当前的为枝那就进入递归；检测当前的枝下面是否还有枝；
      $item->displayOperation($align);
    }
  }

  public function getName(){
    return $this->_name;
  }
}

/**
 * 树叶构件角色(Leaf)
 * 定义有子部件的那些部件的行为。存储子部件。在Component接口中实现与子部件有关的操作。
 */
class ItemLeaf extends MenuComponent
{
  private $_name = null;
  private $_url = null;
  public function __construct($name,$url)
  {
    $this->_name = $name;
    $this->_url = $url;
  }

  public function displayOperation()
  {
    echo '<a href="', $this->_url, '">' , $this->_name, "</a>\n";
  }

  public function getName(){
    return $this->_name;
  }
}

class Client
{
  public static function displayMenu()
  {
    // 准备枝
    $subMenu1 = new MenuComposite("submenu1");
    $subMenu2 = new MenuComposite("submenu2");
    $subMenu3 = new MenuComposite("submenu3");
    $subMenu4 = new MenuComposite("submenu4");
    $subMenu5 = new MenuComposite("submenu5");

    // 联结枝
    $allMenu = new MenuComposite("AllMenu");
    $allMenu->add($subMenu1);
    $allMenu->add($subMenu2);
    $allMenu->add($subMenu3);
    $subMenu3->add($subMenu4);
    $subMenu4->add($subMenu5);

    // 准备叶
    $item3 = new ItemLeaf("baidu","www.baidu.com");
    $item4 = new ItemLeaf("google","www.google.com");

    // 为枝填充叶
    $subMenu2->add($item3);
    $subMenu2->add($item4);

    // 组合显示
    $allMenu->displayOperation();
  }
}

// 创建menu
Client::displayMenu();