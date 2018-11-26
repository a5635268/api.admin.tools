<?php

namespace traits;

use libs\Log;

/**
 * 用于构建响应消息体，为了方便多个类统一继承所以放在traits中
 * Trait ResponsDataBuild
 * @package traits
 */
trait ResponsDataBuild
{

    /**
     * 直接返回调用成功的消息
     * @param string $msg
     * @param int $code
     * @return array
     */
    protected function returnSucc($msg='',$code=0){
        return response_data($code , [] , $msg);
    }

    /**
     * 直接返回调用成功的数据
     * @param array $data
     * @param int $code
     * @param string $msg
     * @return array
     */
    protected function returnRight($data=[],$code=0,$msg=''){
        return response_data($code , $data , $msg);
    }

    /**
     * 直接返回调用失败的消息
     * @param $code
     * @param array $data
     * @param string $msg
     * @return array
     */
    protected function returnError($code=1,$data=[],$msg=''){
        return response_data($code , $data , $msg);
    }

    /**
     * 直接返回验证错误数据
     * @param $msg
     * @return array
     */
    protected function validateError($msg='')
    {
        return response_data(21 , [] , $msg);
    }

    /**
     * 内部的模型层处理错误
     * @param string $msg
     */
    protected function modelError($msg='',$data = []){
        $msg = $this->getErrorMsg(10) . (config('app_debug') ? ':' . $msg : '');
        return response_data(10,$data,$msg);
    }

    /**
     * 错误返回，这里的错误返回只返回两种错误，validateError和modelError；
     * @param \Exception $ex
     * @return array
     */
    protected function returnException(\Exception $ex){
        $latest = current($ex->getTrace());
        $source = $latest['function'];
        if(in_array($source,['validate','validateData'])){
            return $this->validateError($ex->getMessage());
        }
        
        $trace = trace_handle($ex->getTrace());
        $tracData = config('app_debug') ? $trace : [] ;
        if(in_array($source,['thrError'])){
            return $this->returnError($ex->getCode(),[],$ex->getMessage());
        }
        Log::err('内部模型处理错误: '.$ex->getMessage(),$trace);
        return $this->modelError($ex->getMessage());
    }

    /**
     * 返回json，并退出程序
     * @param $data
     */
    protected function exitJson($data){
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($data));
    }

    /**
     * 数组格式索引重建
     * Author: Zhou xiaogang
     * Date: 2017/11/26
     * Time: 16:02
     * @param $dataArray
     * @param $newIndexSource
     * @param string $delimiter
     * @param bool $unsetIndexKey
     * @return array
     */
    protected function resetArrayIndex( $dataArray, $newIndexSource, $delimiter = ':', $unsetIndexKey = false ){
        $resultArray = array();
        foreach( $dataArray as $k=>$v ) {
            // string格式的单key索引, 则直接赋值, 继续下一个
            if( is_string( $newIndexSource ) ) {
                $resultArray[$v[$newIndexSource]] = $v;
                if( $unsetIndexKey )
                    unset( $v[$newIndexSource] );
                continue;
            }
            // 数组格式多key组合索引处理
            $k = '';
            foreach( $newIndexSource as $index ) {
                $k .= "{$v[$index]}{$delimiter}";
                if( $unsetIndexKey )
                    unset( $v[$index] );
            }
            $k = rtrim( $k, $delimiter );
            $resultArray[$k] = $v;
        }
        return $resultArray;
    }

    public function thrError($code=1,$message=''){
        throw new \Exception($message,$code);
    }

    protected function getErrorMsg($code){
        $errorMsg = config('error.code');
        return $errorMsg[$code] ? : '返回消息未定义';
    }

    public function arrObjToArray($arr){
        if(!is_array($arr)){
            return [];
        }
        foreach ($arr as $k=>&$v){
            $v = $v->toArray();
        }
        return $arr;
    }

    public function arrSum($arr,$value){
        $total = 0;
        foreach ($arr as $v){
            $total += $v[$value];
        }
        return $total;
    }
}