<?php
/**
 * 模板类，用于渲染输出
 */
class Template{
    /**
     * 渲染方法
     *
     * @access public
     * @param obj 信息类
     * @param string 模板文件名
     */
    public function render($context, $tpl){
        $closure = function($tpl){
            ob_start();
            include $tpl;
            return ob_end_flush();
        };
        $closure = $closure->bindTo($context, $context);
        $closure($tpl);
    }
}