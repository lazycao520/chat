<?php

$config['app'] = array();

$config['websocket'] = array(
		'address_id' => '0.0.0.0',
		'port'		 => 9000
	);
$config['redis'] = array(
		
	);

$config['database'] = array(
    'server' => 'localhost',
    'username' => 'root',
    'password' => 'root',
    'database_name' => 'game',
    'database_type' => 'mysql',
    'charset' => 'utf8',
    'prefix' =>'jb_'
	);
return $config;