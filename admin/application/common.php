<?php

// 公共助手函数

if (!function_exists('__')) {

    /**
     * 获取语言变量值
     * @param string $name 语言变量名
     * @param array $vars 动态变量值
     * @param string $lang 语言
     * @return mixed
     */
    function __($name, $vars = [], $lang = '')
    {
        if (is_numeric($name) || !$name)
            return $name;
        if (!is_array($vars)) {
            $vars = func_get_args();
            array_shift($vars);
            $lang = '';
        }
        return \think\Lang::get($name, $vars, $lang);
    }

}

/**
 * 实例化服务层
 * @param $name
 * @return \think\Controller
 */
if (!function_exists('service')) {
    function service($name)
    {
        $nameArr = explode('/' , $name);
        if (1 == count($nameArr)) {
            $name = 'common/' . $name;
        }
        return controller($name , 'service');
    }
}

if (!function_exists('format_bytes')) {

    /**
     * 将字节转换为可读文本
     * @param int $size 大小
     * @param string $delimiter 分隔符
     * @return string
     */
    function format_bytes($size, $delimiter = '')
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        for ($i = 0; $size >= 1024 && $i < 6; $i++)
            $size /= 1024;
        return round($size, 2) . $delimiter . $units[$i];
    }

}

if (!function_exists('datetime')) {

    /**
     * 将时间戳转换为日期时间
     * @param int $time 时间戳
     * @param string $format 日期时间格式
     * @return string
     */
    function datetime($time, $format = 'Y-m-d H:i:s')
    {
        $time = is_numeric($time) ? $time : strtotime($time);
        return date($format, $time);
    }

}

if (!function_exists('human_date')) {

    /**
     * 获取语义化时间
     * @param int $time 时间
     * @param int $local 本地时间
     * @return string
     */
    function human_date($time, $local = null)
    {
        return \fast\Date::human($time, $local);
    }

}

if (!function_exists('cdnurl')) {

    /**
     * 获取上传资源的CDN的地址
     * @param string $url 资源相对地址
     * @param boolean $domain 是否显示域名 或者直接传入域名
     * @return string
     */
    function cdnurl($url, $domain = false)
    {
        $url = preg_match("/^https?:\/\/(.*)/i", $url) ? $url : \think\Config::get('upload.cdnurl') . $url;
        if ($domain && !preg_match("/^(http:\/\/|https:\/\/)/i", $url)) {
            if (is_bool($domain)) {
                $public = \think\Config::get('view_replace_str.__PUBLIC__');
                $url = rtrim($public, '/') . $url;
                if (!preg_match("/^(http:\/\/|https:\/\/)/i", $url)) {
                    $url = request()->domain() . $url;
                }
            } else {
                $url = $domain . $url;
            }
        }
        return $url;
    }

}


if (!function_exists('is_really_writable')) {

    /**
     * 判断文件或文件夹是否可写
     * @param    string $file 文件或目录
     * @return    bool
     */
    function is_really_writable($file)
    {
        if (DIRECTORY_SEPARATOR === '/') {
            return is_writable($file);
        }
        if (is_dir($file)) {
            $file = rtrim($file, '/') . '/' . md5(mt_rand());
            if (($fp = @fopen($file, 'ab')) === FALSE) {
                return FALSE;
            }
            fclose($fp);
            @chmod($file, 0777);
            @unlink($file);
            return TRUE;
        } elseif (!is_file($file) OR ($fp = @fopen($file, 'ab')) === FALSE) {
            return FALSE;
        }
        fclose($fp);
        return TRUE;
    }

}

if (!function_exists('rmdirs')) {

    /**
     * 删除文件夹
     * @param string $dirname 目录
     * @param bool $withself 是否删除自身
     * @return boolean
     */
    function rmdirs($dirname, $withself = true)
    {
        if (!is_dir($dirname))
            return false;
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dirname, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }
        if ($withself) {
            @rmdir($dirname);
        }
        return true;
    }

}

if (!function_exists('copydirs')) {

    /**
     * 复制文件夹
     * @param string $source 源文件夹
     * @param string $dest 目标文件夹
     */
    function copydirs($source, $dest)
    {
        if (!is_dir($dest)) {
            mkdir($dest, 0755, true);
        }
        foreach (
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item
        ) {
            if ($item->isDir()) {
                $sontDir = $dest . DS . $iterator->getSubPathName();
                if (!is_dir($sontDir)) {
                    mkdir($sontDir, 0755, true);
                }
            } else {
                copy($item, $dest . DS . $iterator->getSubPathName());
            }
        }
    }

}

if (!function_exists('mb_ucfirst')) {

    function mb_ucfirst($string)
    {
        return mb_strtoupper(mb_substr($string, 0, 1)) . mb_strtolower(mb_substr($string, 1));
    }

}

if (!function_exists('addtion')) {

    /**
     * 附加关联字段数据
     * @param array $items 数据列表
     * @param mixed $fields 渲染的来源字段
     * @return array
     */
    function addtion($items, $fields)
    {
        if (!$items || !$fields)
            return $items;
        $fieldsArr = [];
        if (!is_array($fields)) {
            $arr = explode(',', $fields);
            foreach ($arr as $k => $v) {
                $fieldsArr[$v] = ['field' => $v];
            }
        } else {
            foreach ($fields as $k => $v) {
                if (is_array($v)) {
                    $v['field'] = isset($v['field']) ? $v['field'] : $k;
                } else {
                    $v = ['field' => $v];
                }
                $fieldsArr[$v['field']] = $v;
            }
        }
        foreach ($fieldsArr as $k => &$v) {
            $v = is_array($v) ? $v : ['field' => $v];
            $v['display'] = isset($v['display']) ? $v['display'] : str_replace(['_ids', '_id'], ['_names', '_name'], $v['field']);
            $v['primary'] = isset($v['primary']) ? $v['primary'] : '';
            $v['column'] = isset($v['column']) ? $v['column'] : 'name';
            $v['model'] = isset($v['model']) ? $v['model'] : '';
            $v['table'] = isset($v['table']) ? $v['table'] : '';
            $v['name'] = isset($v['name']) ? $v['name'] : str_replace(['_ids', '_id'], '', $v['field']);
        }
        unset($v);
        $ids = [];
        $fields = array_keys($fieldsArr);
        foreach ($items as $k => $v) {
            foreach ($fields as $m => $n) {
                if (isset($v[$n])) {
                    $ids[$n] = array_merge(isset($ids[$n]) && is_array($ids[$n]) ? $ids[$n] : [], explode(',', $v[$n]));
                }
            }
        }
        $result = [];
        foreach ($fieldsArr as $k => $v) {
            if ($v['model']) {
                $model = new $v['model'];
            } else {
                $model = $v['name'] ? \think\Db::name($v['name']) : \think\Db::table($v['table']);
            }
            $primary = $v['primary'] ? $v['primary'] : $model->getPk();
            $result[$v['field']] = $model->where($primary, 'in', $ids[$v['field']])->column("{$primary},{$v['column']}");
        }

        foreach ($items as $k => &$v) {
            foreach ($fields as $m => $n) {
                if (isset($v[$n])) {
                    $curr = array_flip(explode(',', $v[$n]));

                    $v[$fieldsArr[$n]['display']] = implode(',', array_intersect_key($result[$n], $curr));
                }
            }
        }
        return $items;
    }

}

if (!function_exists('var_export_short')) {

    /**
     * 返回打印数组结构
     * @param string $var 数组
     * @param string $indent 缩进字符
     * @return string
     */
    function var_export_short($var, $indent = "")
    {
        switch (gettype($var)) {
            case "string":
                return '"' . addcslashes($var, "\\\$\"\r\n\t\v\f") . '"';
            case "array":
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value) {
                    $r[] = "$indent    "
                        . ($indexed ? "" : var_export_short($key) . " => ")
                        . var_export_short($value, "$indent    ");
                }
                return "[\n" . implode(",\n", $r) . "\n" . $indent . "]";
            case "boolean":
                return $var ? "TRUE" : "FALSE";
            default:
                return var_export($var, TRUE);
        }
    }
}

if (!function_exists('d')) {
    /**
     * 强大的打印调试函数
     * https://symfony.com/doc/current/components/var_dumper.html
     * @param $var
     * @return array
     */
    function d($var)
    {
        foreach (func_get_args() as $v) {
            if (is_string($v) || is_numeric($v)) {
                $v = new \ErrorException(
                    $v , 1 , E_WARNING
                );
            }
            Symfony\Component\VarDumper\VarDumper::dump($v);
        }
        if (1 < func_num_args()) {
            return func_get_args();
        }
        return $var;
    }
}

if (!function_exists('dd')) {
    /**
     * 断掉的d
     * @param $var
     */
    function dd($var)
    {
        foreach (func_get_args() as $v) {
            d($v);
        }
        die;
    }
}

if(!function_exists('Curl')){
    function Curl()
    {
        static $_curl = null;
        if(!is_null($_curl)){
            return $_curl;
        }
        $_curl = new Curl\Curl();
        return $_curl;
    }
}

/**
 * 检测是postman才打印的函数
 * author: xiaogang.zhou@qq.com
 * datetime: 2020/4/23 13:29
 * @param $var
 * @param bool $die
 */
function pdd($var,$die = true)
{
    $check =  \think\Request::instance()->header('Postman-Token');
    if(empty($check)){
        return ;
    }
    $die && dd($var);
    d($var);
}

function division($val1,$val2, $point = 2)
{
    if ($val2 == 0){
        return 0;
    }
    return round($val1/$val2, 2);
}

/**
 * 比例相差值（同比，环比）
 * author: xiaogang.zhou@qq.com
 * datetime: 2021/10/26 13:21
 * @param $val1
 * @param $val2
 * @return float|string
 */
function rate($val1, $val2)
{
    if ($val2 == 0 || $val1 == 0){
        return '-';
    }
    return round(($val1-$val2)/$val2 * 100, 2);
}

function modelDataIndexId($data , $id="id")
{
    if (empty($data)){
        return [];
    }
    $data = ($data[0] instanceof Model) ? collect($data)->toArray() : $data;
    return resetArrayIndex($data, $id);
}

function resetArrayIndex($dataArray , $newIndexSource , string $delimiter = ':' , bool $unsetIndexKey = false)
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
 * 实例化服务层
 * @param $name
 * @return \think\Controller
 */
function service($name)
{
    /*    $nameArr = explode('/',$name);
        if(1 == count($nameArr)){
            $name = 'common/'. $name;
        }*/
    return controller($name, 'service');
}

if (!function_exists('price_format')) {
    define_once('PRICE_FORMAT_FLOAT', 'float');
    define_once('PRICE_FORMAT_STRING', 'string');

    /**
     * @param $val
     * @param string $returnType PRICE_FORMAT_FLOAT|PRICE_FORMAT_STRING
     * @param int $decimals
     * @return float|string
     */
    function price_format($val, $returnType = 'string', $decimals = 2)
    {
        $val = floatval($val);
        $result = number_format($val, $decimals, '.', '');
        if ($returnType === PRICE_FORMAT_FLOAT) {
            return (float)$result;
        }
        return $result;
    }
}


//导出excel
function exportExcel($data, $col_names = array(), $title = '', $sheet_title = '', $type = 0)
{
    if (!$data) return false;
    $letters = ['A' , 'B' , 'C' , 'D' , 'E' , 'F' , 'G' , 'H' , 'I' , 'J' , 'K' , 'L' , 'M' , 'N' , 'O' , 'P' , 'Q' , 'R' , 'S' , 'T' , 'U' , 'V' , 'W' , 'X' , 'Y' , 'Z' , 'AA' , 'AB' , 'AC' , 'AD' , 'AE' , 'AF' , 'AG' , 'AH' , 'AI' , 'AJ' , 'AK' , 'AL' , 'AM' , 'AN'];
    $col_count = count(current($data)); //总共的列数
    $col_names = $col_names ?: array_keys(current($data)); //列名
    $row_count = count($data);
    $title = $title ?: 'export-' . date('Y-m-d-h-i-s'); //标题
    $title .= '.xls';
    $sheet_title = $sheet_title ?: 'sheet1';

    $objPHPExcel = new \PHPExcel();

    //列宽自适应
    for ($i = 0; $i < $col_count; $i++) {
        $objPHPExcel->getActiveSheet()->getColumnDimension($letters[$i])->setAutoSize(true);
    }

    //统一行高
    for ($i = 1; $i <= $row_count + 1; $i++) {
        $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(22);
    }

    //列名分配
    for ($i = 0; $i <= $col_count; $i++) {
        $col_name = $letters[$i] . '1';
        $objPHPExcel->getActiveSheet()->setCellValue($col_name, $col_names[$i]);
    }

    //内容填充
    $i = 2;
    foreach ($data as $k => $v) {
        $j = 0;
        foreach ($v as $kk => $vv) {
            $col_name = $letters[$j] . $i;
            $objPHPExcel->getActiveSheet()->setCellValue($col_name, $vv);
            $j++;
        }
        $i++;
    }

    $objPHPExcel->setActiveSheetIndex(0); //设置当前的sheet
    $objPHPExcel->getActiveSheet()->setTitle($sheet_title);//设置s当前的sheet的name

    //锁定表头
    if ($type) {
        $objPHPExcel->getActiveSheet()->freezePane("A2");
    }

    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $title . '.xlsx"');
    header('Cache-Control: max-age=0');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: cache, must-revalidate');

    $objWriter->save("php://output");
}
