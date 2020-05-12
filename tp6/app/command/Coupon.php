<?php

namespace app\command;
use app\common\command\BaseCommand;
use app\common\service\WxMsg;
use app\model\WxMember;
use libs\CacheKeyMap;
use libs\Log;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;

set_time_limit(0);

class Coupon extends BaseCommand
{
    const TEST_OPENIDS = [
        'ohabjjiL7-oQDjeic3eu3aMe9lPM',
        'ohabjjppOFA_m-0IGG-BLEV8g0zI',
        'ohabjjkT1CAmHEXnQkdiTeo2l7TU'
    ];

    // 蛋糕券ID
    const CAKE_COUPON_ID = 75;
    // 鲜花券ID
    const FLOWER_COUPON_ID = 76;
    // 停车券ID
    const PARK_COUPON_ID = 79;
    // 消息
    const MSG = [
        75 => [
           'first' =>  '亲爱的会员，在您的生日即将来临之际，长泰广场赠送您生日福利券一张，并预祝您生日快乐！福利已存入您的优惠券包中，赶快去使用吧！',
            'remark' => '生日福利券自发放之日起30天内有效，请在有效期内使用！点击查看'
        ],
        79 => [
            'first' =>  '亲爱的铂金专享会员，感谢支持和祝您生日快乐！长泰广场赠送您铂金专享会员生日专属福利3小时免费停车券一张，福利已存人您的优惠券包中，赶快去使用吧！',
            'remark' => '本券自发放之日起90天内有效，请在有效期内使用！点击查看'
        ]
    ];
    protected function configure()
    {
        $this->setName('coupon')
            ->addArgument('func', Argument::OPTIONAL, "方法名称",'test')
            ->setDescription('用于定时发券');
    }

    protected function execute(Input $input , Output $output)
    {
        $func = $input->getArgument('func');
        try {
            // 使用反射判断，必须是public才可以外部调用
            $method = new \ReflectionMethod($this , $func);
            if(!$method->isPublic()){
                return $this->output->error('没有该方法');
            }
            $method->invoke($this);
        } catch (\Exception $ex) {
            $this->output->error($ex->getMessage());
            return Log::err($func , $ex->getMessage());
        }
    }

    public function test()
    {
        header("Content-type: text/html; charset=utf-8");
        echo "<pre>";
        print_r(func_get_args());
        echo "<pre/>";
        die;
    }


    public function birthday_park()
    {
        $date = date("m-d");
        $this->send($date, self::PARK_COUPON_ID);
    }

    public function birthday_cake()
    {
        $date = date("m-d", strtotime('+14 day'));
        $this->send($date, self::CAKE_COUPON_ID);
    }

    /**
     * 生日券，每天就几百人，直接通过php-cli，分批处理
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function send($date,$couponId)
    {
        $chunk = function ($data) use($date,$couponId){
            foreach ($data as $member){
                try{
                    // 送券
                    $this->sendCoupon($member,$couponId);
                }catch (\Exception $ex){
                    Log::error('生日券发送失败', $ex->getMessage(), $member->id);
                    $this->output->error($ex->getMessage());
                    continue;
                }
            }
        };
        $query = WxMember::where('phone','not null')
            ->where('openid','not null')
            ->where('state',1)
            ->where('unionid','not null')
            ->where("right(`birthday`, 5) = '{$date}'");
        $couponId == self::PARK_COUPON_ID && $query = $query->where('grade' , 2);
        $query->chunk(100,$chunk);
    }

    public function couponInit()
    {
        $chunk = function ($data){
            foreach ($data as $member){
                try{
                    // 送券
                    $this->sendCoupon($member,self::CAKE_COUPON_ID);
                }catch (\Exception $ex){
                    Log::error('生日券发送失败', $ex->getMessage(), $member->id);
                    $this->output->error($ex->getMessage());
                    continue;
                }
            }
        };
        WxMember::where('phone','not null')
            ->where('openid','not null')
            ->where('unionid','not null')
            ->where('state',1)
            ->where("DATE_FORMAT(`birthday`,'%m-%d') between '04-29' and '05-12'")
            ->chunk(100,$chunk);

    }

    // 发券
    public function sendCoupon(WxMember $member,$couponId)
    {
        // 已送过券的不会再送，一年只送一次
        // 防止生日改变后，再次推送
        $redis = app('redis');
        $year = date('y',NOW);
        $cacheKey = CacheKeyMap::birthdaySet($year);
        $cacheVal = $couponId . '|' . $member->id;
        if($redis->sIsMember($cacheKey,$cacheVal)){
           throw new \Exception('该优惠券已领取过:' . $cacheVal);
        }

        if(in_array($couponId,[self::CAKE_COUPON_ID,self::PARK_COUPON_ID])){
            $validStime = NOW;
            // 停车券的话，有效期是三个月，普通券的有效期是一个月
            $validEtime = $couponId == self::PARK_COUPON_ID ? strtotime('+3 month') : strtotime('+1 month');
        }

        $params = [
            'wx_member_id' => $member->id,
            // 来自于生日券发放
            'comefrom' => 10,
            'valid_stime' => $validStime,
            'valid_etime' => $validEtime
        ];
        $coupon = invoke(\app\common\service\Coupon::class)->send($couponId,$member->id,$params);

        // 鲜花券和蛋糕券一起发
        if($couponId == self::CAKE_COUPON_ID){
            $params = [
                'wx_member_id' => $member->id,
                // 来自于生日券发放
                'comefrom' => 10,
                'valid_stime' => $validStime,
                'valid_etime' => $validEtime
            ];
            $coupon = invoke(\app\common\service\Coupon::class)->send(self::FLOWER_COUPON_ID,$member->id,$params);
            $coupon['title'] = '30元生日蛋糕券*1张、8.5折鲜花券*1张';
            $pagepath = 'pages/coupon/index';
        }

        $redis->sAdd($cacheKey,$cacheVal);

        //记个日志
        $member->s_log .= ' |' . date('Y-m-d') . $coupon['title'];
        $member->save();

        $this->output->info($member->id . $coupon['title'] . '券已发放');

        if(empty($member->openid)){
            Log::error('生日券消息发送失败，没有公众号openid', $member->id);
            return false;
        }

        // 发送消息
        $data = [
            'first'    =>  self::MSG[$couponId]['first'] ,
            'keyword1' => $member->card ,
            'keyword2' => $coupon['title'] ,
            'keyword3' => '长泰广场会员生日专属福利' ,
            'keyword4' => date("Y-m-d"),
            'remark'   =>  self::MSG[$couponId]['remark'] ,
        ];

        // 消息推送，由于在脚本里面，同步发送即可
        $params = [
            'touser' => $member->openid,
            'template_id' => config('wx_templates.birthday.template_id'),
            'miniprogram' => [
                'appid' => env('WECHAT.MINI_PROGRAM_APPID'),
                'pagepath' => $pagepath ?: '/pages/coupon/detail?id=' . $coupon['coupon_id']
            ],
            'data' => $data
        ];
        invoke(WxMsg::class)->send($params);
    }
}
