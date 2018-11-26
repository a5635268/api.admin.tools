<?php
/**
 * Created by PhpStorm.
 * User: lzl
 * Date: 2018/9/7
 * Time: 17:16
 */

namespace app\common\model;

use think\Model;
use traits\ResponsDataBuild;

class Base extends Model
{
    use ResponsDataBuild;

    protected $autoWriteTimeStamp = true;

    // 验证失败要抛出异常；
    protected $failException = true;

    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * 解决TP5在数据为空时不进行验证的bug,如果后续升级后解决可把该方法撤掉；
     * @param $data
     * @param null $rule
     * @param null $batch
     * @return array
     */
    protected function validateData($data, $rule=null, $batch = null){
        if(empty($data)){
            if($this->failException){
                throw new \think\exception\ValidateException('数据不能为空');
            }
            return $this->validateError('数据不能为空');
        }
        return parent::validateData($data, $rule, $batch);
    }

    /**
     * 基础的列表获取，里面的各种值转换可以通过获取器获得
     * @param bool $where
     * @param string $fields
     * @param null $order
     * @param int $page
     * @param int $pageSize
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getBaseList($where=true,$fields='*',$order=null,$page=1,$pageSize=20){
        $data = [
            'count' => 0,
            'list' => []
        ];
        $count = $this->where($where)->count();
        if(empty($count)){
            return $this->returnRight($data);
        }
        $order = is_null($order) ? $this->getPk(). ' desc' : $order;
        $list = $this->where($where)
            ->field($fields)
            ->order($order)
            ->page($page,$pageSize)
            ->select();
        $data['count'] = $count;
        $data['list']  = $list;
        return $this->returnRight($data);
    }

    /**
     * 基础的详情获取，里面的各种值转换可以通过获取器获得
     * @param $where
     * @param string $fields
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getBaseOne($where,$fields='*'){
        $res = $this->where($where)->field($fields)->find();
        return $this->returnRight($res);
    }
}