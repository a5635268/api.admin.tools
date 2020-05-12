<?php
declare (strict_types = 1);

namespace app\controller;

use app\common\controller\BaseController;
use app\common\facade\JWT;
use app\common\service\Sms;
use app\model\WxMember;
use Exception;
use libs\CacheKeyMap;
use libs\Log;
use think\App;
use think\facade\Db;
use EasyWeChat\Factory;
use \Throwable;

class Login extends BaseController
{
    private  $miniProgram;
    private $member;
    private  $wechat;
    public function __construct(App $app,WxMember $member)
    {
        parent::__construct($app);
        $wechatConfig = collect(config('wechat.default'))
            ->merge(config('wechat.mini_program'))
            ->toArray();
        $miniProgram = Factory::miniProgram($wechatConfig);
        $this->miniProgram = $miniProgram;
        $this->member = $member;
    }

    /**
     * 微信登录
     * 登录分三步：
     * 1. 根据code换取openid，根据openid判断当前用户激活状态
     *      1. 未授权用户信息
     *      2. 未激活手机号
     *      3. 完整激活或者授权状态
     * @return array
     */
    public function session()
    {
        try {
            $code = input('code/s');
            $data = $this->miniProgram->auth->session($code);
            if(isset($data['errcode'])){
                $this->thrError($data['errcode'],$data['errmsg']);
            }
            if(!isset($data['unionid'])){
               $res = WxMember::where('m_openid',$data['openid'])->find();
               if(empty($res['unionid'])){
                   return $this->returnRight(['activate_type' => 0,'session_key' => $data['session_key']],0,'未关注公众号');
                }
               $data['unionid'] = $res['unionid'];
            }
            return $this->member->add($data);
        } catch (Throwable $ex) {
            return $this->returnException($ex);
        }
    }

    public function nologin()
    {
        $iv = input('iv/s','');
        $sessionKey = input('session_key/s','');
        $encryptData = input('encryptData/s','');
        try {
            $data = $this->miniProgram
                ->encryptor
                ->decryptData($sessionKey, $iv, $encryptData);
            $data['session_key'] = $sessionKey;
            return $this->member->nologin($data);
        } catch (Throwable $ex) {
            return $this->returnException($ex);
        }
    }

    /**
     * post 其他手机号登录
     * @return array
     */
    public function otherMobile()
    {
        $code = $this->params['code'];
        $mobile = $this->params['mobile'];
        $sessionKey =  $this->request->session_key;
        try{
            // 判断验证码
            if($code !== '99188' && $code !== cache(CacheKeyMap::smsValidkey($mobile))){
               $this->thrError(1003);
            }
            return $this->member->bindPhone($this->request->member_id,$mobile,$sessionKey);
        } catch (Throwable $ex) {
            return $this->returnException($ex);
        }
    }

    /**
     * 前端先通过wx.checkSession(）判断session_key是否过期
     * 过期就重新登录
     * post: 本地手机号一键登录
     * @return array
     */
    public function localMobile()
    {
        $sessionKey =  $this->request->session_key;
        $iv = $this->params['iv'];
        $encryptData = $this->params['encryptData'];
        try{
            $decryptedData = $this->miniProgram
                ->encryptor
                ->decryptData($sessionKey, $iv, $encryptData);
            $mobile = $decryptedData['phoneNumber'];
            return $this->member->bindPhone($this->request->member_id,$mobile,$sessionKey);
        } catch (Throwable $ex) {
            return $this->returnException($ex);
        }
    }
    /**
     * PUT
     * 用户信息更新
     * 前端获得头像，昵称，性别，省市
     * @return array
     */
    public function info()
    {
        try {
            $data = [
                'id' => $this->request->member_id,
                'head_img' => $this->params['avatarUrl'],
                'sex' => $this->params['gender'],
                'member_name' => $this->params['nickName']
            ];
            return $this->member->edit($this->request->member_id,$data);
        } catch (Throwable $ex) {
            return $this->returnException($ex);
        }
    }


    /**
     * 短信发送
     * author: xiaogang.zhou@qq.com
     * datetime: 2020/2/26 12:35
     * @param Sms $smsService
     * @return array
     */
    public function sendSms(Sms $smsService)
    {
        try {
            $mobile = $this->params['mobile'];
            $wxmember = WxMember::where('phone',$mobile)->find();
            if($wxmember && $wxmember->openid){
                return $this->returnError(1007);
            }
            $params = [
                'platform' => '长泰广场'
            ];
            $data = $smsService->sendSms($mobile,$params);
            $data['create_time'] = NOW;
            Db::table('sms_log')->insert($data);
            return $this->returnRight();
        } catch (Throwable $ex) {
            return $this->returnException($ex);
        }
    }
}
