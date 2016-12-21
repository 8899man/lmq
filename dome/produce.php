<?php
require_once  __DIR__."/Bootstarp.php";
require_once  __DIR__."/msq.php";
\LSYS\MQ::instance()->push(omsg::factory()->set_order_id(1)->set_product_id(1));