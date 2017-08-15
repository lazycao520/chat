<?php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
/**
* app
*/
class App
{
    protected static $_instance = [];

    protected $log_file = '';
    protected $log;
    protected function init(){
        $this->log_file = __DIR__.'logs/chat_log_'.date('Y-m-d');
        $this->log = new Logger('chat');
        $this->log->pushHandler(new StreamHandler($this->log_file, Logger::WARNING));
    }
	

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
	* socket 连接
	*/	
	function start($server,$request){
        /*维护一个hash保存{
            socket句柄 
            客户ip 
        }=> hash_fd_1 = {
            fd；1；
            host_ip: 127.0.0.1
        }
        */
        $this->init();
        $redis = static::getInstance('PRedis');
        $redis -> hset ( 'hash_fd_'.$request->fd , 'fd' , $request->fd ) ;
        $redis -> hset ( 'hash_fd_'.$request->fd , 'host_ip' , $request->server['remote_addr'] ) ;
        $this->log->info('有握手链接');
        echo "server: 有握手链接{$request->fd}\n";
    }

    /**
    * 发送消息
    */
    function message($server, $frame){
        /*
        用户发送消息
        消息类型
        type = [start|info|stop]
        switch(type){
            case start{
                开始传递数据
                message = {
                    user_id: id
                    message: ''
                    
                }
                根据token获取用户id，用户老师id
                hash_fd_id = {
                    fd；1；
                    host_ip: 127.0.0.1
                    role:[student|teacher]
                    user_id: 
                }
                student_hash: hash_student_id = {
                    fd: fd
                    user_id:
                    student_name: 
                    teacher_id: 
                }
                teacher_kv: hash_student_id = {
                    fd
                }
            }
            case info{
                发送消息
                message = {
                    user_id: id,
                    type: [student|teacher]
                    message: '',

                }
            
                if 获取到
                    switch(type){
                        case student；
                            根据hash_student_id 获取teacher_id 再根据kv_teacher_id 获取 fd
                                通过fd发送消息{student_id,student_name,message}
                            未获取到断开连接
                        case teacher:
                            message = {student_id,message ,type}
                            根据hash_studnet_id 获取fd 发送数据
                        }
                    }
                else 断开连接
            }
            case stop{
                手动断开
                根据hash_fd_id 获取user_id 清除 hash_fd_id,hash_student_id或者hash_student_id
            }
        }
        */
        $this->init();

        $redis = static::getInstance('PRedis');
    /*
        data = {
            type:[start|info|stop],
            role:
            user_id:
            student_id:
            message:
        }
    */
        $data = $frame->data;
        $data = json_decode($data,true);
        var_dump($data);
        switch ($data['type']) {
            case 'start':
                switch ($data['role']) {
                    case 'student':
                        $user_id = $data['user_id'];
                        //更新hash_fd的数据
                        $redis -> hset ( 'hash_fd_'.$frame->fd,'role','student');
                        $redis -> hset ( 'hash_fd_'.$frame->fd,'user_id',$user_id);
                            //根据user_id 获取用户信息
//                        $user_info = $this->getUserInfo($user_id);
                        $user_info = UserController::getTeacher($user_id);


                            // 获取老师id，如果，根据老师id判断，kv_teacher_id 获取fd 老师是否在线 返回fd

                            // 维护student_hash
                        $student_data = array(
                            'fd'            => $frame->fd,
                            'user_id'       => $user_id,
                            'student_name'  => $user_info[0]['username'], 
                            'teacher_id'    => $user_info['teacher_id']
                        );
                        $redis -> hmset ('hash_student_'.$user_id,$student_data) ;  
                        break;
                    case 'teacher':
                        //更新hash_fd的数据
                        $user_id = $data['user_id'];
                        $redis -> hset ( 'hash_fd_'.$frame->fd,'role','teacher');
                        $redis -> hset ( 'hash_fd_'.$frame->fd,'user_id',$user_id);
                        $fd = $frame->fd;
                        $redis->set('kv_teacher_'.$user_id,$fd);
                        break;
                    default:

                    break;
                }
            break;
            case 'info':
                switch ($data['role']) {
                    case 'student':
                        $user_id = $data['user_id'];
                        $message = $data['message'];
                                //根据 hash_student_id 获取老师id，根据老师id 获取老师fd
                        $teacher_id = $redis->hget ( 'hash_student_'.$user_id,'teacher_id');
                        $fd = $redis->get('kv_teacher_'.$teacher_id);
                        


                        $server->push($fd,$message);         
                        break;  
                    case 'teacher':
                        $user_id = $data['user_id'];
                        $message = $data['message'];
                        $student_id = $data['student_id'];

                                //根据studnet_id 获取 fd
                        $fd = $redis->hget('hash_student_'.$student_id,'fd');
                        if ($fd) {
                            $server->push($fd,$message);    
                        }else{
                            return;
                        }

                        break;
                    default:

                        break;
                }
                break;
            case 'stop':
                $user_id = $data['user_id'];
                clearRedis($user_id,$data['role']);
                break;
            default:
            
            break;
        }
    }

    /**
    * 断开连接
    */
    function close($ser, $fd){
        $redis = static::getInstance('PRedis');
        $user_role = $redis->hget ( 'hash_fd_'.$fd,'role');
        $user_id = $redis->hget ( 'hash_fd_'.$fd,'user_id');
        $this->clearRedis($user_id,$user_role);
    }

    public function clearRedis($user_id,$type)
    {
        $redis = static::getInstance('PRedis');
        switch ($type) {
            case 'student':
                $fd = $redis->hget ( 'hash_student_'.$user_id,'fd');
                $redis->del('hash_student_'.$user_id);
                $redis->del('hash_fd_'.$fd); 
                break;
            case 'teacher':
                $fd = $redis->get ('kv_teacher_'.$user_id);
                $redis->del('kv_teacher_'.$user_id);
                $redis->del('hash_fd_'.$fd); 
                break;
            default:

            break;
        }
    }
}