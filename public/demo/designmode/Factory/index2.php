<?php
/**
 * 工厂方法模式
 * 定义一个用于创建对象的接口,让子类决定将哪一个类实例化,使用一个类的实例化延迟到其子类
 */
/*
class DBFactory{
  public static function create($type){
    swtich($type){
            case "Mysql":
                return new MysqlDB();
            case "Postgre":
                return new PostgreDB();
            case "Mssql":
                return new MssqlDB();
        }
    }
}

# 面向对象设计原则: http://developer.51cto.com/art/201206/340930.htm
# 面向对象的主要原则就是: 把修改关闭，把扩展打开;如果不使用工厂模式,我们每增加一个数据库类型就要修改源码，这不符合面向对象的设计原则
*/

//具体工厂
class DBFactory{
  public static function create($type){
    $class = $type . "DB";
    return new $class;
  }
}

//抽象产品
interface DB{
  public function connect();

  public function exec();
}

//具体产品1
class MysqlDB implements DB{
  public function __construct(){
    echo "mysql db<br/>";
  }

  public function connect(){
  }

  public function exec(){
  }
}

//具体产品2
class PostgreDB implements DB{
  public function __construct(){
    echo "Postgre db<br/>";
  }

  public function connect(){
  }

  public function exec(){
  }
}

//具体产品3
class MssqlDB implements DB{
  public function __construct(){
    echo "mssql db<br/>";
  }

  public function connect(){
  }

  public function exec(){
  }
}

# client
$oMysql = DBFactory::create("Mysql");
$oPostgre = DBFactory::create("Postgre");
$oMssql = DBFactory::create("Mssql");