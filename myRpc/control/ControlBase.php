<?php

!defined('IN_HH') && exit('Access Denied');

class ControlBase {

    public $db;
    
    public $model;
    
    public $control;
    
    public $config;

    public function __construct($class = '') {
    	global $config;

        $this->control = $class;
        $this->config = $config;
        $this->db = db();

        $model = $class.'Model';
        if (file_exists(HH_ROOT . "/model/$model.php")) {
        	$this->model = new $model($this->db);
        }
    }
    
}
