<?php
require 'vendor/autoload.php';
$config = include('config/app.config.php');

$redis = new Redis();
$redis_client = $redis->getInstance();


$server = new swoole_websocket_server($config['websocket']['address_id'], $config['websocket']['port']);

$server->on('open', function (swoole_websocket_server $server, $request,$redis_client) {
    echo "server: 有握手链接{$request->fd}\n";
});

$server->on('message', function (swoole_websocket_server $server, $frame,$redis_client) {
//    echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
    echo("有用户发送信息 \n");
    var_dump($frame);
    $redis_client->set('ST_'.$frame->fd,'yes');
//    $server->push($frame->fd, "this is server");
});

$server->on('close', function ($ser, $fd,$redis_client) {
    $redis_client->del ('ST_'.$fd);
    echo "关闭链接 {$fd} closed\n";
});

$server->start();