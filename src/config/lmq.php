<?php
return array(
	"redis"=>array(
		'handler'=>\LSYS\MQ\Handler\Redis::class,
	),
	"kafka"=>array(
		'handler'=>\LSYS\MQ\Handler\Kafka::class,
		'config'=>array(
			'host'=>'127.0.0.1',
			'timeout'=>'60',
			'group'=>'default',
		)
	),
	"mqs"=>array(
		'handler'=>\LSYS\MQ\Handler\MQS::class,
		'config'=>array(
			'accessId'=>'your access id',
			'accessKey'=>'your access key',
			'endPoint'=>'http://127.0.0.1/callback.php',
		)
	),
	"gearman"=>array(
		'handler'=>\LSYS\MQ\Handler\Gearman::class,
		'server'=>null
	),
);