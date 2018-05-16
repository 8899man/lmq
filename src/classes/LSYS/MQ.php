<?php
/**
 * lsys mq
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS;
use LSYS\MQ\Handler;
use LSYS\MQ\Message;

class MQ implements ConfigFinder{
	use \LSYS\ConfigFinder\ConfigShare;
	public static $topic='LMQ';
	/**
	 * @var string default instance name
	 */
	public static $config = 'lmq.redis';
	//inistances
	protected static $_instances=array();
	/**
	 * @param Config $config
	 * @return MQ
	 */
	public static function &instance(Config $config=null){
		if ($config === NULL){
			if (is_string(self::$config)){
				self::$config = self::find_config(self::$config);
			}
			$config=self::$config;
		}
		$name=$config->name();
		if (!isset(self::$_instances[$name])){
			self::$_instances[$name]=new static($config);
		}
		return self::$_instances[$name];
	}
	/**
	 * @var Config
	 */
	protected $_config;
	/**
	 * @var Handler
	 */
	protected $_handle;
	public function __construct(Config $config){
	    $handler=$config->get("handler",NULL);
	    if ($handler==null){
	        throw new Exception ( __('MQ handler not defined in [:name] configuration',array("name"=>$config->name()) ));
	    }
	    if (!class_exists($handler)||!in_array('LSYS\MQ\Handler',class_implements($handler))){
	        throw new Exception(__("MQ handler [:handler] wong,not extends \LSYS\MQ\Handler",array("handler"=>$handler)));
	    }
	    $this->_handle=new $handler($config);
	    $this->_config=$config;
	}
	public function __destruct(){
	    unset(self::$_instances[$this->_config->name()]);
	}
	public function listen($topic=null){
		$topic=$topic===null?self::$topic:$topic;
		$this->_handle->listen($topic);
	}
	public function push(Message $message,$topic=null){
		$status=$this->_handle->push(serialize($message),$topic===null?self::$topic:$topic);
		if ($status===false){
			try{
				$message->exec();
			}catch (\Exception $e){
				return false;
			}
		}
		return true;
	}
}