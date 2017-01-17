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

init();

try {

	if( isset($_GET['c']) && trim($_GET['c']) ) {
		$control = trim($_GET['c']);
	} else {
		error_log(date('Y-m-d H:i:s').":Controller can not be empty".PHP_EOL, 3, HH_ROOT.'log/missController.log');

		//TODO: 暂时没找到对于yar有效的报错机制
		exit;
	}

	$control = $control.'Control';

	if (file_exists(HH_ROOT . "control/$control.php")) {
		$control = new $control($_REQUEST['c']);

		//yar
		$service = new Yar_Server( $control );
		$service->handle();

	} else {
		error_log(date('Y-m-d H:i:s').":{$control} NOT FOUND".PHP_EOL, 3, HH_ROOT.'log/missController.log');

		//TODO: 报错
		exit;
	}

} catch (Exception $e)  {

	//记录错误日志
	error_log(date('Y-m-d H:i:s').':'.$e->getMessage().PHP_EOL, 3, HH_ROOT.'log/fatalError.log');

	//TODO: 报错
	exit;
}