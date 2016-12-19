<?php
namespace LSYS\MQ{
	function __($string, array $values = NULL, $domain = "db")
	{
		$i18n=\LSYS\I18n::instance(dirname(__FILE__)."/I18n/");
		return $i18n->__($string,  $values , $domain );
	}
};
namespace {
	LSYS\Config\File::dirs(array(
			dirname(__FILE__)."/config",
	));
};