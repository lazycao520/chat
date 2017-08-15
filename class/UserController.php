<?php
/**
 * Created by PhpStorm.
 * User: lazycao
 * Date: 2017/8/14 0014
 * Time: 14:33
 */
class UserController
{
    public static function getTeacher($student_id){
        $config = include(__DIR__.'/../config/app.config.php');
        $database = new medoo($config['database']);
        $start_time =strtotime(date('H:i'));
        $query = sprintf("SELECT teacher_id,c.id,c.start_time,c.end_time FROM jb_courses as c LEFT JOIN jb_course_student_relationship as cu on c.id=cu.course_id where cu.student_id=%d AND is_delete=0",$student_id);
        $data = $database->query($query)->fetchAll(PDO::FETCH_ASSOC);

        if (count($data)>1){
            $time = 100000000000;
            $key1 = 0;
            foreach ($data as $key=>$val){
                $time1 = $start_time - strtotime($val['start_time'])?$start_time - strtotime($val['start_time']):strtotime($val['start_time'])-$start_time;
                if ($time1 < $time){
                    $time = $time1;
                    $key1 = $key;
                }
            }
            $data1 = $data[$key1];
        }
        $userinfo = $database->select('users','*',array('id'=>$student_id));
        $userinfo['teacher_id'] =$data1['teacher_id'];
        var_dump($userinfo);
        return ($userinfo);
    }
}