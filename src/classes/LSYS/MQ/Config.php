<?php
namespace LSYS\MQ;
interface Config{
	public function name();
	public function as_array();
	public function handler();
}