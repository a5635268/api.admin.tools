<?php
declare (strict_types = 1);

namespace app\model;

use app\command\Coupon;
use app\common\facade\JWT;
use app\common\model\BaseModel;
use \libs\Log;
use think\facade\Db;

class WxMember extends BaseModel
{
    const DEFAULT_SQID = 174;
    const DEFAULT_LEVEL = 3;
    const DEFAULT_COMEFROM = 12;

    // 新会员注册
    const NEW_MEMBER_REGISTER = 100;

    protected $pk = 'id';

    protected function initialize()
    {
        parent::initialize();
    }

    public function getIntegralNumAttr($value, $data){
       return $integral_num = invoke(\app\common\service\Intergral::class)->get($data['id']);
    }

    public function memberIntergral()
    {
        return $this->hasOne('WxMemberIntergral','wx_member_id','id');
    }
    protected function getSexAttr($val){
        $val = intval($val);
        if($val === 0){
            return 2;
        }
        return $val;
    }

    /**
     * 获得列表
     * @param bool $where
     * @param string $fields
     * @param string|null $order
     * @param int $page
     * @param int $pageSize
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList($where = true ,string $fields = '*' ,string $order = null ,int $page = 1 ,int $pageSize = 20)
    {
        $data = [
            'count' => 0 ,
            'list'  => []
        ];
        $count = $this->where($where)->count();
        if (empty($count)) {
            return $this->returnRight($data);
        }
        $order = is_null($order) ? $this->pk . ' desc' : $order;
        $list = $this->where($where)
            ->field($fields)
            ->order($order)
            ->page($page , $pageSize)
            ->select();
        $data['count'] = $count;
        $data['list'] = $list;
        return $this->returnRight($data);
    }

    /**
     * 手机号添加
     * author: xiaogang.zhou@qq.com
     * datetime: 2020/2/26 14:22
     * @param array $data
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add(array $data)
    {
        $unionid = $data['unionid'];
        $member = self::where('unionid',$unionid)->find();
        if (empty($member)) {
            $insert = [
                'm_openid' => $data['openid'],
                'unionid' => $data['unionid'] ?? null,
                'b_sq_id' => 174,
                'ctime' => NOW,
                'update_time' => NOW,
                'sex' => 1,
                'update_user' => 'sys',
                'is_new' => 1,
                'comefrom' => 12,
                'state' => 0
            ];
            $member = self::create($insert);
        }
        if(empty($member->m_openid)){
            $member->m_openid = $data['openid'];
            $member->save();
        }
        $jwtData = [
            'member_id' => $member->id,
            'session_key' => $data['session_key']
        ];
        $token = JWT::encode($jwtData);
        if (empty($member->member_name)) {
            $returns = [
                'access_token' => $token,
                'activate_type' => 1,
                'member_data' => [
                    'nickname' => '',
                    'avatar' => '',
                    'gender' => 1
                ]
            ];
            return $this->returnRight($returns);
        }
        $activateType = $member->phone ? 3 : 2;
        $returns = [
            'access_token' => $token,
            'activate_type' => $activateType,
            'member_data' => [
                'nickname' => $member->member_name,
                'avatar' => $member->head_img,
                'gender' => $member->sex,
                'card' => $member->card,
                'grade' => $member->grade
            ]
        ];
        return $this->returnRight($returns);
    }

    public function nologin($data)
    {
        $unionid = $data['unionId'];
        $member = self::where('unionid',$unionid)->find();
        if (empty($member)) {
            $insert = [
                'm_openid' => $data['openId'],
                'unionid' => $unionid,
                'b_sq_id' => 174,
                'head_img' => $data['avatarUrl'],
                'sex' => $data['gender'],
                'member_name' => $data['nickName'],
                'ctime' => NOW,
                'update_time' => NOW,
                'sex' => 1,
                'update_user' => 'sys',
                'is_new' => 1,
                'comefrom' => 12,
                'state' => 0
            ];
            $member = self::create($insert);
        }
        if(empty($member->m_openid)){
            $member->m_openid = $data['openid'];
            $member->save();
        }
        $jwtData = [
            'member_id' => $member->id,
            'session_key' => $data['session_key']
        ];
        $token = JWT::encode($jwtData);
        $activateType = $member->phone ? 3 : 2;
        $returns = [
            'access_token' => $token,
            'activate_type' => $activateType,
            'member_data' => [
                'nickname' => $member->member_name,
                'avatar' => $member->head_img,
                'gender' => $member->sex,
                'card' => $member->card,
                'grade' => $member->grade
            ]
        ];
        return $this->returnRight($returns);
    }

    public function getOne(array $where,string $fields='*')
    {
        $row = self::where($where)->field($fields)->find();
        if(!$row)   return $this->returnError();
        $arr = ['sex', 'birthday', 'full_name', 'user_idy', 'address', 'wedlock', 'business_type', 'degree', 'month_income'];
        $row->is_perfect = true;
        foreach ($arr as $v){
            if(!($row->$v)){
                $row->is_perfect = false;
                break;
            }
        }
        return $this->returnRight($row);
    }

    /**
     * 用户信息更新
     * author: xiaogang.zhou@qq.com
     * datetime: 2020/2/26 14:30
     * @param $memberId
     * @param $data
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit($memberId,$data)
    {
        Log::info('member edit', $data);
        $member = self::where('id',$memberId)->find();
        if(empty($member)){
            return $this->returnError(1008);
        }
        $member->save($data);
        $activate_type = $member->phone ? 3 : 2;
        return $this->returnRight(['activate_type' => $activate_type]);
    }
}
