<?php
declare (strict_types = 1);

namespace app\common\command\create;


use think\console\input\Option;
use think\facade\Config;

class Controller extends Create
{
    protected $type = "Controller";

    protected function configure()
    {
        parent::configure();
        $this->setName('create:controller')
            ->addOption('empty', null, Option::VALUE_NONE, 'Generate an empty controller class.')
            ->setDescription('Create a new custom controller class');
    }

    protected function getStub()
    {
        $stubPath = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR;

        if ($this->input->getOption('empty')) {
            return $stubPath . 'controller.empty.stub';
        }

        return $stubPath . 'controller.stub';
    }


    protected function getClassName(string $name): string
    {
        return parent::getClassName($name) . ($this->app->config->get('route.controller_suffix') ? 'Controller' : '');
    }

    protected function getNamespace(string $app): string
    {
        return parent::getNamespace($app) . '\\controller';
    }

}
