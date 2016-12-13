<?php

//有效防止子进程挂逼...
require_once  __DIR__."/Bootstarp.php";
require_once  __DIR__."/msq.php";



\LMQ\MQ::instance()->push(new omsg(oparam::factory('xxxx')));




