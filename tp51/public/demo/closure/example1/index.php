<?php
function __autoload($class) {
    require_once "$class.php";
}
$template = new Template;

$template->render(new Article, 'tpl.php');