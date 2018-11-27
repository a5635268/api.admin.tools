<?php

namespace app\index\controller;

use think\Controller;
use think\Request;

class Route extends Controller
{

    // 控制器中间件，但不应该耦合在此处
    // protected $middleware = ['checkhaha'];

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        header("Content-type: text/html; charset=utf-8");
        echo "<pre>";
        print_r($this->request);
        echo "<pre/>";
        die;
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        header("Content-type: text/html; charset=utf-8");
        echo "<pre>";
        print_r($this->request);
        echo "<pre/>";
        die;
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
