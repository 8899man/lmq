<?php
/**
 * lsys mq
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\MQ\Handler;
use LSYS\MQ\Handler;
use LSYS\Exception;
use LSYS\MQ\Message;
use LSYS\Config;
class Kafka implements Handler {
	protected $_config;
	protected $_produce;
	protected $_consumer;
	public function __construct(Config $config){
		if(!class_exists("\ZooKeeper")) throw new Exception(__('plase install zookeeper extension.'));
		if(!class_exists("\Kafka\Produce")) throw new Exception(__('plase run: composer require nmred/kafka-php'));
		$this->_config=$config->get('config',array())+array(
			'host'=>'127.0.0.1',
			'timeout'=>'60',
			'group'=>'default',
		);
	}
	protected function _produce(){
		if (!$this->_produce){
			$this->_produce = \Kafka\Produce::getInstance($this->_config['host'], $this->_config['timeout']);
		}
		return $this->_produce;
	}
	protected function _consumer(){
		if (!$this->_consumer){
			$this->_consumer = \Kafka\Consumer::getInstance($this->_config['host'], $this->_config['timeout']);
		}
		return $this->_consumer;
	}
	public function listen($topic){
		$consumer=$this->_consumer();
		$consumer->setGroup($this->_config['group']);
		$consumer->setFromOffset(true);
		$consumer->setTopic($topic);
		while (true){
			$result = $consumer->fetch();
			if(count($result)==0){
				sleep(1);//not data sleep
				return null;
			}
			foreach ($result as $topicName => $partition) {
				foreach ($partition as $partId => $messageSet) {
					foreach ($messageSet as $message) {
						$msg=(string)$message;
						$_msg=@unserialize($msg);
						if ($_msg instanceof Message){
							try{
								$_msg->exec();
							}catch (\Exception $e){
								loger::instance()->add_error($e);
							}
						}
					}
				}
			}
		}
	}
	public function push($message,$topic){
		$produce=$this->_produce();
		try{
			$partitions = $produce->getAvailablePartitions($topic);
		}catch (\Exception $e){
			return false;
		}
		if(count($partitions)==0) return false;
		if(count($partitions)==1) $partition=array_pop($partitions);
		else $partition=rand(0,count($partitions)-1);
		$produce->setRequireAck(-1);
		$produce->setMessages($topic,$partition, $message);
		try{
			return $produce->send();
		}catch(\Kafka\Exception $e){
			return false;
		}
		return true;
	}
}