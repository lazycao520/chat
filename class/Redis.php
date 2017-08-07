<?php

/**
 * Created by PhpStorm.
 * User: lazycao
 * Date: 2017/8/7 0007
 * Time: 15:33
 */
class Redis
{
    private $client;
    public function __construct($config='')
    {
        $single_server = array(
            'host' => '127.0.0.1',
//            'host' => '123.56.0.88',
            'port' => 6379,
        );
        $this->client = new Predis\Client($single_server + array('read_write_timeout' => 0));
    }
    function getInstance(){
        return $this->client;
    }

}