<?php
require 'vendor/autoload.php';
$config = include('config/app.config.php');
$redis = new App();
// $redis = PRedis::getInstance();
var_dump($redis);