<?php
/**
 * 代理模式
 * 为其他对象提供一个代理以控制这个对象的访问
 *
 * 实现步骤：
 * 1. 通过代理接口约束主题类与代理类
 * 2. 代理类中要有一个对真实主题的引用对象；
 * 3. 通过这个引用对象调用真实主题
 */

/**
 * Interface Proxy
 * 定义真实主题角色RealSubject 和 抽象主题角色Proxy的共用接口，这样就在任何使用RealSubject的地方都可以使用Proxy。代理主题通过持有真实主题RealSubject的引用,不但可以控制真实主题RealSubject的创建或删除,可以在真实主题RealSubject被调用前进行拦截,或在调用后进行某些操作.
 */
interface Proxy{
  public function request();
  public function display();
}

/**
 * 真实主题角色(RealSubject):
 * 定义了代理角色(proxy)所代表的具体对象.
 */
class RealSubject implements Proxy{
  public function request(){
    echo "RealSubject request<br/>";
  }

  public function display(){
    echo "RealSubject display<br/>";
  }
}

/**
 * 代理角色(Proxy):
 * 1. 保存一个引用使得代理可以访问实体。若 RealSubject和Subject的接口相同，Proxy会引用Subject。
 * 2. 提供一个与Subject的接口相同的接口，这样代理就可以用来替代实体。
 * 3. 控制对实体的存取，并可能负责创建和删除它。
 */
class ProxySubject  implements Proxy{
  private $_subject = null;

  public function __construct(){
    $this->_subject = new RealSubject();
  }

  public function request(){
    $this->_subject->request();
  }

  public function display(){
    $this->_subject->display();
  }
}

$objProxy = new ProxySubject();
$objProxy->request();
$objProxy->display();