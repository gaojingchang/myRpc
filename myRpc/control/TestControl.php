<?php

!defined('IN_HH') && exit('Access Denied');

class TestControl extends ControlBase {
    public $model;

    public $config;

    /**
     * @desc this is an example
     * @param $param your param
     * @return array
     *      ['yourParam:'=>$_param, 'dbData'=>$dbData]
     */
    public function testSuccess($param){

        $testM = new TestModel( $this->db );
        $dbData = $testM->test();

        $result = ['yourParam:'=>$param, 'dbData'=>$dbData];

        return success($result);
    }

    /**
     * @desc this is an example
     * @return array
     */
    public function testError() {
        return error(1001);
    }
}