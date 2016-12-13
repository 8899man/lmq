<?php
namespace LMQ;
class ConfigArr implements Config{
	protected $_name;
	protected $_handler;
	protected $_config;
	public function __construct($name,$handler,array $config){
		$this->_name=$name;
		$this->_handler=$handler;
		$this->_config=$config;
	} 
	public function name(){
		return $this->_name;
	}
	public function as_array(){
		return $this->_config;
	}
	public function handler(){
		return $this->_handler;
	}
}