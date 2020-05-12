<?php

namespace app\admin\model\content;

use think\Model;

class Teacher extends Model
{
    // 表名
    protected $name = 'teacher';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 追加属性
    protected $append = [
        'course_data'
    ];

    protected function getCourseDataAttr($value,$data)
    {
        if(empty($data['course_ids'])){
            return '';
        }
        $value = $data['course_ids'];
        $res = Course::where('course_id','in',$value)
            ->field('course_id,name')
            ->select();
        $names = array_column($res,'name');
        return implode(',',$names);
    }













    public function organization()
    {
        return $this->belongsTo('Organization', 'org_id', 'org_id', [], 'LEFT')->setEagerlyType(0);
    }
}
