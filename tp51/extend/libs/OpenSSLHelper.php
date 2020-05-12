<?php

namespace libs;

/**
 *  基于openssl的des,3des,aes等多模式的加密
 * Class OpenSSLHelper
 * @package libs
 */
class OpenSSLHelper
{
    /**
     * http://tool.chacuo.net/cryptaes
     * var string $method 加解密方法，可通过openssl_get_cipher_methods()获得
     * 【注意】 不要看手册上的，手册上的不全；要直接打印出来；
     */
    protected $method;

    /**
     * var string $secret_key 加解密的密钥
     */
    protected $secret_key;

    /**
     * var string $iv 加解密的向量，有些方法需要设置比如CBC
     */
    protected $iv;

    /**
     * var string $options
     *      1,OPENSSL_RAW_DATA 返回原始未加工的数据，然后通过bin2hex或者base64去转换
     *      0,返回base64格式化后的数据
     *      OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, 返回zeropadding后的原始数据 （用于CBC,zeropadding）
     */
    protected $options;

    /**
     * 构造函数
     *
     * @param string $key 密钥
     * @param string $method 加密方式
     * @param string $iv iv向量
     * @param mixed $options
     *
     *
     */
    public function __construct($key, $method = 'AES-128-ECB', $iv = '', $options = 0)
    {
        // key是必须要设置的
        $this->secret_key = isset($key) ? $key : exit('key为必须项');

        $this->method = $method;

        $this->iv = $iv;

        $this->options = $options;
    }

    /**
     * 返回16进制
     * @param $data
     * @return string
     */
    public function encryptHex($data)
    {
        return bin2hex($this->encrypt($data));
    }

    /**
     * 16进制解密
     * @param $data
     * @return string
     */
    public function decryptHex($data)
    {
        return $this->decrypt(hex2bin($data));
    }

    /**
     * 用于填充
     * @param $data
     * @return string
     */
    private function padding($data)
    {
        if (!strlen($data) % 8) {
            return '';
        }
        return str_pad($data, strlen($data) + 8 - strlen($data) % 8, "\0");
    }


    /**
     * 加密方法，对数据进行加密，返回加密后的数据
     *
     * @param string $data 要加密的数据
     *
     * @return string
     *
     */
    public function encrypt($data)
    {
        // cbc模式是需要填充的
        if(strpos(strtolower($this->method),'cbc')){
            $data = $this->padding($data);
        }
        return openssl_encrypt($data, $this->method, $this->secret_key, $this->options, $this->iv);
    }

    /**
     * 解密方法，对数据进行解密，返回解密后的数据
     *
     * @param string $data 要解密的数据
     *
     * @return string
     *
     */
    public function decrypt($data)
    {
        // cbc模式是需要填充的
        if(strpos(strtolower($this->method),'cbc')){
            $data = $this->padding($data);
        }
        return openssl_decrypt($data, $this->method, $this->secret_key, $this->options, $this->iv);
    }
}

/*
 *  使用示例
 * $key = '12345678';
 * $data = '123456';

    // AES加密模式:ecb,填充:pkcs5padding,数据块:128,返回16进制hex
    $options = OPENSSL_RAW_DATA;
    $openssl = new OpenSSLHelper($key, 'AES-128-ECB', '', $options);
    $hexStr = $openssl->encryptHex($data);
    $data = $openssl->decryptHex($hexStr);
    dd([$data]);

    // 3DES加密模式:CBC(该模式是需要填充的),加密没问题，但解密会出现不可见的非法字符串preg_replace替换一下；
    // 只有ZERO_PADDING填充的才需要特意指定一下，否则不需要特意指定
    $options = OPENSSL_ZERO_PADDING;
    $openssl = new OpenSSLHelper($key, 'DES-EDE3-CBC', $iv , $options);
    $hexStr = $openssl->encrypt($data);
    $data = $openssl->decrypt($hexStr);
    $data = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $data);
 */
