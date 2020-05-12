<?php

namespace app\admin\validate\content;

use think\Validate;

class Organization extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'city_code'=>'require|number',
        'category_ids' => 'require'
    ];
    /**
     * 提示消息
     */
    protected $message = [
    ];
    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => ['category_ids'],
        'edit' => ['category_ids'],
    ];

}
