<?php
LMQ\MQ::config(new \LMQ\ConfigArr("redis", \LMQ\Handler\Redis::class, array(
	'host'=>'192.168.2.88',
	'port'=>6379
)));
class oparam extends \LMQ\Param{
	
}

class omsg extends \LMQ\Message{
	public function __construct(oparam $param,$topic=null){
		parent::__construct($param,$topic);
	}
	public function exec(){
		$param=$this->get_param();
		print_r($param);
	}
}