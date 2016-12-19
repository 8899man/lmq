<?php
use \LSYS\MQ;
require_once  __DIR__."/Bootstarp.php";
require_once  __DIR__."/msq.php";
ini_set('memory_limit','32M');
MQ::instance()->listen();