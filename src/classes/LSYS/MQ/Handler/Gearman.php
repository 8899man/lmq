<?php
namespace LSYS\MQ\Handler;
use LSYS\MQ\Handler;
use LSYS\MQ\Message;
use LSYS\Config;
use LSYS\Exception;
class Gearman implements Handler {
	/**
	 * @var Config
	 */
	protected $_config;
	public function __construct(Config $config){
		if (!class_exists('GearmanClient')) throw new Exception(__("plase install gearman ext"));
		$this->_config=$config;
	}
	public function listen($topic){
		$worker = new \GearmanWorker();
		$worker->addServers($this->_config->get("server"));
		$worker->addFunction($topic, function(\GearmanJob $job){
			$workload = $job->workload();
			$_msg=@unserialize($workload);
			if ($_msg instanceof Message){
				try{
					$_msg->is_daemon=true;
					$_msg->exec();
				}catch (\Exception $e){
					loger::instance()->addError($e);
				}
			}else{
				Loger::instance()->addDebug("mqs bad:".$workload);
			}
			unset($_msg);
		});
		while (1) {
			$ret = $worker->work();
			if ($worker->returnCode() != GEARMAN_SUCCESS) {
				break;
			}
		}
	}
	public function push($message,$topic){
		$client = new \GearmanClient();
		$client->addServers($this->_config->get("server"));
		$result = @$client->doBackground($topic, $message);
		return $result!='';
	}
	
}