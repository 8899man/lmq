<?php
use LSYS\MQ\Utils;

require_once  __DIR__."/Bootstarp.php";
require_once  __DIR__."/msq.php";
if (!Utils::is_daemon("consumer.php")) {
	http_response_code(404);
	//后台进程挂B
}