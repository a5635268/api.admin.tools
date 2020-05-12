<?php

namespace app\common\validate;

use think\exception\ValidateException;
use think\Validate;
use traits\ResponsDataBuild;

class BaseValidate extends Validate
{
    use ResponsDataBuild;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    protected $app;

    public function __construct(array $rules = [], array $message = [], array $field = [])
    {
        parent::__construct($rules,$message,$field);
        $this->app = app();
    }

    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = null)
    {
//        if (empty($data)) {
//            if ($this->failException) {
//                throw new ValidateException('数据不能为空');
//            }
//            return $this->validateError('数据不能为空');
//        }
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                list($validate, $scene) = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v     = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }
        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }
}
