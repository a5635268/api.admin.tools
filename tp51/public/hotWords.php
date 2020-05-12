<?php
$s = <<<EOF
王者荣耀
阴阳师
EOF;
header("Content-type: text/html; charset=utf-8");
header('Last-Modified: '.gmdate('D, d M Y H:i:s', time()).' GMT', true, 200);
header('ETag: "5816f349-19"');
echo $s;