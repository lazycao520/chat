<?php

/**
 * Created by PhpStorm.
 * User: lazycao
 * Date: 2017/8/7 0007
 * Time: 15:33
 */
class PRedis
{
    protected static $_instance = null;
    public function __construct($config='')
    {
        $single_server = array(
            'host' => '127.0.0.1',
//            'host' => '123.56.0.88',
            'port' => 6379,
        );
        static::$_instance = new Predis\Client($single_server + array('read_write_timeout' => 0));
    }
     final public static function getInstance(){
        if(null !== static::$_instance){
            return static::$_instance;
        }
        static::$_instance = new PRedis();
        return static::$_instance;
    }
    public function get(){
        var_dump(self::$_instance);
    }

}