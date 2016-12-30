<?php
use \LSYS\MQ;
//set run user...
define('LSYS_MQ_LIMIT',100);//执行多少次后重启
define('LSYS_MQ_USER','nobody');//执行用户
require __DIR__.'/../src/unix_utils.php';
require_once  __DIR__."/Bootstarp.php";
require_once  __DIR__."/msq.php";
ini_set('memory_limit','32M');
MQ::instance()->listen();

