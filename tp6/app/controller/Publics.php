<?php

namespace app\controller;
use app\common\controller\BaseController;
use \Throwable;

class Publics extends BaseController
{
    public function test()
    {
        $data = $_REQUEST;
        $data['uri'] = 'publics/test';
        return json($data);
    }

    public function upload(){
        try {
            if(isset($_POST['name'])){
                $_FILES && $_FILES['files'] && $_FILES['files']['name'] = $_POST['name'];
            }else{
                $_FILES && $_FILES['files'] && $_FILES['files']['name'];
            }
            return $this->uploadFile($filePath = 'uploads', $size = 5120000, $ext = 'jpg,jpeg,JPG,png,gif,blob', $oss = true);
        } catch (Throwable $ex) {
            return $this->returnException($ex);
        }
    }
}
