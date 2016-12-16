<?php
return array(
	"redis"=>array(
		'handler'=>LSYS\MQ\Handler\Redis::class,
		'topic'=>'LMQ',
	),
	"kafka"=>array(
		'handler'=>LSYS\MQ\Handler\Kafka::class,
		'topic'=>'LMQ',
		'config'=>array(
			'host'=>'127.0.0.1',
			'timeout'=>'60',
			'group'=>'default',
		)
	),
);