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
			Loger::instance()->add_error($e);
		}
	}
	public function listen($topic){
		static $set_timeout;
		if (!$this->_redis) throw new Exception(__("redis server is disable"));
		if (!$set_timeout){
			$this->_redis->setOption(\Redis::OPT_READ_TIMEOUT, -1);
			$set_timeout=true;
		}
		if (defined('LSYS_MQ_FORK_RUN')&&LSYS_MQ_FORK_RUN){
			if(!defined('LSYS_MQ_LIMIT')) $max=1;
			else{
				if (LSYS_MQ_LIMIT===true) $max=true;
				else $max=LSYS_MQ_LIMIT>0?LSYS_MQ_LIMIT:1;
			}
		}else $max=true;
		while ($max===true||$max-->0){ $this->_run($topic); }
		return true;
	}
		
	protected function _run($topic){
		$data=$this->_redis->brPop($topic,0);
		if (!isset($data[1])) return false;
		$_msg=@unserialize($data[1]);
		if ($_msg instanceof Message){
			try{
				$_msg->exec();
			}catch (\Exception $e){
				loger::instance()->add_Error($e);
			}
		}else{
			Loger::instance()->add_debug("mqs bad:".$data[1]);
		}
		unset($_msg);
		return true;
	}
	public function push($message,$topic){
		if($this->_redis&&$this->_redis->isConnected()&&$this->_redis->lPush($topic,$message)) return true;
		return false;
	}
}