<?php
/**
 * lsys mq
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\MQ;
use LSYS\Config;
interface Handler{
	public function __construct(Config $config);
	public function push($message,$topic);
	public function listen($topic);
}