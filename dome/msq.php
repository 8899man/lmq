<?php
require_once  __DIR__."/Bootstarp.php";
// 设置默认配置
// \LSYS\MQ::$config="lmq.gearman";
class omsg extends \LSYS\MQ\Message{
	protected $_order_id;
	protected $_product_id;
	public function set_order_id($order_id){
		$this->_order_id=$order_id;
		return $this;
	}
	public function set_product_id($product_id){
		$this->_product_id=$product_id;
		return $this;
	}
	public function serialize () {
		return json_encode(array($this->_order_id,$this->_product_id));
	}
	public function unserialize ($serialized) {
		list($this->_order_id,$this->_product_id)=json_decode($serialized,true);
	}
	public function exec(){
		print_r($this->_order_id);
		print_r($this->_product_id);
	}
}