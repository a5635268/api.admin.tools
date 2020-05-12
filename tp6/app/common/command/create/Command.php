<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 刘志淳 <chun@engineer.com>
// +----------------------------------------------------------------------

namespace app\common\command\create;

use think\console\input\Argument;

class Command extends Create
{
    protected $type = "Command";

    protected function configure()
    {
        parent::configure();
        $this->setName('create:command')
            ->addArgument('commandName', Argument::OPTIONAL, "The name of the command")
            ->setDescription('Create a new custom command class');
    }

    protected function buildClass(string $name): string
    {
        $commandName = $this->input->getArgument('commandName') ?: strtolower(basename($name));
        $namespace   = trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
        $class = str_replace($namespace . '\\', '', $name);
        $stub  = file_get_contents($this->getStub());
        return str_replace(['{%commandName%}', '{%className%}', '{%namespace%}', '{%app_namespace%}'], [
            $commandName,
            $class,
            $namespace,
            $this->app->getNamespace(),
        ], $stub);
    }

    protected function getStub(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'command.stub';
    }

    protected function getNamespace(string $app): string
    {
        return parent::getNamespace($app) . '\\command';
    }

}
