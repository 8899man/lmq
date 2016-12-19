<?php
namespace LSYS;
use LSYS\MQ\Handler;
use LSYS\MQ\Message;

class MQ{
	public static $topic='LMQ';
	/**
	 *
	 * @var string default instance name
	 */
	public static $config = 'lmq.redis';
	//inistances
	protected static $_instances=array();
	/**
	 * @param Config $config
	 * @return MQ
	 */
	public static function instance(Config $config=null){
		if ($config === NULL){
			if (is_string(self::$config)){
				self::$config = new \LSYS\Config\File(self::$config);
			}
			$config=self::$config;
		}
		$name=$config->name();
		if (!isset(self::$_instances[$name])){
			$handler=$config->get("handler",NULL);
			if ($handler==null){
				throw new Exception ( __('MQ handler not defined in [:name] configuration',array("name"=>$config->name()) ));
			}
			if (!class_exists($handler)||!in_array('LSYS\MQ\Handler',class_implements($handler))){
				throw new Exception(__("MQ handler [:handler] wong,not extends \LSYS\MQ\Handler",array("handler"=>$handler)));
			}
			$obj=new static(new $handler($config));
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