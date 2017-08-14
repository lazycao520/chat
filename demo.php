<?php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
require 'vendor/autoload.php';
/*$config = include('config/app.config.php');
$redis = new App();
// $redis = PRedis::getInstance();
var_dump($redis);*/



// create a log channel
/*$log_path = './';
$log_file = 'demo.log';
$log = new Logger('chat');
$log->pushHandler(new StreamHandler($log_path.'your.log', Logger::WARNING));

// add records to the log
$log->warning('这是一个警告',array('username'=>'lazycao'));
$log->error('这是一个错误',array('username'=>'lazycao'));*/
$userinfo = UserController::getTeacher(72);
var_dump($userinfo);