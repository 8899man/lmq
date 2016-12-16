<?php
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