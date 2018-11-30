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
        if(is_string($v) || is_numeric($v)){
            $v = new \ErrorException(
                $v,
                1,
                E_WARNING
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
 * 断掉的打印
 * @param $var
 */
function dd($var){
    foreach (func_get_args() as $v) {
        d($v);
    }
    die;
}