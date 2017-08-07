<?php

$config['app'] = array();

$config['websocket'] = array(
		'address_id' => '0.0.0.0',
		'port'		 => 9000
	);
$config['redis'] = array(
		
	);

$config['database'] = array(
		'hostname' => 'localhost',
		'username' => 'root',
		'password' => 'root',
		'database' => 'game',
		'dbdriver' => 'mysqli',
		'dbprefix' => 'jb_',
		'pconnect' => FALSE,
		'cache_on' => FALSE,
		'cachedir' => '',
		'char_set' => 'utf8',
		'dbcollat' => 'utf8_general_ci',
		'swap_pre' => '',
		'encrypt' => FALSE,
		'compress' => FALSE,
		'stricton' => FALSE,
		'failover' => array(),
		'save_queries' => TRUE
	);
return $config;