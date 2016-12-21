<?php
namespace LSYS\MQ;
abstract class Message implements \Serializable{
	public static function factory(){
		return new static();
	}
	abstract public function exec();
}