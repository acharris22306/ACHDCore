<?php //

ob_start();
require_once("vendor/autoload.php");
ini_set("display_errors",true);
$whoops = new \Whoops\Run();
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
$whoops->register();
header('Content-type: text/html; charset=utf-8');
ini_set('session.cookie_httponly', '1');
header('X-Frame-Options: SAMEORIGIN');
ini_set('session.cookie_secure', '1');

//error_log('test');
// echo ini_get("error_log");


//notAFunction();


require_once("iniConstants.php");
require_once("iniVariables.php");
ini_set("log_errors",true);
ini_set("error_log",$INIerrorLogFile);

ini_set("log_errors_max_len",MAX_ERROR_LOG_SIZE);
ini_set("error_reporting", E_ALL & ~E_NOTICE);

if($inDebugMode == false){
	ini_set("display_errors",false);
}