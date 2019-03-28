<?php
namespace app\index\controller;

use app\common\controller\Base;
use libs\Log;

class Estest extends Base
{
    public function __construct()
    {
        parent::__construct();
        $header = [
            "Content-type" => "text/html; charset=utf-8",
            'Last-Modified' => gmdate('D, d M Y H:i:s') . ' GMT',
            'ETag' => '5816f349-19'
        ];
        header($header);
    }

    public function hotWords()
    {
        $words = [
            '王者荣耀',
            '阴阳师',
            '美伊',
            '美国和伊拉克',
            '中韩',
            '中国和韩国',
            '中美',
            '中国和美国'
        ];
        echo implode($words,"\n");
    }

    public function synonymWords()
    {
        $words = [
            '王者荣耀,王者农药,农药',
            '阴阳师,刚哥'
        ];
        echo implode($words,"\n");
    }

}