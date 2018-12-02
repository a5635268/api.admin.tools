<?php

namespace app\common\service;

use GrpcServer\ClientFactory;
use Userservice\Request;
use libs\Log;
use app\common\facade\ResponsData;

class User
{
    protected $client;
    protected $request;

    private $body;

    const SERVICE_NAME = 'Userservice';
    const APP_ID = 'xg_app';
    const SECRETKEY = 'oCwnF1r1SyHvUFzutFEC0AbwrTkEx17s';

    public function __construct()
    {
        $this->client = ClientFactory::createClient(self::SERVICE_NAME);
        $this->request = new Request();
        $this->request->setAppId(self::APP_ID);
    }

    public function setBoby($body)
    {
        $this->body = $body;
        $body = $this->encryptPass5(json_encode($body));
        $this->request->setBody($body);
        return $this;
    }

    public function send($api)
    {
        list($result, $status) = $this->client
            ->$api($this->request)
            ->wait();
        if($status->code != 0){
            Log::err(__METHOD__,$status->dedetails,$this->body,$api);
            ResponsData::exitJson(ResponsData::returnError(4001));
        }
        if($result->getStatus() !== 0){
            ResponsData::exitJson(ResponsData::returnError($result->getMessage()));
        }
        $data = json_decode($this->decryptPass5($result->getBody()),true);
        return $data;
    }

    /**
     * PHP7.1以下的加密，7以上会报错，后续根据PHP版本做个判断
     * @param $input
     * @return string
     */
    private function encryptPass5($input) {
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $input = $this->pkcs5Pad($input, $size);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = '0102030405060708';
        mcrypt_generic_init($td, GRPC_SECRETKEY, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = bin2hex($data);
        return $data;
    }

    /**
     * 填充
     * @param $text
     * @param $blocksize
     * @return string
     */
    private function pkcs5Pad ($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    /**
     * PHP7.1以下的解密，7以上会报错，后续根据PHP版本做个判断
     * @param $sStr
     * @return bool|string
     */
    private  function decryptPass5($sStr) {
        $iv = '';
        $decrypted= mcrypt_decrypt(
            MCRYPT_RIJNDAEL_128,
            GRPC_SECRETKEY,
            hex2bin($sStr),
            MCRYPT_MODE_ECB,
            $iv
        );
        $dec_s = strlen($decrypted);
        $padding = ord($decrypted[$dec_s-1]);
        $decrypted = substr($decrypted, 0, -$padding);
        return $decrypted;
    }
}