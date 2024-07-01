<?php


mb_internal_encoding("UTF-8");
mb_regex_encoding('UTF-8');
mb_http_output('UTF-8');
mb_language('uni');

ini_set('default_charset','UTF-8');

if (mb_substr(php_uname(), 0, 7) == "Windows") { setlocale(LC_ALL, "russian.UTF-8"); } else { setlocale(LC_ALL,"ru_RU.UTF-8"); }

header('Content-Type: application/json; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Expires: " . date("r"));

header('Accept: application/json');
header('Content-Type: application/json');

date_default_timezone_set('Europe/Moscow');


require_once(__DIR__ . '/library/autoload.php');


foreach (glob(__DIR__ . '/engine/{constants,class,function,service}.*.php', GLOB_BRACE) as $path) {
    require_once $path;
}

