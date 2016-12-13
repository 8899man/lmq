<?php
use LMQ\MQ;
require __DIR__.'/../src/unix_utils.php';
require_once  __DIR__."/Bootstarp.php";
require_once  __DIR__."/msq.php";
ini_set('memory_limit','32M');
MQ::instance()->listen();



