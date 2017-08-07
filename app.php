<?php
require 'vendor/autoload.php';
$config = include('config/app.config.php');


$server = new swoole_websocket_server($config['websocket']['address_id'], $config['websocket']['port']);

$server->on('open', function (swoole_websocket_server $server, $request) {
    echo "server: 有握手链接{$request->fd}\n";
});

$server->on('message', function (swoole_websocket_server $server, $frame) {
//    echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
    echo("有用户发送信息 \n");
    $data = $frame->data;
    //开始数据
    $redis = new Redis();
    $redis_client = $redis->getInstance();
    $data = json_decode($data);
    switch ($data['type']){
        case 'start':{
            if ($data['role'] == 'student'){
                $redis_client->set('ST_'.$data['token'],$frame->fd);
            }else{
                $redis_client->set('TC_'.$data['token'],$frame->fd);
            }
            break;
        }
        case 'info':{
            //获取教师fd
            $teacher = $redis_client->get('TC_'.$data['teacher']);
            if ($teacher){
                $server->push($teacher, $data['message']);
            }
            break;
        }
    }


//    $server->push($frame->fd, "this is server");
});

$server->on('close', function ($ser, $fd) {
    $redis = new Redis();
    $redis_client = $redis->getInstance();
    $redis_client->del ('ST_'.$fd);
    echo "关闭链接 {$fd} closed\n";
});

$server->start();