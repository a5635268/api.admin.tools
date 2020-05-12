<?php

namespace app\command;

use app\common\command\BaseCommand;
use app\common\command\create\Model;
use app\common\facade\JWT;
use app\common\model\WxOpenid;
use app\common\service\Intergral;
use app\common\service\Sms;
use app\controller\Login;
use app\model\Banner;
use app\model\GoodsImagesProcess;
use app\model\WxMember;
use EasyWeChat\Factory;
use libs\OssUpload;
use think\console\Input;
use think\console\Output;
use think\console\input\Argument;
use think\console\input\Option;
use libs\Log;
use think\facade\Db;
use think\facade\Debug;

class Test extends BaseCommand
{
    protected function configure()
    {
        $this->setName('test')
            ->addArgument('func', Argument::OPTIONAL, "argument::optional",'test')
            ->setDescription('this is a description');
    }

    protected function execute(Input $input , Output $output)
    {
        $func = $input->getArgument('func');
        try {
            $res = $this->$func();
        } catch (\Exception $ex) {
            $this->output->error($ex->getMessage());
            return Log::err($ex->getMessage());
        }
    }
}
