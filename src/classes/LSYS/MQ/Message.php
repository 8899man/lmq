<?php
namespace LSYS\MQ;
abstract class Message implements \Serializable{
	public $is_daemon=false;
	public static function factory(){
		return new static();
	}
	abstract public function exec();
}