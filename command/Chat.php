<?php
namespace app\common\command;

// tp指令特性使用的功能
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;

// 引用项目的基类,该类继承自worker
use app\chat\controller\Start;


/**
 * 指令类
 * 在此定义指令
 * 再次启动多个控制器
 * @var mixed
 */
class Chat extends Command
{

    /**
     * 注册模块名称
     * 使用命令会启动该模块控制器
     * @var mixed
     */
    public $model_name = 'chat';

    /**
     * 注册控制器名称
     * 使用命令启动相关控制器
     * @var mixed
     */
    public $controller_names = ['Chat'];



    /**
     * configure
     * tp框架自定义指令特性
     * 注册命令参数
     * @return mixed
     */
    protected function configure()
    {
        $this->setName('chat')
            ->addArgument('status', Argument::OPTIONAL, "status")
            ->addArgument('controller_name', Argument::OPTIONAL, "controller_name/controller_name")
            ->addArgument('mode', Argument::OPTIONAL, "d")
            ->setDescription('chat control');

        /**
         * 以上设置命令格式为:php think spider [status] [controller_name/controller_name] [d]
         * think        为thinkphp框架入口文件
         * spider       为在框架中注册的命令,上面setName设置的
         * staus        为workerman框架接受的命令
         * controller_name/controller_name      为控制器名称,以正斜线分割,执行制定控制器,为空或缺省则启动所有控制器,控制器列表在controller_name属性中注册
         * d            最后一个参数为wokerman支持的-d-g参数,但是不用加-,直接使用d或者g
         */
    }


    /**
     * execute
     * tp框架自定义指令特性
     * 执行命令后的逻辑
     * @param mixed $input
     * @param mixed $output
     * @return mixed
     */
    protected function execute(Input $input, Output $output)
    {

        //获得status参数,即think自定义指令中的第一个参数,缺省报错
        $status  = $input->getArgument('status');
        if(!$status){
            $output->writeln('pelase input control command , like start');
            exit;
        }


        //获得控制器名称
        $controller_str =  $input->getArgument('controller_name');

        //获得模式,d为wokerman的后台模式(生产环境)
        $mode = $input->getArgument('mode');

        //分析控制器参数,如果缺省或为all,那么运行所有注册的控制器
        $controller_list = $this->controller_names;

        if($controller_str != '' && $controller_str != 'all' )
        {
            $controller_list = explode('/',$controller_str);
        }

        //重写mode参数,改为wokerman接受的参数
        if($mode == 'd'){
            $mode = '-d';
        }

        if($mode == 'g'){
            $mode = '-g';
        }

        //将wokerman需要的参数传入到其parseCommand方法中,此方法在start类中重写
        Start::$argvs = [
            'think',
            $status,
            $mode
        ];

        $output->writeln('start running chat');

        $programs_ob_list = [];


        //实例化需要运行的控制器
        foreach ($controller_list as $c_key => $controller_name) {
            $class_name = 'app\\'.$this->model_name.'\controller\\'.$controller_name;
            $programs_ob_list[] = new $class_name();
        }



        //将控制器的相关回调参数传到workerman中
        foreach (['onWorkerStart', 'onConnect', 'onMessage', 'onClose', 'onError', 'onBufferFull', 'onBufferDrain', 'onWorkerStop', 'onWorkerReload'] as $event) {
            foreach ($programs_ob_list as $p_key => $program_ob) {
                if (method_exists($program_ob, $event)) {
                    $programs_ob_list[$p_key]->$event = [$program_ob,$event];
                }
            }
        }

        Start::runAll();
    }
}