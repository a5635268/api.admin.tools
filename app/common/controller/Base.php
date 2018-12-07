<?php

namespace app\common\controller;

use libs\OssUpload;
use think\Controller;
use traits\ResponsDataBuild;

/**
 * 基控制器
 * Class Base
 * @package app\common\controller
 */
class Base extends Controller
{
    use ResponsDataBuild;

    //验证失败要抛出异常；
    protected $failException = true;

    public function __construct()
    {
        parent::__construct();
    }

    protected function validate($data , $validate , $message = [] , $batch = false , $callback = null)
    {
        // 解决TP5在数据为空时不进行验证的bug,如果后续升级后解决可把该方法撤掉；
        if (empty($data)) {
            if ($this->failException) {
                throw new \think\exception\ValidateException('数据不能为空');
            }
            return $this->validateError('数据不能为空');
        }
        return parent::validate($data , $validate , $message , $batch , $callback);
    }

    // 解决vue跨域的问题可以引用
    // #todo 跨域通过中间件解决
    protected function crossHeader()
    {
        header('Access-Control-Allow-Origin:*');
        // 响应类型
        header('Access-Control-Allow-Methods:POST');
        // 响应头设置
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
    }

    /**
     * 图片上传
     * @access public
     * @param string 上传文件对应的资源文件夹地址
     * @param int $size 限制大小
     * @param string $ext 文件类型
     * @return array
     * @throws \OSS\Core\OssException
     */
    public function upload($filePath='uploads', $size = 5120000, $ext = 'jpg,jpeg,JPG,png,gif')
    {
        $files = request()->file();
        //$images = array();
        $ali = array();
        if(empty($files)){
            return $this->validateError('缺少文件资源');
        }
        foreach($files as $file){
            $info = $file->validate(
                ['size'=>$size,'ext'=>$ext,'mine'=>"image"]
            )->move($filePath);
            if($info){
                // push进数组
                $path = $filePath.'/'.str_replace('\\', '/', $info->getSaveName());
                //array_push($images,$path);
                $OssUpload = new OssUpload();
                array_push($ali,$OssUpload->upload(PUBLIC_PATH.'/'.$path));
            }else{
                return $this->returnError(6, [], $file->getError());
            }
        }
        // 成功上传后 获取上传信息
        return $this->returnRight($ali);
    }
}