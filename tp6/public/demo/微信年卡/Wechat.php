<?php
declare (strict_types = 1);

namespace app\controller;

use app\common\controller\BaseController;
use libs\Log;
use think\App;
use EasyWeChat\Factory;
use app\model\WxMember;
use Throwable;

class Wechat extends BaseController
{
    protected $wechatApp;
    const VIP_CARD = 'phabjjlrWSIigTtIrRj2fq-DQyAI';
    const PT_CARD = 'phabjjmwGJ73fJyiOrVyVOskDVPQ';

    public function __construct(App $app)
    {
        parent::__construct($app);
        $wechatConfig = collect(config('wechat.default'))
            ->merge(config('wechat.official_account'))
            ->toArray();
        $wechatApp = Factory::officialAccount($wechatConfig);
        $this->wechatApp = $wechatApp;
    }

    public function receive()
    {
        Log::debug('111111receivereceive');
        $this->wechatApp->server->push(function ($message) {
            Log::debug('message' , $message);
            return "您好！欢迎使用 EasyWeChat!";
        });

        $response = $this->wechatApp->server->serve();
        Log::debug('response',$response);
        $response->send();
    }

    // 领取
    public function cardJson()
    {
        $memberId = $this->request->member_id;
        $member = WxMember::where('id',$memberId)->find();
        $grade = $member->grade;
        // 是否铂金会员卡
        $isPt = $grade == 2 ? 1 : 0;
        $cards = [
            [
                'card_id' => $isPt ? self::PT_CARD : self::VIP_CARD,
                'outer_id' => 1
            ]
        ];
        $card = $this->wechatApp->card;
        $json = $card->jssdk->assign($cards); // 返回 json 格式
        return $this->returnRight($json);
    }

    // 判断是否领取过卡券
    public function haveCard()
    {
        $memberId = $this->request->member_id;
        $card = $this->wechatApp->card;
        $member = WxMember::where('id',$memberId)->find();
        if($member->card_code){
            return $this->returnRight(['is_have_card'=>1]);
        }
        return $this->returnRight(['is_have_card'=>0]);
        /*$openid = $member->openid;
        $grade = $member->grade;
        // 是否铂金会员卡
        $isPt = $grade == 2 ? 1 : 0;
        $cardData  = $card->getUserCards($member->openid);
        $cardData = $cardData['card_list'] ?? [];
        foreach ($cardData as $item){
            if(in_array($item['card_id'],[self::VIP_CARD,self::PT_CARD])){
                return $this->returnRight(['is_have_card'=>1]);
            }
        }
        return $this->returnRight(['is_have_card'=>0]);*/
    }

    public function received()
    {
        $memberId = $this->request->member_id;
        Log::notice('received',$this->params);
        $member = WxMember::where('id',$memberId)->find();
        $member->card_code = 1;
        $member->save();
        return $this->returnRight();
    }

    // 激活
    public function cardActivate()
    {
        try {
            $data =  $this->params;
            Log::info('卡包激活入口' , $data);
            $card = $this->wechatApp->card;
            $encryptedCode = $data['encrypt_code'];
            $openid = $data['openid'];
            $result = $card->code->decrypt($encryptedCode);
            if($result['errcode'] !== 0){
                $this->thrError(25,$result['errmsg']);
            }
            $code = $result['code'];
            $activateTicket = $data['activate_ticket'];
            $result = $card->member_card->getActivationForm($activateTicket);
            if($result['errcode'] !== 0){
                $this->thrError(25,$result['errmsg']);
            }
            $memberInfo = $result['info']['common_field_list'];

            $getPhone = function ($arr){
                foreach ($arr as $item){
                    if($item['name'] == 'USER_FORM_INFO_FLAG_MOBILE'){
                        return $item['value'];
                    }
                }
                return '';
            };
            $phone = $getPhone($memberInfo);

            if(empty($phone)){
                $this->thrError(25,'请填写正确的手机号');
            }

            # todo: 进行会员信息更新操作
            Log::notice('收到回调getField',$memberInfo);
            //$user = $this->wechatApp->user->get($openid);
            $member = WxMember::where('phone',$phone)->find();
            if(empty($member)){
                $this->thrError(-1,'激活失败,该会员不存在,请填写激活时一致的手机号');
            }
            // 开始激活
            $info = [
                'membership_number' => $member->card,
                'code' => $code
            ];
            $result = $card->member_card->activate($info);
            if($result['errcode'] !== 0){
                $this->thrError(25,$result['errmsg']);
            }
            // 激活成功
            $member->openid = $openid;
            $member->card_code = $code;
            $member->save();
            return $this->returnRight($result);
        } catch (Throwable $ex) {
            Log::error('卡包激活失败', $data, $ex->getMessage());
            return $this->returnException($ex);
        }
    }
}
