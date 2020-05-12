<?php

namespace app\admin\model\content;

use fast\Pinyin;
use think\Model;

class Category extends Model
{
    // 表名
    protected $name = 'category';

    protected $pk = 'category_id';

    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 追加属性
    protected $append = [
        //'label_text'
    ];

    protected static function init()
    {
        $func = function ($category) {
            $name = $category->getData('name');
            $category->py_name = self::getPinYin($name);
            $category->label = $category->label ?
                implode(',',$category->label) : '';
        };
        self::beforeInsert($func);


        $func = function ($category) {
            $category->label = $category->label ?
                implode(',',$category->label) : '';
        };
        self::beforeUpdate($func);
    }

    private static function getPinYin($name)
    {
        $obj = new Pinyin();
        $pyname = $obj->get($name);
        $i = 0;
        while(true){
            $check = self::where('py_name', $pyname)->find();
            if($check){
                $i += 1;
                $pyname .= $i;
                continue;
            }
            break;
        }
        return $pyname;
    }
}
