<?php

!defined('IN_HH') && exit('Access Denied');

$config = array();

$db_config = include(HH_ROOT . '/conf/db.config.php');
$switch_config = include(HH_ROOT . '/conf/switch.config.php');

$config = array_merge($db_config,$switch_config);
