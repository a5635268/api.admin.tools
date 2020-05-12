<?php

namespace app\common\library;

use fast\Curl;

class HttpClient {

    private  $_curl;
    private static $_instance = null;
    private $debug = 0;

    use \traits\controller\Jump;

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

    public function debug($isBug = true)
    {
        $this->debug = $isBug;
        return $this;
    }

    public function __call($method , $arguments)
    {
        if(!method_exists($this->_curl,$method)){
            $this->error('错误调用');
        }
        $requestEntry = Config('api') . $arguments[0];
        $params = $arguments[1];
        array_walk($params, function (&$item) {
            $item = is_array($item) ? json_encode($item) : (string) $item;
        });
        krsort($params);
        $key = md5(md5(json_encode($params)) . Config('api_sign_key'));
        $params['_sign'] = $key;
        //        $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, FALSE);
        //        $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, FALSE);
        $result = $this->_curl->$method($requestEntry,$params);
        if(true === $this->debug){
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
        if($result->code !== 0){
            $this->error( $result->message ? : '服务层请求失败');
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