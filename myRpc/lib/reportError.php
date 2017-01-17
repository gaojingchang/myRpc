<?php
//打开错误信息
set_error_handler('displayErrorHandler');

//捕获Warring错误
function displayErrorHandler($error, $error_string, $filename, $line, $symbols)
{
    $error_no_arr = array(1=>'ERROR', 2=>'WARNING', 4=>'PARSE', 8=>'NOTICE', 16=>'CORE_ERROR', 32=>'CORE_WARNING', 64=>'COMPILE_ERROR', 128=>'COMPILE_WARNING', 256=>'USER_ERROR', 512=>'USER_WARNING', 1024=>'USER_NOTICE', 2047=>'ALL', 2048=>'STRICT');
    if(in_array($error,array(1,2,4)))
    {
        $msg="数据库连接或者设置信息有误。请联系系统管理员解决问题。";
        dieByError($msg);
    }
    
    /*echo "<b>Custom error:</b> [$error] $error_string<br>";
    echo " Error on line $line in $filename<br>";*/
}
//显示错误信息
function dieByError($msg)
{
    echo $msg;
    exit();
}