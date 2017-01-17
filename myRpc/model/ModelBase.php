<?php

!defined('IN_HH') && exit('Access Denied');

class ModelBase {
	
	public $db;
	
	public function __construct($db) {
		$this->db = $db;
	}
    
}//class