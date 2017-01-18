<?php

//串行调用
/*$client = new Yar_Client("http://localhost/github/myRpc/myRpc/index.php?c=Test");
$testRetVal = $client->testSuccess(1);
var_dump($testRetVal);
exit;*/


function callback($retval, $callinfo) {

    //error_log(time().':callinfo:'.json_encode($callinfo).PHP_EOL, 3, 't.log');

    if ($callinfo == NULL) {
        //做本地的逻辑
        return TRUE;

        //error_log(time().':'.'send req success'.PHP_EOL, 3, 't.log');
    }else{

        //code等于0表示成功，否则有错误信息返回
        if($retval['code'] === 0) {
            //成功
            var_dump($retval['data']);
            echo "<br/>";

        } else {
            //有错误
            var_dump($retval['errorMsg']);
            echo "<br/>";
        }

        //error_log(time().':'.json_encode( $retval, JSON_UNESCAPED_UNICODE ).PHP_EOL, 3, 't.log');
    }

}

function error_callback($type, $error, $callinfo) {
    var_dump($error);
    echo "<br/>";

    //error_log($error);
}

//并行调用：
//1、所有请求发送成功，Yar会调用一次callback，其中$callinfo为null
//2、每个请求执行完成，获取到了结果，也会去调用callback，其中$callinfo不为null
$res = Yar_Concurrent_Client::call("http://localhost/github/myRpc/myRpc/index.php?c=Test", "testSuccess", ['1']);
$res1 = Yar_Concurrent_Client::call("http://localhost/github/myRpc/myRpc/index.php?c=Test", "testError");
$res2 = Yar_Concurrent_Client::call("http://localhost/github/myRpc/myRpc/index.php?c=Test", "testError1");
$res3 = Yar_Concurrent_Client::call("http://localhost/github/myRpc/myRpc/index.php?c=Test1", "testError");
$res4 = Yar_Concurrent_Client::loop("callback", "error_callback");  //send