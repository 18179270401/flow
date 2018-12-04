<?php
header("Content-Type: text/html;charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
define('ROOT_PATH', dirname(__FILE__));			//项目根目录
define('NOW_TIME', $_SERVER['REQUEST_TIME']);	//当前访问时间戳
define('DB_PREFIX', 't_flow_');

require_once ROOT_PATH.'/functions.php'; //功能函数
require_once ROOT_PATH.'/libs/Op.php'; //数据库操作


