<?php
if(PHP_SAPI!='cli') die("plase run in cli");
define("LSYS_UNIX_FORK_RUN", DIRECTORY_SEPARATOR != '\\'&&function_exists('pcntl_fork'));
if(LSYS_UNIX_FORK_RUN){
	//set run user...
	if (!defined('LSYS_MQ_USER')){
		$userinfo = posix_getpwnam('www');
		if(isset($userinfo['uid']))$u=$userinfo['uid'];
		$userinfo = posix_getpwnam('nobody');
		if(isset($userinfo['uid']))$u=$userinfo['uid'];
		foreach($argv as $v){
			if(isset($is_u)){
				$u=trim($v);break;
			}
			if($v=='-u')$is_u=true;
		}
		if (isset($u))define("LSYS_MQ_USER", $u);
	}
	if (defined('LSYS_MQ_USER')){
		$userinfo = posix_getpwnam(LSYS_MQ_USER);
		if(!isset($userinfo['uid'])) die("can't find :".LSYS_MQ_USER);
		@posix_setuid($userinfo['uid']);
	}
	unset($u,$is_u,$userinfo);
lmq_restart://restart...
	//
	$pid = pcntl_fork();
	if ($pid == -1) die(pcntl_get_last_error());
	else if ($pid) {
		//wait child end..
		pcntl_wait($status,WUNTRACED);
		sleep(1);
		goto lmq_restart;
		exit;
	}
}

