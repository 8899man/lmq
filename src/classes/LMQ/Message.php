<?php
namespace LMQ;
abstract class Message implements \Serializable{
	protected $_param;
	public function __construct(Param $param){
		$this->_param=$param;
	}
	abstract public function exec();
	public function serialize () {
		return serialize($this->_param);
	}
	public function unserialize ($serialized) {
		$this->_param=unserialize($serialized);
	}
	public function get_param(){
		return $this->_param;
	}
}