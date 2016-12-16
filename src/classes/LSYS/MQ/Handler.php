<?php
namespace LSYS\MQ;
use LSYS\Config;
interface Handler{
	public function __construct(Config $config);
	public function push($message,$topic);
	public function pop($topic);
}