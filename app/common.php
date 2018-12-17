<?PHP
/**
 * 公共函数库
 */
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

/**
 * 实例化服务层
 * @param $name
 * @return \think\Controller
 */
function service($name)
{
    $nameArr = explode('/',$name);
    if(1 == count($nameArr)){
        $name = 'common/'. $name;
    }
    return controller($name, 'service');
}

function download_image($url){
    $pathInfo = pathinfo($url);
    $filename = md5($url) . '.' . $pathInfo['extension'];
    $filePath = PUBLIC_PATH. '/uploads/image/' . $filename;
    if (file_exists($filePath)) {
        return $filePath;
    }
    $bt = file_get_contents($url);
    if (empty($bt)) {
        return '';
    }
    file_put_contents($filePath, $bt);
    return $filePath;
}

/**
 * 二维数组根据某个字段排序
 * @param $data
 * @param string $sort
 * @return array
 */
function multisort($data , $sort = '' , $type = 0)
{
    if (empty($data)) {
        return [];
    }
    //排序
    $startTime = [];
    foreach ($data as $key => $value) {
        $startTime[$key] = $value[$sort];
    }
    array_multisort($startTime , $type ? SORT_DESC : SORT_ASC , $data);
    return $data;
}

/**
 * 根据PHP各种类型变量生成唯一标识号
 * @param mixed $mix 变量
 * @return string
 */
function to_guid_string($mix)
{
    if (is_object($mix) && function_exists('spl_object_hash')) {
        return spl_object_hash($mix);
    } elseif (is_resource($mix)) {
        $mix = get_resource_type($mix) . strval($mix); //get_resource_type 此函数返回一个字符串，用于表示传递给它的 resource 的类型。string strval ( mixed $var )返回 var 的 string 值,var 可以是任何标量类型。不能将 strval() 用于数组或对象。
    } else {
        $mix = serialize($mix);
    }
    return md5($mix);
}

/**
 * 获得几天前，几小时前，几月前
 * @param int $time 时间戳
 * @param array $unit 时间单位
 * @return bool|string
 */
function date_before($time , $unit = null)
{
    if (!is_int($time))
        return false;
    $now = time();
    $unit = is_null($unit) ? ["年" , "月" , "星期" , "日" , "小时" , "分钟" , "刚刚"] : $unit;
    switch (true) {
        case $time < ($now - 31536000):
            return floor(($now - $time) / 31536000) . $unit[0];
        case $time < ($now - 2592000):
            return floor(($now - $time) / 2592000) . $unit[1];
        case $time < ($now - 604800):
            return floor(($now - $time) / 604800) . $unit[2];
        case $time < ($now - 86400):
            return floor(($now - $time) / 86400) . $unit[3];
        case $time < ($now - 3600):
            return floor(($now - $time) / 3600) . $unit[4];
        case $time < ($now - 60):
            return floor(($now - $time) / 60) . $unit[5];
        default:
            return $unit[6];
    }
}

/**
 * 根据生日计算年龄
 * @param $dob
 * @return bool|string
 */
function age_from_dob($dob)
{
    $dob = strtotime($dob);
    $y = date('Y' , $dob);
    if (($m = (date('m') - date('m' , $dob))) < 0) {
        $y ++;
    } elseif ($m == 0 && date('d') - date('d' , $dob) < 0) {
        $y ++;
    }
    return date('Y') - $y;
}

/**
 * 转换秒到日期、时或者分
 * @param $mysec
 * @return string
 */
function seconds2days($mysec)
{
    $mysec = (int)$mysec;
    $mysec = abs($mysec);
    if ($mysec === 0) {
        return '0 second';
    }
    $mins = 0;
    $hours = 0;
    $days = 0;
    if ($mysec >= 60) {
        $mins = (int)($mysec / 60);
        $mysec = $mysec % 60;
    }
    if ($mins >= 60) {
        $hours = (int)($mins / 60);
        $mins = $mins % 60;
    }
    if ($hours >= 24) {
        $days = (int)($hours / 24);
        $hours = $hours % 60;
    }
    $output = '';
    if ($days) {
        $output .= $days . " days ";
    }
    if ($hours) {
        $output .= $hours . " hours ";
    }
    if ($mins) {
        $output .= $mins . " minutes ";
    }
    if ($mysec) {
        $output .= $mysec . " seconds ";
    }
    $output = rtrim($output);
    return $output;
}

/**
 * 格式化字节大小
 * @param  number $size 字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size , $delimiter = '')
{
    $units = ['B' , 'KB' , 'MB' , 'GB' , 'TB' , 'PB'];
    for ($i = 0;$size >= 1024 && $i < 5;$i ++)
        $size /= 1024;
    return round($size , 2) . $delimiter . $units[$i];
}

/**
 * 获得常量
 * @param   string $name 常量名称，默认为获得所有常量
 * @param   void $value 常量不存在时的返回值
 * @param   string $type 常量类型，默认为用户自定义常量,参数为true获得所有常量
 * @return  array   常量数组
 */
function get_defines($name = "" , $value = null , $type = 'user')
{
    if ($name) {
        $const = get_defined_constants();
        return defined($name) ? $const[$name] : $value;
    }
    $const = get_defined_constants(true);
    return $type === true ? $const : $const[$type];
}

/**
 * 数字转人名币
 * @param [type] $num [description]
 * @return [type]  [description]
 */
function num2rmb($num)
{
    $c1 = "零壹贰叁肆伍陆柒捌玖";
    $c2 = "分角元拾佰仟万拾佰仟亿";
    $num = round($num , 2);
    $num = $num * 100;
    if (strlen($num) > 10) {
        return "oh,sorry,the number is too long!";
    }
    $i = 0;
    $c = "";
    while (1) {
        if ($i == 0) {
            $n = substr($num , strlen($num) - 1 , 1);
        } else {
            $n = $num % 10;
        }
        $p1 = substr($c1 , 3 * $n , 3);
        $p2 = substr($c2 , 3 * $i , 3);
        if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
            $c = $p1 . $p2 . $c;
        } else {
            $c = $p1 . $c;
        }
        $i = $i + 1;
        $num = $num / 10;
        $num = (int)$num;
        if ($num == 0) {
            break;
        }
    }
    $j = 0;
    $slen = strlen($c);
    while ($j < $slen) {
        $m = substr($c , $j , 6);
        if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
            $left = substr($c , 0 , $j);
            $right = substr($c , $j + 3);
            $c = $left . $right;
            $j = $j - 3;
            $slen = $slen - 3;
        }
        $j = $j + 3;
    }
    if (substr($c , strlen($c) - 3 , 3) == '零') {
        $c = substr($c , 0 , strlen($c) - 3);
    } // if there is a '0' on the end , chop it out
    return $c . "整";
}


/**
 * 随机密码生成
 * @param $length
 * @return string
 */
function rand_password($length)
{
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $chars .= '0123456789';
    $chars .= '!@#%^&*()_,./<>?;:[]{}\|=+';
    $str = '';
    $max = strlen($chars) - 1;
    for ($i = 0;$i < $length;$i ++)
        $str .= $chars[rand(0 , $max)];
    return $str;
}

/**
 * 随机颜色生成器
 * @return string
 */
function randomColor()
{
    $str = '#';
    for ($i = 0;$i < 6;$i ++) {
        $randNum = rand(0 , 15);
        switch ($randNum) {
            case 10:
                $randNum = 'A';
                break;
            case 11:
                $randNum = 'B';
                break;
            case 12:
                $randNum = 'C';
                break;
            case 13:
                $randNum = 'D';
                break;
            case 14:
                $randNum = 'E';
                break;
            case 15:
                $randNum = 'F';
                break;
        }
        $str .= $randNum;
    }
    return $str;
}

/**
 * 计算字符串中文长度
 * @param  [string] $string
 */
function utf8_strlen($string = null)
{
    // 将字符串分解为单元
    preg_match_all("/./us" , $string , $match);
    // 返回单元个数
    return count($match[0]);
}

/**
 * Utf-8、gb2312都支持的汉字截取函数
 * @param  [string]   $string    要截取的字符串
 * @param  [integer]  $sublen    截取长度
 * @param  [integer]  $start     开始长度
 * @param  [string]   $code      编码,默认是UTF8
 * @return [string]   截取后的字符串
 */
function cut_str($string , $sublen , $start = 0 , $code = 'UTF-8')
{
    if ($code == 'UTF-8') {
        $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
        preg_match_all($pa , $string , $t_string);
        if (count($t_string[0]) - $start > $sublen)
            return join('' , array_slice($t_string[0] , $start , $sublen)) . "..";
        return join('' , array_slice($t_string[0] , $start , $sublen));
    } else {
        $start = $start * 2;
        $sublen = $sublen * 2;
        $strlen = strlen($string);
        $tmpstr = '';
        for ($i = 0;$i < $strlen;$i ++) {
            if ($i >= $start && $i < ($start + $sublen)) {
                if (ord(substr($string , $i , 1)) > 129) {
                    $tmpstr .= substr($string , $i , 2);
                } else {
                    $tmpstr .= substr($string , $i , 1);
                }
            }
            if (ord(substr($string , $i , 1)) > 129)
                $i ++;
        }
        if (strlen($tmpstr) < $strlen)
            $tmpstr .= "..";
        return $tmpstr;
    }
}

/**
 * 字符截取
 * @param $string 需要截取的字符串
 * @param $length 长度
 * @param $dot
 */
function str_cut($sourcestr , $length , $dot = '...')
{
    $returnstr = '';
    $i = 0;
    $n = 0;
    $str_length = strlen($sourcestr); //字符串的字节数
    while (($n < $length) && ($i <= $str_length)) {
        $temp_str = substr($sourcestr , $i , 1);
        $ascnum = Ord($temp_str); //得到字符串中第$i位字符的ascii码
        if ($ascnum >= 224) {//如果ASCII位高与224，
            $returnstr = $returnstr . substr($sourcestr , $i , 3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
            $i = $i + 3; //实际Byte计为3
            $n ++; //字串长度计1
        } elseif ($ascnum >= 192) { //如果ASCII位高与192，
            $returnstr = $returnstr . substr($sourcestr , $i , 2); //根据UTF-8编码规范，将2个连续的字符计为单个字符
            $i = $i + 2; //实际Byte计为2
            $n ++; //字串长度计1
        } elseif ($ascnum >= 65 && $ascnum <= 90) { //如果是大写字母，
            $returnstr = $returnstr . substr($sourcestr , $i , 1);
            $i = $i + 1; //实际的Byte数仍计1个
            $n ++; //但考虑整体美观，大写字母计成一个高位字符
        } else {//其他情况下，包括小写字母和半角标点符号，
            $returnstr = $returnstr . substr($sourcestr , $i , 1);
            $i = $i + 1;            //实际的Byte数计1个
            $n = $n + 0.5;        //小写字母和半角标点等与半个高位字符宽...
        }
    }
    if ($str_length > strlen($returnstr)) {
        $returnstr = $returnstr . $dot; //超过长度时在尾处加上省略号
    }
    return $returnstr;
}

/**
 * 表情替换函数,供参考
 * @param  string $contents 传入需要更换表情的字符串;
 * @return string  返回更改后的字符串
 */
function replace_phiz($contents)
{
    preg_match_all('/\[.*?\]/is' , $contents , $arr);
    if ($arr[0]) {
        $phiz = F("phiz" , "" , "./data/"); //thinkphp中的F快速缓存函数,类似file_get_contents;
        foreach ($arr[0] as $v) {
            foreach ($phiz as $k => $val) {
                if ($v == "[{$val}]") {
                    $contents = str_replace($v , "<img src='" . __ROOT__ . "/Public/Wish/TP/Images/phiz/{$k}.gif' alt='' />" , $contents);
                }
            }
            continue;
        }
    }
    return $contents;
}

/**
 * 检测一个数据长度是否超过最小值
 * @param type $value 数据
 * @param type $length 最小长度
 * @return type
 */
function isMin($value , $length)
{
    return mb_strlen($value , 'utf-8') >= (int)$length ? true : false;
}

/**
 * 检测一个数据长度是否超过最大值
 * @param type $value 数据
 * @param type $length 最大长度
 * @return type
 */
function isMax($value , $length)
{
    return mb_strlen($value , 'utf-8') <= (int)$length ? true : false;
}

/**
 * 取得文件扩展
 * @param type $filename 文件名
 * @return type 后缀
 */
function fileext($filename)
{
    $pathinfo = pathinfo($filename);
    return $pathinfo['extension'];
}

/**
 * 根据文件扩展名来判断是否为图片类型
 * @param type $file 文件名
 * @return type 是图片类型返回 true，否则返回 false
 */
function is_image($file)
{
    $ext_arr = ['jpg' , 'gif' , 'png' , 'bmp' , 'jpeg' , 'tiff'];
    //取得扩展名
    $ext = fileext($file);
    return in_array($ext , $ext_arr) ? true : false;
}

/**
 * 对URL中有中文的部分进行编码处理
 * @param type $url 地址 http://www.abc3210.com/s?wd=博客
 * @return type ur;编码后的地址 http://www.abc3210.com/s?wd=%E5%8D%9A%20%E5%AE%A2
 */
function cn_urlencode($url)
{
    $pregstr = "/[\x{4e00}-\x{9fa5}]+/u"; //UTF-8中文正则
    if (preg_match_all($pregstr , $url , $matchArray)) {//匹配中文，返回数组
        foreach ($matchArray[0] as $key => $val) {
            $url = str_replace($val , urlencode($val) , $url); //将转译替换中文
        }
        if (strpos($url , ' ')) {//若存在空格
            $url = str_replace(' ' , '%20' , $url);
        }
    }
    return $url;
}

/**
 * 获取当前页面完整URL地址
 * @return type 地址
 */
function get_url()
{
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? safe_replace($_SERVER['PHP_SELF']) : safe_replace($_SERVER['SCRIPT_NAME']);
    $path_info = isset($_SERVER['PATH_INFO']) ? safe_replace($_SERVER['PATH_INFO']) : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? safe_replace($_SERVER['REQUEST_URI']) : $php_self . (isset($_SERVER['QUERY_STRING']) ? '?' . safe_replace($_SERVER['QUERY_STRING']) : $path_info);
    return $sys_protocal . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $relate_url;
}

/**
 * 取得URL地址中域名部分
 * @param type $url
 * @return \url 返回域名
 */
function urlDomain($url)
{
    if ($url) {
        $pathinfo = parse_url($url);
        return $pathinfo['scheme'] . "://" . $pathinfo['host'] . "/";
    }
    return false;
}

/**
 * 发送HTTP状态
 * @param integer $code 状态码
 * @return void
 */
function send_http_status($code)
{
    static $_status = [// Informational 1xx
        100 => 'Continue' ,
        101 => 'Switching Protocols' , // Success 2xx
        200 => 'OK' ,
        201 => 'Created' ,
        202 => 'Accepted' ,
        203 => 'Non-Authoritative Information' ,
        204 => 'No Content' ,
        205 => 'Reset Content' ,
        206 => 'Partial Content' , // Redirection 3xx
        300 => 'Multiple Choices' ,
        301 => 'Moved Permanently' ,
        302 => 'Moved Temporarily ' , // 1.1
        303 => 'See Other' ,
        304 => 'Not Modified' ,
        305 => 'Use Proxy' , // 306 is deprecated but reserved
        307 => 'Temporary Redirect' , // Client Error 4xx
        400 => 'Bad Request' ,
        401 => 'Unauthorized' ,
        402 => 'Payment Required' ,
        403 => 'Forbidden' ,
        404 => 'Not Found' ,
        405 => 'Method Not Allowed' ,
        406 => 'Not Acceptable' ,
        407 => 'Proxy Authentication Required' ,
        408 => 'Request Timeout' ,
        409 => 'Conflict' ,
        410 => 'Gone' ,
        411 => 'Length Required' ,
        412 => 'Precondition Failed' ,
        413 => 'Request Entity Too Large' ,
        414 => 'Request-URI Too Long' ,
        415 => 'Unsupported Media Type' ,
        416 => 'Requested Range Not Satisfiable' ,
        417 => 'Expectation Failed' , // Server Error 5xx
        500 => 'Internal Server Error' ,
        501 => 'Not Implemented' ,
        502 => 'Bad Gateway' ,
        503 => 'Service Unavailable' ,
        504 => 'Gateway Timeout' ,
        505 => 'HTTP Version Not Supported' ,
        509 => 'Bandwidth Limit Exceeded'
    ];
    if (isset($_status[$code])) {
        header('HTTP/1.1 ' . $code . ' ' . $_status[$code]);
        // 确保FastCGI模式下正常
        header('Status:' . $code . ' ' . $_status[$code]);
    }
}

/**
 * 获取HTTP请求原文
 * @return string
 */
function get_http_raw()
{
    $raw = '';
    // (1) 请求行
    $raw .= $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'] . ' ' . $_SERVER['SERVER_PROTOCOL'] . "\r\n";
    // (2) 请求Headers
    foreach ($_SERVER as $key => $value) {
        if (substr($key , 0 , 5) === 'HTTP_') {
            $key = substr($key , 5);
            $key = str_replace('_' , '-' , $key);
            $raw .= $key . ': ' . $value . "\r\n";
        }
    }
    // (3) 空行
    $raw .= "\r\n";
    // (4) 请求Body
    $raw .= file_get_contents('php://input');
    return $raw;
}

/**
 * 列出目录下所有文件
 * @param $DirPath
 */
function listDirFiles($DirPath)
{
    if ($dir = opendir($DirPath)) {
        while (($file = readdir($dir)) !== false) {
            if (!is_dir($DirPath . $file)) {
                echo "filename: $file<br />";
            }
        }
    }
}

/**
 * 我们在处理时间时，需要计算当前时间距离某个时间点的时长，如计算客户端运行时长，通常用hh:mm:ss表示。
 * @param $seconds 传入秒数
 * @return string
 */
function changeTimeType($seconds)
{
    if ($seconds > 3600) {
        $hours = intval($seconds / 3600);
        $minutes = $seconds % 3600;
        $time = $hours . ":" . gmstrftime('%M:%S' , $minutes);
    } else {
        $time = gmstrftime('%H:%M:%S' , $seconds);
    }
    return $time;
}

/**
 * 特殊的字符替换
 * @param [type] $str [description]
 * @return [type]  [description]
 */
function makeSemiangle($str)
{
    $arr = [
        '０' => '0' ,
        '１' => '1' ,
        '２' => '2' ,
        '３' => '3' ,
        '４' => '4' ,
        '５' => '5' ,
        '６' => '6' ,
        '７' => '7' ,
        '８' => '8' ,
        '９' => '9' ,
        'Ａ' => 'A' ,
        'Ｂ' => 'B' ,
        'Ｃ' => 'C' ,
        'Ｄ' => 'D' ,
        'Ｅ' => 'E' ,
        'Ｆ' => 'F' ,
        'Ｇ' => 'G' ,
        'Ｈ' => 'H' ,
        'Ｉ' => 'I' ,
        'Ｊ' => 'J' ,
        'Ｋ' => 'K' ,
        'Ｌ' => 'L' ,
        'Ｍ' => 'M' ,
        'Ｎ' => 'N' ,
        'Ｏ' => 'O' ,
        'Ｐ' => 'P' ,
        'Ｑ' => 'Q' ,
        'Ｒ' => 'R' ,
        'Ｓ' => 'S' ,
        'Ｔ' => 'T' ,
        'Ｕ' => 'U' ,
        'Ｖ' => 'V' ,
        'Ｗ' => 'W' ,
        'Ｘ' => 'X' ,
        'Ｙ' => 'Y' ,
        'Ｚ' => 'Z' ,
        'ａ' => 'a' ,
        'ｂ' => 'b' ,
        'ｃ' => 'c' ,
        'ｄ' => 'd' ,
        'ｅ' => 'e' ,
        'ｆ' => 'f' ,
        'ｇ' => 'g' ,
        'ｈ' => 'h' ,
        'ｉ' => 'i' ,
        'ｊ' => 'j' ,
        'ｋ' => 'k' ,
        'ｌ' => 'l' ,
        'ｍ' => 'm' ,
        'ｎ' => 'n' ,
        'ｏ' => 'o' ,
        'ｐ' => 'p' ,
        'ｑ' => 'q' ,
        'ｒ' => 'r' ,
        'ｓ' => 's' ,
        'ｔ' => 't' ,
        'ｕ' => 'u' ,
        'ｖ' => 'v' ,
        'ｗ' => 'w' ,
        'ｘ' => 'x' ,
        'ｙ' => 'y' ,
        'ｚ' => 'z' ,
        '（' => '(' ,
        '）' => ')' ,
        '〔' => '[' ,
        '〕' => ']' ,
        '【' => '[' ,
        '】' => ']' ,
        '〖' => '[' ,
        '〗' => ']' ,
        '｛' => '{' ,
        '｝' => '}' ,
        '《' => '<' ,
        '》' => '>' ,
        '％' => '%' ,
        '＋' => '+' ,
        '—' => '-' ,
        '－' => '-' ,
        '～' => '-' ,
        '：' => ':' ,
        '。' => '.' ,
        '、' => ',' ,
        '，' => '.' ,
        '、' => '.' ,
        '；' => ';' ,
        '？' => '?' ,
        '！' => '!' ,
        '…' => '-' ,
        '‖' => '|' ,
        '”' => '"' ,
        '“' => '"' ,
        '\'' => '`' ,
        '‘' => '`' ,
        '｜' => '|' ,
        '〃' => '"' ,
        '　' => ' ' ,
        '．' => '.'
    ];
    return strtr($str , $arr);
}

/**
 * 验证手机号码格式是否正确
 * $mobile 手机号码
 * @return boolean
 */
function check_mobile_format($mobile)
{
    return preg_match('/^0?1((3[0-9]{1})|(47)|(5[0-9]{1})|(8[0-9]{1})){1}[0-9]{8}$/' , $mobile) === 1;
}

/**
 * 验证邮箱格式是否正确
 * @email 邮箱地址
 * @return boolean
 */
function check_email_format($email)
{
    return preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/' , $email) === 1;
}

function copy_dir($src , $dst)
{
    $dir = opendir($src);
    @mkdir($dst , 0777 , true);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                copy_dir($src . '/' . $file , $dst . '/' . $file);
                continue;
            } else {
                copy($src . '/' . $file , $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

/*
 * 手机号码格式验证
 * */
function is_mobile($mobile)
{
    return preg_match('/^1[34578]\d{9}$/' , $mobile);
}

/**
 * 身份证验证ID
 * @param $cardId
 * @return int
 */
function is_cardId($cardId)
{
    return preg_match('/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$|^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/' , $cardId);
}

/*
 * 固话号码格式验证
 * */
function is_tel($tel)
{
    return preg_match('/^0\d{2,3}-\d{5,9}$/' , $tel);
}

/**
 * 获取微秒时间 13位
 * @return [type] [description]
 */
function msectime()
{
    return substr(round(microtime(true) * 1000) , 0 , 13);
}

/**
 * 微秒转秒
 * @param  [type] $msectime [description]
 * @return [type]           [description]
 */
function msectimeTotime($msectime)
{
    if (strlen($msectime) != 13) {
        return 0;
    }
    return substr($msectime , 0 , 10);
}


/**
 * 随机字符串
 * @param int $str_length
 * @return string
 */
function create_pass_num_str($str_length = 10)
{
    $arr = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    shuffle($arr);
    return implode('', array_slice($arr, 0, $str_length));
}

/**
 * 微秒转秒
 * @param $msectime
 * @return bool|int|string
 */
function msectime2time($msectime)
{
    if (strlen($msectime) != 13) {
        return 0;
    }
    return substr($msectime, 0, 10);
}

/**
 * 下划线转驼峰
 * @param $str
 * @return mixed
 */
function convert_underline($str)
{
    $str = preg_replace_callback('/([-_]+([a-z]{1}))/i', function ($matches) {
        return strtoupper($matches[2]);
    }, $str);
    return $str;
}


/**
 * 驼峰转下划线
 * @param $str
 * @return mixed
 */
function hump_to_line($str)
{
    $str = preg_replace_callback('/([A-Z]{1})/', function ($matches) {
        return '_' . strtolower($matches[0]);
    }, $str);
    return ltrim($str, '_');
}


/**
 *随机取字符串中几个字符
 * @param $len 取值长度
 */
function getRandomN($len)
{
    $chars = 'abcdefghijklmnopqrstuvwxyz123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    for ($i = 0, $str = '', $lc = strlen($chars) - 1; $i < $len; $i++) {
        $str .= $chars[mt_rand(0, $lc)];
    }
    return $str;
}


function getRand($len)
{
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
    $string = msectime();
    for (; $len >= 1; $len--) {
        $position = rand() % strlen($chars);
        $position2 = rand() % strlen($string);
        $string .= substr_replace($string, substr($chars, $position, 1), $position2, 0);
    }
    return $string;
}

/**
 *根据生日计算年龄
 */
function getAge($birthday)
{
    $age = strtotime($birthday);
    if ($age === false) {
        return false;
    }
    list($y1, $m1, $d1) = explode("-", date("Y-m-d", $age));
    $now = strtotime("now");
    list($y2, $m2, $d2) = explode("-", date("Y-m-d", $now));
    $age = $y2 - $y1;
    if ((int)($m2 . $d2) < (int)($m1 . $d1))
        $age -= 1;
    return $age;
}

/**
 *根据生日计算星座
 */
function getZodiac($birthday)
{
    $age = strtotime($birthday);
    $month = date("m", $age);
    $day = date("d", $age);
    $signs = array(
        array("20" => "宝瓶座"),
        array("19" => "双鱼座"),
        array("21" => "白羊座"),
        array("20" => "金牛座"),
        array("21" => "双子座"),
        array("22" => "巨蟹座"),
        array("23" => "狮子座"),
        array("23" => "处女座"),
        array("23" => "天秤座"),
        array("24" => "天蝎座"),
        array("22" => "射手座"),
        array("22" => "摩羯座")
    );
    list($sign_start, $sign_name) = each($signs[(int)$month - 1]);
    if ($day < $sign_start) {
        list($sign_start, $sign_name) = each($signs[($month - 2 < 0) ? $month = 11 : $month -= 2]);
    }
    return $sign_name;
}

function hideStr($string, $bengin = 0, $len = 4, $type = 0, $glue = "@")
{
    if (empty($string))
        return false;
    $array = array();
    if ($type == 0 || $type == 1 || $type == 4) {
        $strlen = $length = mb_strlen($string);
        while ($strlen) {
            $array[] = mb_substr($string, 0, 1, "utf8");
            $string = mb_substr($string, 1, $strlen, "utf8");
            $strlen = mb_strlen($string);
        }
    }
    switch ($type) {
        case 1:
            $array = array_reverse($array);
            for ($i = $bengin; $i < ($bengin + $len); $i++) {
                if (isset($array[$i]))
                    $array[$i] = "*";
            }
            $string = implode("", array_reverse($array));
            break;
        case 2:
            $array = explode($glue, $string);
            $array[0] = hideStr($array[0], $bengin, $len, 1);
            $string = implode($glue, $array);
            break;
        case 3:
            $array = explode($glue, $string);
            $array[1] = hideStr($array[1], $bengin, $len, 0);
            $string = implode($glue, $array);
            break;
        case 4:
            $left = $bengin;
            $right = $len;
            $tem = array();
            for ($i = 0; $i < ($length - $right); $i++) {
                if (isset($array[$i]))
                    $tem[] = $i >= $left ? "*" : $array[$i];
            }
            $array = array_chunk(array_reverse($array), $right);
            $array = array_reverse($array[0]);
            for ($i = 0; $i < $right; $i++) {
                $tem[] = $array[$i];
            }
            $string = implode("", $tem);
            break;
        default:
            for ($i = $bengin; $i < ($bengin + $len); $i++) {
                if (isset($array[$i]))
                    $array[$i] = "*";
            }
            $string = implode("", $array);
            break;
    }
    return $string;
}


function filterEmoji($text, $replaceTo = '?')
{
    $clean_text = "";
    // Match Emoticons
    $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
    $clean_text = preg_replace($regexEmoticons, $replaceTo, $text);
    // Match Miscellaneous Symbols and Pictographs
    $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
    $clean_text = preg_replace($regexSymbols, $replaceTo, $clean_text);
    // Match Transport And Map Symbols
    $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
    $clean_text = preg_replace($regexTransport, $replaceTo, $clean_text);
    // Match Miscellaneous Symbols
    $regexMisc = '/[\x{2600}-\x{26FF}]/u';
    $clean_text = preg_replace($regexMisc, $replaceTo, $clean_text);
    // Match Dingbats
    $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
    $clean_text = preg_replace($regexDingbats, $replaceTo, $clean_text);
    return $clean_text;
}

function getTree($arr, $pid = 0, $level = 0,$pk='comments_id')
{
    //声明静态数组,避免递归调用时,多次声明导致数组覆盖
    static $list = [];
    foreach ($arr as $key => $value) {
        //第一次遍历,找到父节点为根节点的节点 也就是pid=0的节点
        if ($value['pid'] == $pid) {
            //父节点为根节点的节点,级别为0，也就是第一级
            $value['level'] = $level;
            //把数组放到list中
            $list[] = $value;
            //把这个节点从数组中移除,减少后续递归消耗
            unset($arr[$key]);
            //开始递归,查找父ID为该节点ID的节点,级别则为原级别+1
            getTree($arr, $value[$pk], $level + 1);
        }
    }
    return $list;
}