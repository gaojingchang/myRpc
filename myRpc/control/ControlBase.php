<?php

!defined('IN_HH') && exit('Access Denied');

class ControlBase {

    public $db;
    
    public $control;
    
    public $config;

    public function __construct($class = '') {
    	global $config;

        $this->control = $class;
        $this->config = $config;
        $this->db = db();
        
    }
    
}
