<?php
namespace app\index\validate;

use app\common\validate\Base;

/**
 * Class Validate
 * https://www.kancloud.cn/manual/thinkphp5/129356
 * @package app\daily\validate
 */
class Publics extends Base
{
    //定义验证规则
    protected $rule = [
        'name'=>'require|number',
    ];

    //定义验证提示
    protected $message = [
        'name.require' => '名称必须',
    ];

    //定义验证场景
    protected $scene = [
        'test' => [],
    ];

}