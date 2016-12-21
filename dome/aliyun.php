<?php
use \LSYS\MQ;
require_once  __DIR__."/Bootstarp.php";
require_once  __DIR__."/msq.php";
MQ::$config="lmq.mqs";
MQ::instance()->listen();