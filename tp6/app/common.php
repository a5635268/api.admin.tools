<?php
// 应用公共文件

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
                $v, 1, E_WARNING
            );
        }
        Symfony\Component\VarDumper\VarDumper::dump($v);
    }
    if (1 < func_num_args()) {
        return func_get_args();
    }
    return $var;
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
    $check = \think\facade\Request::header('Postman-Token');
    if(empty($check)){
        return ;
    }
    $die && dd($var);
    d($var);
}

/**
 * 创建原生redis对象，非原生的会有一个zscan死循环的bug
 * bug：https://github.com/phpredis/phpredis/issues/1402
 * @return null|Redis
 */
function redis()
{
    static $_redis = null;
    if (!is_null($_redis)) {
        return $_redis;
    }
    $_redis = new \Redis();
    $config = config('cache.stores.redis');
    $_redis->connect($config['host'] , $config['port']);
    return $_redis;
}

/**
 * 计算出两个日期之间的月份
 * @author Eric
 * @param  [type] $start_date [开始日期，如2014-03]
 * @param  [type] $end_date   [结束日期，如2015-12]
 * @param  string $explode    [年份和月份之间分隔符，此例为 - ]
 * @param  boolean $addOne    [算取完之后最后是否加一月，用于算取时间戳用]
 * @return [type]             [返回是两个月份之间所有月份字符串]
 */
function dateMonths($start_date,$end_date,$explode='-',$addOne=false){
    //判断两个时间是不是需要调换顺序
    $start_int = strtotime($start_date);
    $end_int = strtotime($end_date);
    if($start_int > $end_int){
        $tmp = $start_date;
        $start_date = $end_date;
        $end_date = $tmp;
    }


    //结束时间月份+1，如果是13则为新年的一月份
    $start_arr = explode($explode,$start_date);
    $start_year = intval($start_arr[0]);
    $start_month = intval($start_arr[1]);


    $end_arr = explode($explode,$end_date);
    $end_year = intval($end_arr[0]);
    $end_month = intval($end_arr[1]);


    $data = array();
    $data[] = $start_date;


    $tmp_month = $start_month;
    $tmp_year = $start_year;


    //如果起止不相等，一直循环
    while (!(($tmp_month == $end_month) && ($tmp_year == $end_year))) {
        $tmp_month ++;
        //超过十二月份，到新年的一月份
        if($tmp_month > 12){
            $tmp_month = 1;
            $tmp_year++;
        }
        $data[] = $tmp_year.$explode.str_pad($tmp_month,2,'0',STR_PAD_LEFT);
    }


    if($addOne == true){
        $tmp_month ++;
        //超过十二月份，到新年的一月份
        if($tmp_month > 12){
            $tmp_month = 1;
            $tmp_year++;
        }
        $data[] = $tmp_year.$explode.str_pad($tmp_month,2,'0',STR_PAD_LEFT);
    }


    return $data;
}


/**
 * 下载远程图片
 * @param $url
 * @param bool $type true，使用原名 false重命名
 * @return string
 */
function downloadImage($url, $type = false, $ext = '')
{
    $extension = pathinfo($url)['extension'];
    $ext = $extension ? '.'.$extension : '.jpg';
    $filename = $type ? pathinfo($url, PATHINFO_BASENAME) : md5(time() . rand(1, 10000)) . $ext;
    $filePath = app()->getRootPath() . 'public/storage/images/' . $filename;
    $bt = file_get_contents($url);
    if (empty($bt)) {
        return '';
    }
    file_put_contents($filePath, $bt);
    return $filePath;
}
