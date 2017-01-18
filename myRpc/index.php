<?php
date_default_timezone_set('PRC');

define('IN_HH', TRUE);
define('HH_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);

include_once(HH_ROOT.'conf/config.inc.php');
include_once(HH_ROOT . 'lib/fun.inc.php');

//开启调试
$config['debug'] ? error_reporting(E_ALL ^ E_NOTICE) : error_reporting(0);

//GET参数安全过滤
$_GET = daddslashes($_GET, 1, TRUE);

//注册自动加载类
init();

try {

	//检查 c 参数
	if( isset($_GET['c']) && trim($_GET['c']) ) {
		$control = trim($_GET['c']);
	} else {
		error_log(date('Y-m-d H:i:s').":Controller can not be empty".PHP_EOL, 3, HH_ROOT.'log/missController.log');
		error404();
	}

	//实例化控制器
	$control = $control.'Control';
	if (file_exists(HH_ROOT . "control/$control.php")) {
		$control = new $control($_GET['c']);

		//yar
		$service = new Yar_Server( $control );
		$service->handle();

	} else {
		error_log(date('Y-m-d H:i:s').":{$control} NOT FOUND".PHP_EOL, 3, HH_ROOT.'log/missController.log');
		error404();
	}

} catch (Exception $e)  {

	error_log(date('Y-m-d H:i:s').':'.$e->getMessage().PHP_EOL, 3, HH_ROOT.'log/fatalError.log');
}