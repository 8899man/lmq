<?php
namespace LSYS;
use LSYS\MQ\Config;
use LSYS\MQ\Handler;
use LSYS\MQ\Message;

class MQ{
	public static $topic='LMQ';
	protected static $_config;
	protected static $_instances=array();
	public static function config(Config $config){
		self::$_config=$config;
	}
	/**
	 * @param Config $config
	 * @return MQ
	 */
	public static function instance(Config $config=null){
		$config=$config==null?self::$_config:$config;
		$name=$config->name();
		if (!isset(self::$_instances[$name])){
			$handler=$config->handler();
			$obj=new static(new $handler($config->as_array()));
			self::$_instances[$name]=$obj;
		}
		return self::$_instances[$name];
	}
	/**
	 * @var Handler
	 */
	protected $_handle;
	public function __construct(Handler $handle){
		$this->_handle=$handle;
	}
	public function listen($topic=null,callable $error_callable=NULL){
		$topic=$topic===null?self::$topic:$topic;
		while (true){
			$msg=$this->_handle->pop($topic);
			if ($msg==null)continue;
			$_msg=@unserialize($msg);
			if ($_msg instanceof Message) $_msg->exec();
			else if ($callable)call_user_func($callable,$msg);
		}
	}
	public function push(Message $message,$topic=null){
		return $this->_handle->push(serialize($message),$topic===null?self::$topic:$topic);
	}
}