<?php
/**
 * Created by PhpStorm.
 * User: CTDJ1803111
 * Date: 2019/7/25
 * Time: 13:22
 */

namespace libs;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\Message;
use Naixiaoxin\ThinkWechat\Facade;

class WechatMessage
{

    protected $app;
    protected $appid;
   // protected $url = 'https://api.farm.chamshare.cn';
    public function __construct()
    {
        $this->app = Facade::miniProgram();
        $this->appid = config('wechat.mini_program.default.app_id');
    }

    /**
     * 发送小程序模板消息
     * @param string $template_id 模板id
     * @param string $form_id
     * @param string $touser 发送人
     * @param string|null $pagepath
     * @param array $data 消息参数
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function SendTemplateMsg(string $template_id,string $form_id,string $touser,string $pagepath = null,array $data){
        $sendData = [
            'template_id' => $template_id,
            'touser' => $touser,
            'form_id' => $form_id,
            'page' => $pagepath,
            'data' => $data
        ];
        $result = $this->app->template_message->send($sendData);
        if($result['errcode'] !==0){
            Log::error('消息推送失败',$sendData);
        }
        return $result;
    }

    /**
     * 发送一次性订阅消息
     *
     * @param $data 消息参数
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function SendSubscription($data){
        return $this->app->template_message->sendSubscription($data);
    }

    /**
     * 微信公众号消息群发
     * @param $message 消息类
     * @param null $to
     * 当 $to 为整型时为标签 id
     * 当 $to 为数组时为用户的 openid 列表（至少两个用户的 openid）
     * 当 $to 为 null 时表示全部用户
     * @return mixed
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function SendMessage(Message $message, $to = null){
        $this->app = Facade::officialAccount();
        return $this->app->broadcasting->sendMessage($message, $to);
    }

    /**
     * 微信公众号文本消息群发
     * @param string $message
     * @param null $to
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function SendText(string $message,$to = null){
        $this->app = Facade::officialAccount();
        return $this->app->broadcasting->SendText($message, $to);
    }

    /**
     * 微信公众号图片消息群发.
     * @param int $mediaId 图片媒体资源id
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function SendImage(int $mediaId){
        $this->app = Facade::officialAccount();
        return $this->app->broadcasting->sendImage($mediaId);
    }
}
