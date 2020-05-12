<?php

namespace app\command;
use app\common\command\BaseCommand;
use app\model\WxMember;
use EasyWeChat\Factory;
use libs\Log;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;

class Card extends BaseCommand
{
    const PLATINUM = 'phabjjmwGJ73fJyiOrVyVOskDVPQ';
    const VIP = 'phabjjlrWSIigTtIrRj2fq-DQyAI';

    protected function configure()
    {
        $this->setName('card')
            ->addArgument('func' , Argument::OPTIONAL , "方法名称" , 'test')
            ->setDescription('用于卡包生成编辑');
    }

    protected function execute(Input $input , Output $output)
    {

        try {
            $func = $input->getArgument('func');
            $this->$func();
        } catch (\Exception $ex) {
            $this->output->error($ex->getMessage());
            return Log::err($func , $ex->getMessage());
        }
    }

    private function getWechatApp()
    {
        $wechatConfig = collect(config('wechat.default'))
            ->merge(config('wechat.official_account'))
            ->toArray();
        $app = Factory::officialAccount($wechatConfig);
        return $app;
    }

    /**
     * vip: phabjjlrWSIigTtIrRj2fq-DQyAI
     * platinum: phabjjmwGJ73fJyiOrVyVOskDVPQ
     * author: xiaogang.zhou@qq.com
     * datetime: 2020/4/22 13:52
     * @param string $card
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create($card='vip')
    {
        $cardData = [
            'vip' => [
                'background_pic_url' => 'http://mmbiz.qpic.cn/mmbiz_jpg/UhDEHUyuXSosgO5mCdvGA54xnS0QOo4FNdRfDAHVgjC9HHTuiasEibkPS9ibkeyHfzjBluK1JTUaKzGgIODBurYSg/0',
                'title' => 'VIP会员卡'
            ],
            'platinum' => [
                'background_pic_url' => 'http://mmbiz.qpic.cn/mmbiz_jpg/UhDEHUyuXSosgO5mCdvGA54xnS0QOo4F75knSy7muSmyPWuahibLz35BHH9duBEL4fqR2mzGJdqY9P4uNiaVSJrw/0',
                'title' => '铂金专享会员卡'
            ]
        ];

        // 1. 创建会员卡
        $cardType = "member_card";
        $appletUserName = 'gh_f0207551fb52@app';
        $attributes = [
            'background_pic_url' => $cardData[$card]['background_pic_url'],
            'base_info' => [
                'logo_url' => 'http://mmbiz.qpic.cn/mmbiz_jpg/UhDEHUyuXSqO1icR6BjRdALPpE59JRzVuia2MPCazaB2uWIdbH77iaHglZwIiak9m2f9KGAgtia1l21NSL3oiaEuLvOQ/0',
                'brand_name' => '长泰广场',
                'code_type' => 'CODE_TYPE_QRCODE',
                'title' => $cardData[$card]['title'],
                'color' => 'Color010',
                'notice' => '使用时向服务员出示此卡',
                'description' => '不可与其他优惠同享',
                'center_title' => '会员中心',
                'center_app_brand_user_name' => $appletUserName,
                'center_app_brand_pass' => 'pages/index/index',
                'date_info' => [
                    'type' => 'DATE_TYPE_PERMANENT'
                ],
                'sku' => [
                    'quantity' => 100000000
                ],
                'get_limit' => 1,
                'can_share' => false,
                'can_give_friend' => false,
                "use_custom_code" => false,
                'promotion_url_name' => '积分商城',
                'promotion_app_brand_user_name' => $appletUserName,
                'promotion_app_brand_pass' => 'pages/mall/index',
                'custom_url_name' => '优惠券包',
                'custom_app_brand_user_name' => $appletUserName,
                'custom_app_brand_pass' => 'pages/coupon/index',
            ],
            'supply_bonus' => false,
            'supply_balance' => false,
            'prerogative' => '长泰广场会员卡',
            // 使用跳转型激活
            "wx_activate" => true,
            'wx_activate_after_submit' => true,
            'wx_activate_after_submit_url' => 'http://h5.hotelshop.chamshare.cn/activation',
        ];
        $app = $this->getWechatApp();
        $card = $app->card;
        $res = $card->create($cardType,$attributes);
        dd($res);
    }

    private function delCard()
    {
        $cardId = 'pVQuU1J5pQW10CDJnAzPtjXlGml8';
        $app = $this->getWechatApp();
        $card = $app->card;
        $res = $card->delete($cardId);
        header("Content-type: text/html; charset=utf-8");
        echo "<pre>";
        print_r($res);
        echo "<pre/>";
        die;
    }

    private function cardJosn()
    {
        $cards = [
            ['card_id' => 'pVQuU1JiyRcv8VnF3t_9oBj2HCd0']
        ];
        $card = $this->getWechatApp()->card;
        $json = $card->jssdk->assign($cards); // 返回 json 格式
        $rs = $card->jssdk->getTicket();
        dd($rs);
    }


    private function setField()
    {
        $cardId =  self::VIP;
        $app = $this->getWechatApp();
        $card = $app->card;
        $settings = [
            'required_form' => [
                'can_modify' => false,
                'common_field_id_list' => [
                    'USER_FORM_INFO_FLAG_MOBILE',
                    'USER_FORM_INFO_FLAG_SEX',
                    'USER_FORM_INFO_FLAG_NAME',
                    'USER_FORM_INFO_FLAG_BIRTHDAY'
                ]
            ]
        ];
        $res = $card->member_card->setActivationForm($cardId, $settings);
        dd($res);
    }

    private function cardlist()
    {
        $app = $this->getWechatApp();
        $card = $app->card;
        $res = $card->list($offset = 0, $count = 100, $statusList = 'CARD_STATUS_VERIFY_OK');
        $data = $res['card_id_list'];
        foreach ($data as $v){
            $res = $card->delete($v);
            $this->output->info(json_encode($res));
        }
    }

    private function setWhilist()
    {
        $app = $this->getWechatApp();
        $card = $app->card;
        $usernames = [
            'tianya900625',
            'a_5635268'
        ];
        $res = $card->setTestWhitelistByName($usernames); // 使用 username
        dd($res);
    }

    private function haveCard()
    {
        $memberId = 1338404;
        $card = $this->getWechatApp()->card;
        $member = WxMember::where('id' , $memberId)->find();
        $openid = $member->openid;
        $grade = $member->grade;
        // 是否铂金会员卡
        $isPt = $grade == 2 ? 1 : 0;
        $cardData = $card->getUserCards($openid);
    }
}
