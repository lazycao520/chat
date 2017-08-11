<?php
/**
* app
*/
class App
{
	protected static $_instance = [];

	public static function getInstance($app='')
	{
		if ($app == '') {
			return;
		}else{
			if (!in_array($app, static::$_instance)) {
				// echo $app;
			 static::$_instance[$app] = ucfirst($app)::getInstance();
			// self::$instance[$app] = $class->getInstane();
		}
		

			return static::$_instance[$app];
		}
	}
	/**
	* socket 
	*/	
	function start($server,$request){
		var_dump($server);
		var_dump($request);
		echo "server: 有握手链接{$request->fd}\n";
	}
	function message($server, $frame){
		echo("有用户发送信息 \n");
    $data = $frame->data;
    //开始数据
    $data = array(
        'user_id'=>'',
        'type' => 'start|info',
        'role' => 'student|teacher',
        'message'=>''
    );
    //教师登录，两个redis fd=>teacher_id techer_id=>hash()
    //学员登录，两个redis fd=>user_id user_id=>hash(姓名)

    //根据user_id获取，用户信息（教师id）
    var_dump($data);
    $redis = new Redis();   
    $redis_client = $redis->getInstance();
    $data = json_decode($data,true);
    var_dump($data);
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

	}
	function close($ser, $fd){
		 $redis = new Redis();
	    $redis_client = $redis->getInstance();
	    $redis_client->del ('ST_'.$fd);
	    echo "关闭链接 {$fd} closed\n";
	}
}