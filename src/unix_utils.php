<?php
if(PHP_SAPI!='cli') throw new Exception("plase run in cli");
//有效防止进程挂逼...
if(DIRECTORY_SEPARATOR != '\\'){
lmq_restart://restart...
	//set run user...
	if (!defined('LMQ_USER')){
		$u=null;
		foreach($argv as $v){
			if(isset($is_u)){
				$u=trim($v);break;
			}
			if($v=='-u')$is_u=true;
		}
	}else $u=LMQ_USER;
	if($u){
		$userinfo = posix_getpwnam($u);
		if(!isset($userinfo['uid'])) throw new Exception("can't find :".$u);
		@posix_setuid($userinfo['uid']);
	}else{
		$userinfo = posix_getpwnam('nobody');
		if(isset($userinfo['uid'])){
			@posix_setuid($userinfo['uid']);
		}
		$userinfo = posix_getpwnam('www');
		if(isset($userinfo['uid'])){
			@posix_setuid($userinfo['uid']);
		}
	}
	//
	$pid = pcntl_fork();
	if ($pid == -1) throw new Exception(pcntl_get_last_error());
	else if ($pid) {
		//wait child end..
		pcntl_wait($status,WUNTRACED);
		sleep(1);
		goto lmq_restart;
		exit;
	}
}

