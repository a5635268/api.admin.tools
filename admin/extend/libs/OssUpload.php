<?php
/**
 * Created by PhpStorm.
 * User: CTDJ1803111
 * Date: 2018/12/5
 * Time: 16:43
 */

namespace libs;
use OSS\OssClient;
use think\Log;

class OssUpload
{
    static   $_ossClient;
    static  $ossConfig;

    /**
     * OssUpload constructor.
     * @param $bucket 云存储空间
     * @param $directoryName 上传文件路径
     * @throws \OSS\Core\OssException
     */
    public function __construct()
    {
        $ossConfig = config('upload.oss');
        self::$ossConfig = $ossConfig;
        if (!is_null(self::$_ossClient)) {
            return self::$_ossClient;
        }
        self::$_ossClient = new OssClient($ossConfig['accessKey_id'],$ossConfig['access_secret'],$ossConfig['endpoint']);
        return self::$_ossClient;
    }

    /**
     * 文件上传
     * @param $filePath 本地路径
     * @param string $oldUrl  保持原有结构
     * @param bool $type 使用原名 false重命名
     * @param string $ext
     * @return string|void
     * @throws \OSS\Core\OssException
     */
    public function upload($filePath, $oldUrl = '', $type = true, $ext = '.jpg'){
        if ($oldUrl) {
            $pattern = '/(http|https):\/\/[\w\-_]+(\.[\w\-_]+)+\/?/';
            $path = self::$ossConfig['directory_name'] . preg_replace($pattern, '', pathinfo($oldUrl, PATHINFO_DIRNAME)) . '/' . pathinfo($oldUrl, PATHINFO_BASENAME);
        } else {
            $path = self::$ossConfig['directory_name'] . ($type ? basename($filePath) : md5(time() . rand(1, 10000)) . $ext);
        }
        try{
            self::$_ossClient->uploadFile(self::$ossConfig['bucket'], $path, $filePath);
        } catch(OssException $e) {
            Log::error(__METHOD__ . ':' . __LINE__, 'upload faild', $e->getMessage());
            return;
        }
        return 'http://'.self::$ossConfig['bucket'].'.'.self::$ossConfig['endpoint'].'/'.$path;
    }

    /**
     * 下载文件到本地
     * @param $patch 云存储文件
     * @param $localfile 本地文件名
     * @return string|void
     */
    public function down($patch,$localfile){
        $options = [
            OssClient::OSS_FILE_DOWNLOAD => $localfile
        ];
        try{
            $content = self::$_ossClient->getObject(self::$ossConfig['bucket'], $patch,$options);
        } catch(OssException $e) {
            Log::error(__METHOD__ . ':' . __LINE__, 'down faild', $e->getMessage());
            return;
        }
        return $content;

    }

}
