<?php
/**
 * Created by PhpStorm.
 * User: CTDJ1803111
 * Date: 2018/12/5
 * Time: 16:43
 */

namespace libs;
use OSS\OssClient;

class OssUpload
{
    static   $accessKeyId = 'LTAIpx15RUnaW95Q';   //yourAccessKeyId
    static   $accessKeySecret  = 'S4xlW78AKECC25zXHBbseOlw0B97jJ'; //yourAccessKeySecret
    static   $endpoint   = 'oss-cn-shanghai.aliyuncs.com'; // Endpoint以杭州为例，其它Region请按实际情况填写。
    static   $_ossClient;
    private $bucket;
    private $directoryName;

    /**
     * OssUpload constructor.
     * @param $bucket 云存储空间
     * @param $directoryName 上传文件路径
     * @throws \OSS\Core\OssException
     */
    public function __construct($bucket='',$directoryName='')
    {
        $this->bucket = $bucket ? : 'loongcent';
        $this->directoryName = $directoryName ? :'';
        if (!is_null(self::$_ossClient)) {
            return self::$_ossClient;
        }
        self::$_ossClient = new OssClient(self::$accessKeyId,self::$accessKeySecret,self::$endpoint);
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
            $path = $this->directoryName . preg_replace($pattern, '', pathinfo($oldUrl, PATHINFO_DIRNAME)) . '/' . pathinfo($oldUrl, PATHINFO_BASENAME);
        } else {
            $path = $this->directoryName . ($type ? basename($filePath) : md5(time() . rand(1, 10000)) . $ext);
        }
        try{
            self::$_ossClient->uploadFile($this->bucket, $path, $filePath);
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        return $path;
    }

    /**
     * 下载文件到本地
     * @param $patch 云存储文件
     * @param $localfile 本地文件名
     * @return string|void
     */
    public function down($patch,$localfile){
        $options = array(
            OssClient::OSS_FILE_DOWNLOAD => $localfile
        );
        try{
            $content = self::$_ossClient->getObject($this->bucket, $patch,$options);
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        return $content;

    }

}