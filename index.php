<?php
// phpinfo();
// exit;

require('./vendor/autoload.php'); //for composer
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
// exit("reached");
ini_set("display_erors",1);
error_reporting(E_ALL);
// change the following paths if necessary
$yii=dirname(__FILE__).'/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

// for cros working because if not angular won't work
header("Access-Control-Allow-Methods: GET , POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once($yii);
Yii::createWebApplication($config)->run();
