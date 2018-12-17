<?php
/**
 * 命令角色
 */
interface Command {

  /**
   * 执行方法
   */
  public function execute();
}

/**
 * 宏命令接口
 */
interface MacroCommand extends Command {

  /**
   *  宏命令聚集的管理方法，可以删除一个成员命令
   * @param Command $command
   */
  public function remove(Command $command);

  /**
   * 宏命令聚集的管理方法，可以增加一个成员命令
   * @param Command $command
   */
  public function add(Command $command);

}


class DemoMacroCommand implements MacroCommand {

  private $_commandList;

  public function __construct() {
    $this->_commandList = array();
  }

  public function remove(Command $command) {
    $key = array_search($command, $this->_commandList);
    if ($key === FALSE) {
      return FALSE;
    }

    unset($this->_commandList[$key]);
    return TRUE;
  }

  public function add(Command $command) {
    return array_push($this->_commandList, $command);
  }

  public function execute() {
    foreach ($this->_commandList as $command) {
      $command->execute();
    }
  }
}

/**
 * 具体拷贝命令角色
 */
class CopyCommand implements Command {

  private $_receiver;

  public function __construct(Receiver $receiver) {
    $this->_receiver = $receiver;
  }

  /**
   * 执行方法
   */
  public function execute() {
    $this->_receiver->copy();
  }
}

/**
 * 具体拷贝命令角色
 */
class PasteCommand implements Command {

  private $_receiver;

  public function __construct(Receiver $receiver) {
    $this->_receiver = $receiver;
  }

  /**
   * 执行方法
   */
  public function execute() {
    $this->_receiver->paste();
  }
}

/**
 * 接收者角色
 */
class Receiver {

  /* 接收者名称 */
  private $_name;

  public function __construct($name) {
    $this->_name = $name;
  }

  /**
   * 复制方法
   */
  public function copy() {
    echo $this->_name, ' do copy action.<br />';
  }

  /**
   * 粘贴方法
   */
  public function paste() {
    echo $this->_name, ' do paste action.<br />';
  }
}

/**
 * 请求者角色
 */
class Invoker {

  private $_command;

  public function __construct(Command $command) {
    $this->_command = $command;
  }

  public function action() {
    $this->_command->execute();
  }
}

/**
 * 客户端
 */
class Client {

  /**
   * Main program.
   */
  public static function main() {
    $receiver = new Receiver('phpppan');
    $pasteCommand = new PasteCommand($receiver);
    $copyCommand = new CopyCommand($receiver);

    $macroCommand = new DemoMacroCommand();
    $macroCommand->add($copyCommand);
    $macroCommand->add($pasteCommand);

    $invoker = new Invoker($macroCommand);
    $invoker->action();

    $macroCommand->remove($copyCommand);
    $invoker = new Invoker($macroCommand);
    $invoker->action();
  }
}

Client::main();