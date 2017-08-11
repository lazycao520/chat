<?php
require 'vendor/autoload.php';
$config = include('config/app.config.php');

$server = new swoole_websocket_server($config['websocket']['address_id'], $config['websocket']['port']);

$app = new App();
$server->on('open', function (swoole_websocket_server $server, $request)use($app) {
    $app->start($server,$request);

});

$server->on('message', function (swoole_websocket_server $server, $frame)use($app) {
//    echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
    $app->message($server, $frame);

//    $server->push($frame->fd, "this is server");
});

$server->on('close', function ($ser, $fd) use($app) {
    $app->close($ser, $fd);
});

$server->start();