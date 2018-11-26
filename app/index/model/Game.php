<?php
namespace app\index\model;

use app\common\model\Base;
use \libs\Log;

class Game extends Base
{

    protected $pk = 'game_id';

    protected function initialize()
    {
        parent::initialize();
    }

    // 获得列表
    public function getList($where=true,$fields='*',$order=null,$page=1,$pageSize=20){
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

    public function add($data){
        Log::info(__METHOD__ . ':' . __LINE__, 'add in parameter', $data);
        $this->validateData($data, 'validate.add');
        self::create($data,true);
        return $this->returnSucc('添加成功');
    }

    public function edit($data){
        Log::info(__METHOD__ . __LINE__, 'add in parameter', $data);
        $this->validateData($data, 'validate.edit');
        self::create($data,true);
        return $this->returnSucc('编辑成功');
    }

    public function changeStatus($data,$status){
        $this->validateData($data,'validate.change');
        $data['status'] = $status;
        $this->allowField(true)->isUpdate(true)->save($data);
        return $this->returnRight();
    }

    public function del($data){
        $this->validateData($data,'validate.del');
        self::destroy(1);
        return $this->returnRight();
    }
}
