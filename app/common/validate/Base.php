<?php

namespace app\common\validate;

use think\Validate;

class Base extends Validate
{
    public function __construct(array $rules = [], array $message = [], array $field = [])
    {
        parent::__construct($rules,$message,$field);
    }
}