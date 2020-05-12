<?php
declare (strict_types = 1);

/**
 * Created by PhpStorm.
 * User: CTDJ1803111
 * Date: 2019/9/2
 * Time: 10:11
 */

namespace libs;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use think\Exception;

class AliCloud
{

    /**
     * @param $data
     * @return bool
     * @throws ClientException
     * @throws Exception
     * @throws ServerException
     */
    public static function sendSms(array $data)
    {
        try {
            $config = config('ali.cloud');
            $accessKeyId = $config['accessKey_id'];
            $accessSecret = $config['access_secret'];
            AlibabaCloud::accessKeyClient($accessKeyId, $accessSecret)
                ->regionId('cn-hangzhou') // replace regionId as you need
                ->asDefaultClient();
            $PhoneNumbers = $data['phone_numbers'];
            $SignName = $data['sign_name'] ?? '元龙湾MOUMOU农场';
            $TemplateCode = $data['template_code'] ?? 'SMS_173175781';
            $TemplateParam = json_encode($data['template_param'],JSON_UNESCAPED_UNICODE);
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                              'query' => [
                                  'RegionId' => "default",
                                  'PhoneNumbers' => $PhoneNumbers,
                                  'SignName' => $SignName,
                                  'TemplateCode' => $TemplateCode,
                                  'TemplateParam' => $TemplateParam,
                              ],
                          ])
                ->request();
            $result = $result->toArray();
            if($result['Code'] != 'OK'){
                Log::error("短信发送失败",$result);
                throw new Exception("参数不合法");
            }
            return true;
        } catch (ClientException $e) {
            //throw new ClientException($e->getErrorMessage(),$e->getErrorCode());
            Log::error("短信发送失败",$e->getErrorMessage());
            throw new Exception("参数不合法");
        } catch (ServerException $e) {
            //throw new ServerException($e->getErrorMessage());
            Log::error("短信发送失败",$e->getErrorMessage());
            throw new Exception("参数不合法");
        }
    }
}
