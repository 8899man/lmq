<?php
/**
 * lsys mq
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\MQ\Handler;
use LSYS\MQ\Handler;
use LSYS\MQ\Message;
use LSYS\Config;
use LSYS\Exception;
use LSYS\Loger;
class Stomp implements Handler {
	/**
	 * @var Config
	 */
	protected $_config;
	public function __construct(Config $config){
		if (!class_exists('Stomp')) throw new Exception(__("plase install Stomp ext"));
		$this->_config=$config;
	}
	public function listen($topic){
		$stomp = new \Stomp($this->_config->get("server"));
		$stomp->subscribe($topic);
		while (true){
			$frame = $stomp->readFrame();
			if ($frame ===false)continue;
			$_msg=@unserialize($frame);
			if ($_msg instanceof Message){
				try{
					$_msg->exec();
				}catch (\Exception $e){
					loger::instance()->add_error($e);
				}
			}else{
				Loger::instance()->add_debug("mqs bad:".$workload);
			}
			unset($_msg);
			$stomp->ack($frame);
		}
		unset($stomp);
	}
	public function push($message,$topic){
		$stomp = new \Stomp($this->_config->get("server"));
		return $stomp->send($topic, $message);
	}
	
}