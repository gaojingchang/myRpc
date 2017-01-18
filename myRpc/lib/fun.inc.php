<?php

!defined('IN_HH') && exit('Access Denied');

function daddslashes($string, $force = 0, $strip = FALSE) {
    if ($force || !get_magic_quotes_gpc()) {
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = daddslashes($val, $force, $strip);
            }
        } else {
            $string = addslashes($strip ? stripslashes($string) : $string);
        }
    }
    return $string;
}

//实例化db对象
function db() {
    static $db;
    if (is_object($db)) {
        return $db;
    }
    global $config;
    include HH_ROOT . '/lib/Db.php';
    $db = new Db($config['db']['dsn'], $config['db']['username'], $config['db']['password']);
    $db->tablePrefix = $config['db']['tablePrefix'];
    $db->connect();
    return $db;
}

//初始化
function init() {
	//自动加载类
    spl_autoload_register(function ($class) {
    	if(file_exists(HH_ROOT . "/control/$class.php") && !class_exists($class)) {
	    	include_once(HH_ROOT . "/control/$class.php");
    	} else if (file_exists(HH_ROOT . "/model/$class.php") && !class_exists($class)) {
    		include_once(HH_ROOT . "/model/$class.php");
    	} else if (file_exists(HH_ROOT . "/lib/$class.php") && !class_exists($class)) {
    		include_once(HH_ROOT . "/lib/$class.php");
    	}
	});
}

//成功则返回数据
function success($data) {
    $result = array();
    $result['code'] = 0;
    $result['data'] = $data;
    
    return $result;
}

//错误信息返回
function error($code, $errorMsg='') {
    include(HH_ROOT.'/conf/errorCode.php');
    $result = array();
    if (isset($errorCode[$code])) {
        $result['code'] = $code;
        $result['errorMsg'] = $errorMsg ? $errorMsg : $errorCode[$code];
    } else {
        $result['code'] = 110;
        $result['errorMsg'] = $errorCode[110];
    }

    return $result;
}

/**
 * @desc 为避免与系统的错误混淆，这里使用了自定义错误
 * @param $code 状态代码 [601|602=c参数错误,700=php执行时出现致命错误]
 */
function httpError($code, $desc) {
    header("HTTP/1.1 {$code} {$desc}");
    exit;
}
