<?php
/**
 * Created by PhpStorm.
 * User: lzl
 * Date: 2018/9/7
 * Time: 17:16
 */

namespace app\common\model;

use think\Model;
use think\App;
use traits\ResponsDataBuild;

class Base extends Model
{
    use ResponsDataBuild;

    protected $autoWriteTimeStamp = true;
    // 验证失败要抛出异常；
    protected $failException = true;
    protected static $app;

    protected function initialize()
    {
        parent::initialize();
    }


    /**
     * 解决TP5在数据为空时不进行验证的bug,如果后续升级后解决可把该方法撤掉；
     * @param $data
     * @param null $rule
     * @param null $batch
     * @return array
     */
    protected function validateData($data , $rule = null , $batch = null)
    {
        if (empty($data)) {
            if ($this->failException) {
                throw new \think\exception\ValidateException('数据不能为空');
            }
            return $this->validateError('数据不能为空');
        }
        return parent::validateData($data , $rule , $batch);
    }

    /**
     * 基础的列表获取，里面的各种值转换可以通过获取器获得
     * @param bool $where
     * @param string $fields
     * @param null $order
     * @param int $page
     * @param int $pageSize
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getBaseList($where = true , $fields = '*' , $order = null , $page = 1 , $pageSize = 20)
    {
        $data = [
            'count' => 0 ,
            'list'  => []
        ];
        $count = $this->where($where)->count();
        if (empty($count)) {
            return $this->returnRight($data);
        }
        $order = is_null($order) ? $this->getPk() . ' desc' : $order;
        $list = $this->where($where)->field($fields)->order($order)->page($page , $pageSize)->select();
        $data['count'] = $count;
        $data['list'] = $list;
        return $this->returnRight($data);
    }

    /**
     * 基础的详情获取，里面的各种值转换可以通过获取器获得
     * @param $where
     * @param string $fields
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getBaseOne($where , $fields = '*')
    {
        $res = $this->where($where)->field($fields)->find();
        return $this->returnRight($res);
    }

    /**
     * 构建SQL语句中Where部分条件
     * @param array $args
     * @throws \Exception
     * @return string
     * @see 用法参见ThinkPHP3.2.2模型/查询语言/表达式查询部分介绍
     */
    protected function buildWhereCondition($args)
    {
        if ($args === null || (is_array($args) && sizeof($args) == 0)) {
            return ' 1=1 ';
        } else if (is_array($args) == false)
            throw new \Exception('仅支持通过Array类型参数进行SQL构建! 请检查');
        $sql = '';
        $isNeedLogicOper = false;
        foreach ($args as $k => $v) {
            // 逻辑符号处理
            if ($this->isNumeric($k)) {
                $sql = $sql . ' ' . strtoupper($v) . ' ';
                $isNeedLogicOper = false;
                continue;
            }
            $logicOper = $isNeedLogicOper == true ? 'AND' : '';
            $k = rtrim($k , ' '); // 去除列名中的占位空格符号
            $conditionStr = $k; // 拼装本次条件的列名部分
            if (is_array($v)) {
                $expType = strtoupper(str_replace(' ' , '' , $v[0])); // 空格全部替换, 并大写
                switch ($expType) {
                    case 'EQ':
                        $conditionStr .= '=' . $this->escapeQueryParam($v[1]);
                        break;
                    case 'NEQ':
                        $conditionStr .= '<>' . $this->escapeQueryParam($v[1]);
                        break;
                    case 'GT':
                        $conditionStr .= '>' . $this->escapeQueryParam($v[1]);
                        break;
                    case 'EGT':
                        $conditionStr .= '>=' . $this->escapeQueryParam($v[1]);
                        break;
                    case 'LT':
                        $conditionStr .= '<' . $this->escapeQueryParam($v[1]);
                        break;
                    case 'ELT':
                        $conditionStr .= '<=' . $this->escapeQueryParam($v[1]);
                        break;
                    case 'LIKE':
                        $conditionStr .= ' LIKE ' . $this->escapeQueryParam($v[1]);
                        break;
                    case 'BETWEEN':
                        $rangeArgs = is_array($v[1]) ? $v[1] : explode(',' , $v[1]);
                        $conditionStr .= ' BETWEEN ' . $this->escapeQueryParam($rangeArgs[0]) . ' AND ' . $this->escapeQueryParam($rangeArgs[1]);
                        break;
                    case 'NOTBETWEEN':
                        $rangeArgs = explode(',' , $v[1]);
                        $conditionStr .= ' NOT BETWEEN ' . $this->escapeQueryParam($rangeArgs[0]) . ' AND ' . $this->escapeQueryParam($rangeArgs[1]);
                        break;
                    case 'IN':
                        $caseValueArray = is_array($v[1]) ? $v[1] : explode(',' , $v[1]); // 字符串和数组参数的兼容处理
                        $caseValueStr = '';
                        foreach ($caseValueArray as $caseValue)
                            $caseValueStr .= $this->escapeQueryParam($caseValue) . ',';
                        $caseValueStr = rtrim($caseValueStr , ',');
                        $conditionStr .= ' IN( ' . $caseValueStr . ')';
                        break;
                    case 'NOTIN':
                        $caseValueArray = is_array($v[1]) ? $v[1] : explode(',' , $v[1]); // 字符串和数组参数的兼容处理
                        $caseValueStr = '';
                        foreach ($caseValueArray as $caseValue)
                            $caseValueStr .= $this->escapeQueryParam($caseValue) . ',';
                        $caseValueStr = rtrim($caseValueStr , ',');
                        $conditionStr .= ' NOT IN( ' . $this->escapeQueryParam($v[1]) . ')';
                        break;
                    case 'EXP':
                        $conditionStr .= ' ' . $v[1];
                        break;
                    default:
                        throw new \Exception('指定的条件查询表达式类型' . $expType . '不支持');
                }
            } else if (is_string($v) || is_bool($v) || $this->isNumeric($v)) {
                $conditionStr .= '=' . $this->escapeQueryParam($v);
            } else
                throw new \Exception('条件查询参数类型无法识别, 请仅使用数组以及简单类型');
            $sql = "{$sql} {$logicOper} {$conditionStr} ";
            $isNeedLogicOper = true;
        }
        return $sql;
    }

    /**
     * 过滤处理SQL查询的参数
     * @param mixed $v
     *        要过滤的参数, 字符串或者数字
     * @return string | number    过滤处理后的结果
     */
    private function escapeQueryParam($v)
    {
        return (is_bool($v) ? $v : "'" . addslashes($v) . "'");
    }

    /**
     * 严格判断一个变量是否为数字或数字格式字符串(有前导零的认为不是数字)
     * @param mixed $var
     *        要判断的变量, 如: '123.5', '021'
     * @return boolean 判断结果
     */
    private function isNumeric( $var ){
        if( is_numeric( $var ) === false || ( strpos( $var, '0' ) === 0 && $var != 0 ) )
            return false;
        return true;
    }
}