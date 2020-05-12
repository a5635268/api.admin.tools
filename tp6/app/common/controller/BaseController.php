<?php
declare (strict_types = 1);

namespace app\common\controller;

use libs\Log;
use libs\OssUpload;
use think\App;
use think\exception\ValidateException;
use think\Validate;
use traits\ResponsDataBuild;
use \Throwable;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    use ResponsDataBuild;

    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [
        // 全局中间件
        \app\common\middleware\Validate::class
    ];

    /**
     * 接受的request参数以及路由参数
     * @var array
     */
    protected $params = [];

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;
        $this->params = array_merge($this->request->param(), $this->request->route());

        // 控制器初始化
        $this->initialize();
        $this->crossHeader();
    }

    // 初始化
    protected function initialize()
    {}

    protected function crossHeader()
    {
        header('Access-Control-Allow-Origin:*');
        // 响应类型
        header('Access-Control-Allow-Methods:*');
        // 响应头设置
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
    }

    /**
     * 图片上传
     * @access public
     * @param string 上传文件对应的资源文件夹地址
     * @param int $size 限制大小
     * @param string $ext 文件类型
     * @param bool $oss 是否上传oss
     * @return array
     * @throws \OSS\Core\OssException
     */
    protected function uploadFile($filePath = 'uploads', $size = 5120000, $ext = 'jpg,jpeg,JPG,png,gif,blob', $oss = false)
    {
        $files = request()->file();
        try {
            $images = array();
            $imags = array();
            if (empty($files)) {
                return $this->returnError(1, [], '缺少文件资源');
            }
           validate(['image'=>'filesize:'.$size.',|fileExt:'.$ext.'|image:200,200,jpg'])
                ->check($files);
//            validate(['files'=>['fileSize' => $size, 'fileExt' => $ext, 'fileMime' => 'image/jpeg,image/png,image/gif']])->check($files);
            foreach ($files as $file) {
                    // push进数组
                    $path = \think\facade\Filesystem::putFile( 'images', $file);
                    $path = str_replace('\\', '/', $path);
                    array_push($images, $path);
                    array_push($imags, config('database.api_domain') .'storage/'. $path);
            }
            if ($oss == true) {
                $OssUpload = new OssUpload('','upload/estate/'.date('Ymd').'/');
                $ali = array();
                foreach ($images as $v) {
                    $ali[] = $OssUpload->upload(app()->getRootPath() . 'public/storage/' . $v);
                }
                return $this->returnRight($ali);
            }
            // 成功上传后 获取上传信息
            return $this->returnRight($imags);
        } catch (Throwable $e) {
            return $this->returnException($e);
        }
    }

    /**
     * 生成规则‘业务代码+会员ID+年月日时分秒’，体现信息：“什么会员在什么时间的什么行为”
     * @param $memberId
     * @param string $businessNo 业务代码
     * @return string
     */
    protected function createOrderNo(string $uid,string $businessNo = null):string
    {
        $time = time();
        $businessNo = is_null($businessNo) ? '01' : $businessNo;
        return $businessNo . str_pad($uid, 7, '0', STR_PAD_LEFT) . date('ymdHis',$time);
    }

}
