<?php

namespace app\admin\model\content;

use libs\Lbscloud;
use think\Model;
use libs\Pinyinfirstchar;

class Organization extends Model
{
    // 表名
    protected $name = 'organization';
    protected $pk = 'org_id';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = true;

    // 定义时间戳字段名
    protected $createTime = "create_time";
    protected $updateTime = "update_time";

    // 追加属性
    protected $append = [
        'create_time_text',
        'update_time_text'
    ];

    protected static function init()
    {
        $func = function ($org) {
            $name = $org->getData('name');
            $org->py_name = self::getFirstChar($name);
            $location = self::getLocation($org);
            $org->longitude = $location['lng'];
            $org->latitude = $location['lat'];
        };
        self::beforeInsert($func);
        $func = function ($org) {
            $location = self::getLocation($org);
            $org->longitude = $location['lng'];
            $org->latitude = $location['lat'];
        };
        self::beforeUpdate($func);
    }

    private static function getLocation($org)
    {
        $org->city_code = $org->city_code ? : $org->city;
        $data = City::where('city_code','in',[$org->province,$org->city,$org->city_code])
            ->column('city_name');
        $address = implode('',$data) . $org->address;
        $res = Lbscloud::getLocation($address);
        return $res;
    }

    private static function getFirstChar($name)
    {
        $obj = new Pinyinfirstchar();
        $pyname = $obj->getFirstchar($name);
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

    protected function setImagesAttr($value,$data)
    {
        if(empty($value)){
            return [];
        }
        $arr = explode(',',$value);
        return json_encode($arr);
    }

    protected function getImagesAttr($value,$data)
    {
        $arr = json_decode($value,true);
        return implode(',', $arr);
    }


    protected function getCityNameAttr($value,$data)
    {
        $res = City::where(['city_code' => $value])->find();
        return $res['city_name'];
    }

    protected function getCategoryDataAttr($value,$data)
    {
        $arr = Category::where('category_id','in',$value)
            ->field('category_id,name')
            ->select();
        return $arr;
    }


    public function getCreateTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['create_time']) ? $data['create_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getUpdateTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['update_time']) ? $data['update_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setCreateTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setUpdateTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }


}
