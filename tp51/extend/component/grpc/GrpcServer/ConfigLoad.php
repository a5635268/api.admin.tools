<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/29
 * Time: 17:42
 */

namespace GrpcServer;

use think\Exception;
use think\facade\Env;


class ConfigLoad implements \ArrayAccess
{
    protected $config;
    protected $hostConfig;
    protected $configPath;
    protected $protoPath;
    protected $hostname;

    public function __construct()
    {
        $this->configPath = Env::get('config_path') . 'grpc.php';
        $this->protoPath = __DIR__ . '/proto/';
        $this->hostConfig =config('host.');
        $this->config = config('grpc.');
       if (file_exists($this->configPath) && filemtime($this->configPath) >= filemtime($this->protoPath)) {
            $this->config = require $this->configPath;
        } else {
            $this->config = $this->loadProto();
            file_put_contents($this->configPath, "<?php\r\nreturn " . var_export($this->config, true) . ";");
        }
    }

    public function getHostname($name)
    {
        if (!isset($this->hostConfig[$name])) {
            throw new Exception('没有定义这个服务的host');
        }
        $hosts = $this->hostConfig[$name];
        if (isset($hosts[WEB_VERSION])) {
            // 此处动态定义: 可以把端口动态的写进redis里面。然后这里从redis里面读出来
            $host = $hosts[WEB_VERSION];
        } else {
            if (!isset($hosts['develop'])) {
                throw new Exception('没有定义这个服务的host');
            }
            $host = $hosts['develop'];
        }
        return $host;
    }

    public function __get($name)
    {
        if (isset($this->config[$name])) {
            return $this->config[$name];
        }
    }

    private function loadProto()
    {
        $file = new \FilesystemIterator($this->protoPath, \FilesystemIterator::SKIP_DOTS);
        if (iterator_count($file) <= 0) {
            throw new Exception("没有proto文件");
        }
        $proto = [];
        foreach ($file as $f) {
            $proto += $this->parseProto(file_get_contents($f->getPathname()));
        }
        return $proto;
    }

    private function parseProto($contents)
    {
        preg_match('/package\s+(\w+)\s*;/', $contents, $package);
        preg_match('/service\s+(\w+)\s+\{/', $contents, $service);
        preg_match_all('/rpc\s+(\w+)\s*\((\w+)\)\s*returns\s*\((\w+)\)/', $contents, $rpcMethod);
        if (!empty($package)) {
            $packageName = $package[1];
        }
        if (empty($service) || empty($rpcMethod)) {
            throw new Exception('proto文件定义错误,没有service或rpc方法');
        }
        $serviceName = $service[1];
        $serviceName =( $serviceName == 'UserService' ? 'Userservice' : $serviceName);
        list($none, $rpcMethodName, $rpcMethodRequest, $rpcMethodResponse) = $rpcMethod;
        $rpcConfig = [];
        $keyname   = $packageName ? $packageName . "." . $serviceName : $serviceName;
        foreach ($rpcMethodName as $key => $name) {
            $rpcConfig[$keyname]['method'][trim($name)] = [
                'request'  => ($packageName ? ucfirst($packageName) : $serviceName) . "\\" . trim($rpcMethodRequest[$key]),
                'response' => ($packageName ? ucfirst($packageName) : $serviceName) . "\\" . trim($rpcMethodResponse[$key])
            ];
        }
        return $rpcConfig;
    }

    public function offsetExists($offset)
    {
        return isset($this->config[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->config[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->config[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->config[$offset]);
    }

}