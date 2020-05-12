<?php

namespace app\common\service;

use app\model\coupon\Coupon as couponModel;
use app\model\coupon\MemberCoupon;
use traits\ResponsDataBuild;

class Coupon
{
    use ResponsDataBuild;

    public function __construct()
    {
    }

    public function send($couponId,$memberId,$params)
    {
        $coupon = couponModel::where('id',$couponId)
            ->find();
        if(empty($coupon)){
            $this->thrError(1301);
        }
        if($coupon->total < 1){
            $this->thrError(1302);
        }
        // todo:优惠券是否过期

        $data = [
            'coupon_id' => $couponId,
            'b_sq_id' =>  $coupon['b_sq_id'],
            'b_sh_id' => 0,
            'sn' => "CP".date('md',time()).str_pad($memberId,6,'0',STR_PAD_LEFT).rand(10000,99999),
            'title' => $coupon['title'],
            'deduct_money' => $coupon['deduct_money'],
            'min_consume' => $coupon['min_consume'],
            'valid_stime' => $coupon['valid_stime'],
            'valid_etime' => $coupon['valid_etime'],
            'comefrom' => 2
        ];
        $data = array_merge($data,$params);
        $coupon->total -= 1;
        $coupon->save();
        MemberCoupon::create($data);
        return $data;
    }
}
