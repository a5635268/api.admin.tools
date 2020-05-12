<?php

namespace traits;
use think\Exception;
use Throwable;

/**
 * 用于构建响应消息体，为了方便多个类统一继承所以放在traits中
 * Trait ResponsDataBuild
 * @package traits
 */
trait ResponsDataBuild
{
    /**
     * 消息返回体构建
     * @param int $code
     * @param array $data
     * @param string $msg
     * @return array
     */
    private function buildBody(int $code = 0 , $data = [] , string $msg = '')
    {
        $errorMsg = config('code');
        $arr = [
            'code'    => $code ,
            'msg' => $msg ?: ($errorMsg[$code] ?: '返回消息未定义') ,
            'data'    => $data
        ];
        return json($arr);
    }

    /**
     * 递归格式化参数全部为string
     * @param $data
     * @return array
     */
    private function deepTransArr(array $data): array
    {
        if (empty($data))
            return $data;
        if ($data instanceof \think\model) {
            $data = $data->toArray();
        }
        if (!is_array($data))
            return $data;
        foreach ($data as $k => &$v) {
            if (is_array($v) || is_object($v)) {
                $arr = (array)$v;
                if (empty($arr)) {
                    continue;
                }
                $v = $this->deepTransArr($v);
            }
            if (!is_string($v) && !is_array($v))
                $v = (string)$v;
        }
        return $data;
    }

    /**
     * 直接返回调用成功的消息
     * @param string $msg
     * @param int $code
     * @return array
     */
    protected function returnSucc(string $msg = '' , int $code = 0)
    {
        return $this->buildBody($code , [] , $msg);
    }

    /**
     * 直接返回调用成功的数据
     * @param array|object $data
     * @param int $code
     * @param string $msg
     * @return array
     */
    protected function returnRight($data = [] , int $code = 0 , string $msg = '')
    {
        return $this->buildBody($code , $data , $msg);
    }

    /**
     * 直接返回调用失败的消息
     * @param $code
     * @param array $data
     * @param string $msg
     * @return array
     */
    protected function returnError(int $code = 1 , array $data = [] , string $msg = '')
    {
        return $this->buildBody($code , $data , $msg);
    }

    /**
     * 直接返回验证错误数据
     * @param $msg
     * @return array
     */
    protected function validateError(string $msg = '')
    {
        return $this->buildBody(21 , [] , $msg);
    }

    /**
     * 内部的模型层处理错误
     * @param string $msg
     * @param array $data
     * @return array
     */
    protected function modelError(string $msg = '' , array $data = [])
    {
        $msg = $this->getErrorMsg(10) . (env('app_debug') ? ':' . $msg : '');
        return $this->buildBody(10 , $data , $msg);
    }

    /**
     * 错误返回，这里的错误返回只返回两种错误，validateError和modelError；
     * @param Throwable $ex
     * @return array
     */
    protected function returnException(Throwable $ex)
    {
        $latest = current($ex->getTrace());
        $source = $latest['function'];
        if (in_array($source , ['validate' , 'validateData','check'])) {
            return $this->validateError($ex->getMessage());
        }
        if (in_array($source , ['thrError'])) {
            return $this->returnError($ex->getCode() , [] , $ex->getMessage());
        }
        return $this->modelError($ex->getMessage());
    }

    /**
     * 返回json，并退出程序
     * @param $data
     */
    protected function exitJson(array $data): void
    {
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($data));
    }

    /**
     * 数组格式索引重建
     * 更多可以查看：\think\Collection
     * 也可以参考 https://xueyuanjun.com/post/178
     * Author: Zhou xiaogang
     * Date: 2017/11/26
     * Time: 16:02
     * @param array $dataArray
     * @param $newIndexSource
     * @param string $delimiter
     * @param bool $unsetIndexKey
     * @return array
     */
    protected function resetArrayIndex($dataArray , $newIndexSource , string $delimiter = ':' , bool $unsetIndexKey = false): array
    {
        $resultArray = [];
        foreach ($dataArray as $k => $v) {
            // string格式的单key索引, 则直接赋值, 继续下一个
            if (is_string($newIndexSource)) {
                $resultArray[$v[$newIndexSource]] = $v;
                if ($unsetIndexKey)
                    unset($v[$newIndexSource]);
                continue;
            }
            // 数组格式多key组合索引处理
            $k = '';
            foreach ($newIndexSource as $index) {
                $k .= "{$v[$index]}{$delimiter}";
                if ($unsetIndexKey)
                    unset($v[$index]);
            }
            $k = rtrim($k , $delimiter);
            $resultArray[$k] = $v;
        }
        return $resultArray;
    }

    /**
     * 数据集对象转换为数组
     * #todo: 后续再完善一下
     * @param array $arr
     * @return array
     */
    public function arrObjToArray(array $arr): array
    {
        if (!is_array($arr)) {
            return [];
        }
        foreach ($arr as $k => &$v) {
            $v = $v->toArray();
        }
        return $arr;
    }


    /**
     * 异常和错误的抛出
     * author: xiaogang.zhou@qq.com
     * datetime: 2019/10/30 19:10
     * @param int $code
     * @param string $message
     * @throws Exception
     */
    public function thrError(int $code = 1 , string $message = ''): void
    {
        $message = $message ? : $this->getErrorMsg($code);
        throw new Exception($message , $code);
    }

    /**
     * 获取错误消息
     * @param int $code
     * @return string
     */
    protected function getErrorMsg(int $code): string
    {
        $errorMsg = config('code');
        return $errorMsg[$code] ?: '返回消息未定义';
    }
}
