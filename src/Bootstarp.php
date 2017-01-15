<?php
/**
 * lsys mq
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\MQ{
	function __($string, array $values = NULL, $domain = "default")
	{
		$i18n=\LSYS\I18n::instance(__DIR__."/I18n/");
		return $i18n->__($string,  $values , $domain );
	}
}
namespace {
	LSYS\Config\File::dirs(array(
			__DIR__."/config",
	));
}