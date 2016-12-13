<?php
namespace LMQ\Handler;
use LMQ\Handler;
use LMQ\Exception;
use LMQ\Message;
class Kafka implements Handler {
	protected $_config;
	protected $_produce;
	protected $_consumer;
	public function __construct(array $config){
		if(!class_exists("\ZooKeeper")) throw new Exception('plase install zookeeper extension.');
		$this->_config=$config+array(
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
	public function pop($topic){
		$consumer=$this->_consumer();
		$consumer->setGroup($this->_config['group']);
		$consumer->setFromOffset(true);
		$consumer->setTopic($topic);
		$consumer->setMaxBytes(102400);
		$result = $consumer->fetch(0);
		foreach ($result as $topicName => $partition) {
			foreach ($partition as $partId => $messageSet) {
				foreach ($messageSet as $message) {
					return (string)$message;
				}
			}
		}
		return null;
	}
	public function push($message,$topic){
		$produce=$produce;
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
	}
}