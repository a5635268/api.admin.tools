<?php

namespace libs;

use app\common\facade\Http;
use GuzzleHttp\Client;

/**
 * 基于GuzzleHttp的请求工厂， 待完成异步，并发，文件传输等高级属性
 * Class HttpClient
 * @package libs
 */
class HttpClient
{
    private $_handle;
    private static $_instance = null;
    private $debug = 0;

    public function __construct($options = []) {
        $config = config('http');
        $options = array_merge($config,$options);
        $this->_handle = self::getInstance($options);
    }

    private static function getInstance($options) {
        if (!is_null(self::$_instance)) {
            return self::$_instance;
        }
        $_handle = new Client($options);
        self::$_instance = $_handle;
        return self::$_instance;
    }

    public function debug($isBug = true)
    {
        $this->debug = $isBug;
        return $this;
    }

    public function __call($method , $arguments)
    {
        if(!in_array(strtolower($method),['get','post','put','delete'])){
            exception('no method in GuzzleHttp');
        }
        count($arguments) == 3 || $arguments[] = null;
        list($url,$params,$format) = $arguments;
        $format = $format ? $format : 'json';
        $options = [];
        $this->debug && $options['debug'] = true;
        if($method !== 'get'){
            $format == 'json' ?  $options['json'] = $params : $options['form_params'] = $params;
        }else{
            $options['query'] = $params;
        }
        $result = $this->_handle->request($method,$url,$options);
        $result = $result->getBody()->getContents();
        $returns = json_decode($result,true);
        return $returns ? : $result;
    }
}
