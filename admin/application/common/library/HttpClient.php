<?php

namespace app\common\library;

use fast\Curl;
use think\Env;
use traits\controller\Jump;

/**
 * @method void post($url, $data = '', $follow_303_with_post = false);
 * @method void get($url, $data = array());
 * author: xiaogang.zhou@qq.com
 * datetime: 2022/9/17 12:49
 * @package app\common\library
 */
class HttpClient {

    private  $_curl;
    private static $_instance = null;
    private $debug = 0;

    use Jump;

    public function __construct() {
        $this->_curl = self::getInstance();
    }

    private static function getInstance() {
        if (!is_null(self::$_instance)) {
            return self::$_instance;
        }
        $_curl = new Curl();
        self::$_instance = $_curl;
        return self::$_instance;
    }

    public function debug($isBug = 1)
    {
        $this->debug = $isBug;
        return $this;
    }

    public function __call($method , $arguments)
    {
        if(!method_exists($this->_curl,$method)){
            $this->error('错误调用');
        }
        $requestEntry = Env::get('api_url'). 'admin/' . $arguments[0];
        $params = $arguments[1];
        array_walk($params, function (&$item) {
            $item = is_array($item) ? json_encode($item) : (string) $item;
        });
        krsort($params);
        $key = md5(md5(json_encode($params , 320)) . Env::get('api_secret'));
        $params['_time'] = NOW;
        $params['_sign'] = $key;
        $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, FALSE);
        $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, FALSE);
        $result = $this->_curl->$method($requestEntry , $params);
        if(1 === $this->debug){
            header("Content-type: text/html; charset=utf-8");
            echo "<pre>";
            print_r($result);
            echo "<pre/>";
            die;
        }
        return $this->returnRes($result);
    }

    /**
     * 错误返回以及跳转
     * @param $result
     * @return array
     */
    private function returnRes($result)
    {
        if (empty($result)) {
            $this->error('服务层请求失败');
        }
        if(intval($result->code) !== 0){
            $this->error( $result->msg ? : '服务层请求失败');
        }
        return  $this->object2array($result);
    }

    private function object2array($array) {
        if(is_object($array)) {
            $array = (array) $array;
        } if(is_array($array)) {
            foreach($array as $key=>$value) {
                $array[$key] = $this->object2array($value);
            }
        }
        return $array;
    }

    public function setJosn()
    {
        self::$_instance->setHeader('Content-Type', 'application/json');
        return $this;
    }
}
