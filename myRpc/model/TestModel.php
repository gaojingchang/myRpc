<?php

!defined('IN_HH') && exit('Access Denied');

/**
 * Class TestModel
 */
class TestModel extends ModelBase {

    public $db;
    
    public function test() {
        $sql = "SELECT id FROM {$this->db->tablePrefix}test LIMIT 1";
        $res = $this->db->fetchRow( $sql );

        return $res;
    }
    
}