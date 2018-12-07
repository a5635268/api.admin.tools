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