<?php
namespace LMQ\Handler;
interface RedisShare {
	/**
	 * @return \Redis
	 */
	public static function get_redis(array $config); 
}