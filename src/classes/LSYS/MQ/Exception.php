<?php
namespace LSYS\MQ;
class Exception extends \Exception {
	public function __construct($message = "", $code = 0, \Exception $previous = NULL)
	{
		parent::__construct($message, (int) $code, $previous);
		$this->code = $code;
	}
}