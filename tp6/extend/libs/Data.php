<?php
declare (strict_types = 1);

namespace libs;
/**
 * 数据处理类
 * @package     tools_class
 */
final class Data
{
    /**
     * 返回多层栏目
     * @param $data 操作的数组
     * @param int $pid 一级PID的值
     * @param string $html 栏目名称前缀
     * @param string $fieldPri 唯一键名，如果是表则是表的主键
     * @param string $fieldPid 父ID键名
     * @param int $level 不需要传参数（执行时调用）
     * @return array
     */
    static public function channelLevel(array $data, int $pid = 0, string $html = "&nbsp;", string $fieldPri = 'cid', string $fieldPid = 'pid', int $level = 1):array
    {
        if (empty($data)) {
            return array();
        }
        $arr = array();
        foreach ($data as $v) {
            if ($v[$fieldPid] == $pid) {
                $arr[$v[$fieldPri]] = $v;
                $arr[$v[$fieldPri]]['_level'] = $level;
                $arr[$v[$fieldPri]]['_html'] = str_repeat($html, $level - 1);
                $arr[$v[$fieldPri]]["_data"] = self::channelLevel($data, $v[$fieldPri], $html, $fieldPri, $fieldPid, $level + 1);
            }
        }
        return $arr;
    }

    /**
     * 获得所有子栏目
     * @param $data 栏目数据
     * @param int $pid 操作的栏目
     * @param string $html 栏目名前字符
     * @param string $fieldPri 表主键
     * @param string $fieldPid 父id
     * @param int $level 等级
     * @return array
     */
    static public function channelList(array $data, int $pid = 0, string $html = "&nbsp;", string $fieldPri = 'cid', string $fieldPid = 'pid', int $level = 1):array
    {
        $data = self::_channelList($data, $pid, $html, $fieldPri, $fieldPid, $level);
        if (empty($data)) return $data;
        foreach ($data as $n => $m) {
            if ($m['_level'] == 1) continue;
            $data[$n]['_first'] = false;
            $data[$n]['_end'] = false;
            if (!isset($data[$n - 1]) || $data[$n - 1]['_level'] != $m['_level']) {
                $data[$n]['_first'] = true;
            }
            if (isset($data[$n + 1]) && $data[$n]['_level'] > $data[$n + 1]['_level']) {
                $data[$n]['_end'] = true;
            }
        }
        return $data;
    }

    //只供channelList方法使用
    static private function _channelList(array $data, int $pid = 0, string $html = "&nbsp;", string $fieldPri = 'cid', string $fieldPid = 'pid', int $level = 1):array
    {
        if (empty($data))
            return array();
        $arr = array();
        foreach ($data as $v) {
            $id = $v[$fieldPri];
            if ($v[$fieldPid] == $pid) {
                $v['_level'] = $level;
                $v['_html'] = str_repeat($html, $level - 1);
                array_push($arr, $v);
                $tmp = self::_channelList($data, $id, $html, $fieldPri, $fieldPid, $level + 1);
                $arr = array_merge($arr, $tmp);
            }
        }
        return $arr;
    }

    /**
     * 获得树状数据
     * @param $data 数据
     * @param $title 字段名
     * @param string $fieldPri 主键id
     * @param string $fieldPid 父id
     * @return array
     */
    static public function tree(array $data, string $title, string $fieldPri = 'cid', string $fieldPid = 'pid'):array
    {
        if (!is_array($data) || empty($data)) return array();
        $arr = Data::channelList($data, 0, '', $fieldPri, $fieldPid);
        foreach ($arr as $k => $v) {
            $str = "";
            if ($v['_level'] > 2) {
                for ($i = 1; $i < $v['_level'] - 1; $i++) {
                    $str .= "│&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                }
            }
            if ($v['_level'] != 1) {
                $t = $title ? $v[$title] : "";
                if (isset($arr[$k + 1]) && $arr[$k + 1]['_level'] >= $arr[$k]['_level']) {
                    $arr[$k]['_name'] = $str . "├─ " . $v['_html'] . $t;
                } else {
                    $arr[$k]['_name'] = $str . "└─ " . $v['_html'] . $t;
                }
            } else {
                $arr[$k]['_name'] = $v[$title];
            }
        }
        //设置主键为$fieldPri
        $data = array();
        foreach ($arr as $d) {
            $data[$d[$fieldPri]] = $d;
        }
        return $data;
    }


    /**
     * 获得所有父级栏目
     * @param $data 栏目数据
     * @param $sid 子栏目
     * @param string $fieldPri 唯一键名，如果是表则是表的主键
     * @param string $fieldPid 父ID键名
     * @return array
     */
    static public function parentChannel(array $data, int $sid, string $fieldPri = 'cid', string $fieldPid = 'pid'):array
    {
        if (!$data) {
            return NULL;
        }
        $arr = array();
        foreach ($data as $v) {
            if ($v[$fieldPri] == $sid) {
                $arr[] = $v;
                $_n = self::parentChannel($data, $v[$fieldPid], $fieldPri, $fieldPid);
                if (!empty($_n)) {
                    $arr = array_merge($arr, $_n);
                }
            }
        }
        return $arr;
    }

    /**
     * 判断$s_cid是否是$d_cid的子栏目
     * @param $data 栏目数据
     * @param $sid 子栏目id
     * @param $pid 父栏目id
     * @param string $fieldPri 主键
     * @param string $fieldPid 父id字段
     * @return bool
     */
    static function isChild(array $data, int $sid, int $pid, string $fieldPri = 'cid', string $fieldPid = 'pid'):bool
    {
        $_data = self::channelList($data, $pid, "", $fieldPri, $fieldPid);
        foreach ($_data as $c) {
            //目标栏目为源栏目的子栏目
            if ($c[$fieldPri] == $sid) return true;
        }
        return false;
    }


    /** 根据父类的id查找出所有子类的id;
     * @param array $cate 所有分类信息的数组;
     * @param string $selfid 当前的id键名;
     * @param string $parentid 当前的pid键名;
     * @param int $id 父类的id(父类id); //最外一层的id;
     */
    static public function getChildsId(array $cate,int $id,string $selfid='id',string $parentid='pid'):array
    {
        $arr = array();
        foreach($cate as $v){
            if($v[$parentid] == $id){
                $arr[] = $v[$selfid];
                $arr = array_merge($arr,self::getChildsId($cate,$v[$selfid],$selfid,$parentid));
            }
        }
        return $arr;
    }

    /** 根据子类的pid查找出所有父类id;
     * @param array $cate 所有分类信息的数组;
     * @param string $selfid 当前的id键名;
     * @param string $parentid 当前的pid键名;
     * @param int $pid 子类指向父类的id(父类id); //最深一层的pid;
     */
    static public function getParentsId(array $cate,int $pid,string $selfid="id",string $parentid="pid"):array
    {
        $arr = array();
        foreach($cate as $v){
            if($v[$selfid] == $pid){
                $arr[] = $v[$selfid];
                $arr = array_merge(self::getParentsId($cate,$v[$parentid],$selfid,$parentid),$arr);
            }
        }
        return $arr;
    }

    /**
     * 递归实现迪卡尔乘积
     * @param $arr 操作的数组
     * @param array $tmp
     * @return array
     */
    static function descarte(array $arr, array $tmp = array()):array
    {
        static $n_arr = array();
        foreach (array_shift($arr) as $v) {
            $tmp[] = $v;
            if ($arr) {
                self::descarte($arr, $tmp);
            } else {
                $n_arr[] = $tmp;
            }
            array_pop($tmp);
        }
        return $n_arr;
    }

}
