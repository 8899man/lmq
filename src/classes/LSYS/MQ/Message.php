<?php
namespace LSYS\MQ;
abstract class Message implements \Serializable{
	abstract public function exec();
}