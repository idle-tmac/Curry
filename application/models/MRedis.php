<?php
class MRedis extends CI_Model
{
    private static $instance = null;

    private static $host = '127.0.0.1';
    private static $port = '6379';

    public function __construct()
    {
	parent::__construct();
	//self::$instance = new Redis();
  	//self::$instance->connect(self::$ip, self::$port);	
    }
    

    public static function _getInstance($host_ = null, $port_ = null)
    {
        if (is_null(self::$instance)) {
		$redis = new Redis();
		$redis->connect('127.0.0.1', '6379');
		self::$instance = new Redis();
   		self::$instance->connect(self::$host, self::$port);
        }
        return self::$instance;
    }
}
