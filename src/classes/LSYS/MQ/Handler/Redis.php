<?php
namespace LSYS\MQ\Handler;
use LSYS\MQ\Handler;
use LSYS\MQ\Message;
use LSYS\SService\SRedis\RedisShare;
use LSYS\SService;
use LSYS\Config;
use LSYS\Loger;
use LSYS\Exception;
use function LSYS\MQ\__;
class Redis implements Handler,SService {
	use RedisShare;
	/**
	 * @var \Redis
	 */
	protected $_redis;
	public function __construct(Config $config){
		$_config=$config->get("config",null);
		if (is_array($_config)){
			$class=get_class($config);
			$_config=new $class($config->name().".config");
		}else $_config=null;
		try{
			$this->_redis = self::get_service($_config);
		}catch (\Exception $e){
			Loger::instance()->addError($e);
		}
	}
	public function pop($topic){
		static $set_timeout;
		if (!$this->_redis) throw new Exception(__("redis server is disable"));
		if (!$set_timeout){
			$this->_redis->setOption(\Redis::OPT_READ_TIMEOUT, -1);
			$set_timeout=true;
		}
		$data=$this->_redis->brPop($topic,0);
		if (isset($data[1])) return $data[1];
		return null;
	}
	public function push($message,$topic){
		if($this->_redis&&$this->_redis->isConnected()&&$this->_redis->lPush($topic,$message)) return true;
		return false;
	}
}