<?php
namespace LMQ;
class Param implements \Serializable{
	public static function factory($data){
		return new static($data);
	}
	protected $_data;
	public function __construct($data){
		$this->_data=$data;
	}
	public function serialize () {
		return json_encode($this->_data);
	}
	public function unserialize ($serialized) {
		$this->_data=json_decode($this->_data,true);
	}
}