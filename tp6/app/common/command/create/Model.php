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

class Model extends Create
{
    protected $type = "Model";

    protected function configure()
    {
        parent::configure();
        $this->setName('create:model')
            ->addArgument('pk_id',  Argument::OPTIONAL, 'model primary key name','id')
            ->setDescription('Create a new custom model class');
    }

    protected function getStub()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'model.stub';
    }

    protected function buildClass(string $name)
    {
        $stub = file_get_contents($this->getStub());
        $namespace = trim(implode('\\' , array_slice(explode('\\' , $name) , 0 , - 1)) , '\\');
        $class = str_replace($namespace . '\\' , '' , $name);
        $pkId = $this->input->getArgument('pk_id');
        return str_replace(['{%className%}', '{%actionSuffix%}', '{%namespace%}', '{%app_namespace%}','{%pk_id}'], [
            $class,
            $this->app->config->get('route.action_suffix'),
            $namespace,
            $this->app->getNamespace(),
            $pkId
        ], $stub);
    }

    protected function getNamespace(string $app): string
    {
        return parent::getNamespace($app) . '\\model';
    }
}
