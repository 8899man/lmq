<?php
namespace LMQ\Handler;
use LMQ\Handler;
use LMQ\Exception;
use LMQ\Message;
class Redis implements Handler,RedisShare {
	public static function get_redis(array $config){
		$redis = new \Redis();
		$_config=$config+array(
			'host'=>'127.0.0.1',
			'port'=>'6379',
			'timeout'=>'60',
			'db'=>NULL,
		);
		try{
			$redis->connect($_config['host'],$_config['port'],$_config['timeout']);
			$redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_NONE);
			if (isset($_config['auth']))$redis->auth($_config['auth']);
			if (isset($_config['db']))$redis->select($_config['db']);
		}catch (\Exception $e){
			throw new Exception($e->getMessage().strtr(" [Host:host Port:port]",array("host"=>$_config['host'],"port"=>$_config['port'])),$e->getCode());
		}
		return $redis;
	}
	/**
	 * @var RedisShare
	 */
	public static $redis_share;
	protected static function _redis(array $config){
		$redis=self::$redis_share==null?Redis::class:self::$redis_share;
		return $redis::get_redis($config);
	}
	/**
	 * @var \Redis
	 */
	protected $_redis;
	public function __construct(array $config){
		$this->_redis=self::_redis($config);
	}
	public function pop($topic){
		static $set_timeout;
		if (!$set_timeout){
			$this->_redis->setOption(\Redis::OPT_READ_TIMEOUT, -1);
			$set_timeout=true;
		}
		$data=$this->_redis->brPop($topic,0);
		if (isset($data[1])) return $data[1];
		return null;
	}
	public function push($message,$topic){
		if($this->_redis->isConnected()&&$this->_redis->lPush($topic,$message)) return true;
		return false;
	}
}