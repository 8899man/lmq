<?php
namespace LSYS\MQ;
class Utils{
	/**
	 * 后台进程是否在运行
	 * @param array $process_name
	 * @return boolean
	 */
	public static function is_daemon($process_name=null){
		$is_windows=DIRECTORY_SEPARATOR=='\\';
		if (empty($process_name)) return false; 
		ob_start();
		if (!$is_windows) system('ps aux');
		else system('wmic  process where caption="php.exe" get caption,commandline /value'); 
		$ps=ob_get_contents();
		ob_end_clean();
		$ps = explode("\n", $ps);
		$out=[];
		foreach ($ps as $v){
			$v=trim($v);
			if (empty($v))continue;
			$p=strrpos($v," ");
			if ($p===false) continue;
			$out[]=trim(substr($v,$p));
		}
		if(!in_array($process_name, $out)) return false;
		return true;
	}
}