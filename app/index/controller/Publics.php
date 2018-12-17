<?php
namespace app\index\controller;

use app\common\controller\Base;

use libs\Mail;
use libs\OssUpload;
class Publics extends Base
{
    public function miss()
    {
        return '路由不存在';
    }

    public function test()
    {
       $body = ['uid' => 1];
       $res = service('user')->setBoby($body)->send('GetByUid');
       dd($res);
    }

    public function sendEmail(){
        $subject = '随便测试';
        $body = '1345656';
        $sendTo = '735615901@qq.com';
        Mail::sendMail($subject,$body,$sendTo);
    }

    public function test2($url='https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=192091811,2691395772&fm=173.JPEG'){
        $pathInfo = pathinfo($url);
        $filename = md5($url) . '.' . $pathInfo['extension'];
        $filePath = PUBLIC_PATH. '/image/' . $filename;
        if (!file_exists($filePath)) {
            $bt = file_get_contents($url);
            if (empty($bt)) {
                return '';
            }
            file_put_contents($filePath, $bt);
        }
        $OssUpload = new OssUpload();
        $path = $OssUpload->upload($filePath);
    }
}