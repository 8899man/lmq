<?php
namespace LSYS\MQ\Handler;
use LSYS\MQ\Handler;
use LSYS\MQ\Message;
use LSYS\SService\ServiceShare;
use LSYS\SService\SRedis\RedisShare;
class Redis implements Handler,ServiceShare {
	use RedisShare;
	/**
	 * @var \Redis
	 */
	protected $_redis;
	public function __construct(array $config){
		$this->_redis=self::get_service($config);
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