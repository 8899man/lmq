<?php
namespace LSYS\MQ\Handler;
use LSYS\MQ\Handler;
use LSYS\MQ\Message;
use LSYS\Config;
use LSYS\Loger;
use LSYS\Exception;
use AliyunMNS\Client;
use AliyunMNS\Model\SubscriptionAttributes;
use AliyunMNS\Requests\PublishMessageRequest;
use AliyunMNS\Requests\CreateTopicRequest;
use AliyunMNS\Exception\MnsException;
class MQS implements Handler {
	/**
	 * @var \Redis
	 */
	protected $_config;
	protected $client;
	public function __construct(Config $config){
		$this->_config=$config->get("config",array())+array(
			'accessId'=>'',
			'accessKey'=>'',
			'endPoint'=>'',
		);
		$this->client = new Client($this->_config['endPoint'], $this->_config['accessId'], $this->_config['accessKey']);
	}
	
	protected function get_by_url($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
	
		$output = curl_exec($ch);
	
		curl_close($ch);
	
		return $output;
	}
	
	protected function verify($data, $signature, $pubKey)
	{
		$res = openssl_get_publickey($pubKey);
		$result = (bool) openssl_verify($data, base64_decode($signature), $res);
		openssl_free_key($res);
	
		return $result;
	}
	
	public function listen($topic){
		
		if (!function_exists('getallheaders'))
		{
			function getallheaders()
			{
				$headers = array();
				foreach ($_SERVER as $name => $value)
				{
					if (substr($name, 0, 5) == 'HTTP_')
					{
						$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
					}
				}
				return $headers;
			}
		}
		// 1. get the headers and check the signature
		$tmpHeaders = array();
		$headers = getallheaders();
		foreach ($headers as $key => $value)
		{
			if (0 === strpos($key, 'x-mns-'))
			{
				$tmpHeaders[$key] = $value;
			}
		}
		ksort($tmpHeaders);
		$canonicalizedMNSHeaders = implode("\n", array_map(function ($v, $k) { return $k . ":" . $v; }, $tmpHeaders, array_keys($tmpHeaders)));
		
		$method = $_SERVER['REQUEST_METHOD'];
		$canonicalizedResource = $_SERVER['REQUEST_URI'];
		error_log($canonicalizedResource);
		
		$contentMd5 = '';
		if (array_key_exists('Content-MD5', $headers))
		{
			$contentMd5 = $headers['Content-MD5'];
		}
		else if (array_key_exists('Content-md5', $headers))
		{
			$contentMd5 = $headers['Content-md5'];
		}
		
		$contentType = '';
		if (array_key_exists('Content-Type', $headers))
		{
			$contentType = $headers['Content-Type'];
		}
		$date = $headers['Date'];
		
		$stringToSign = strtoupper($method) . "\n" . $contentMd5 . "\n" . $contentType . "\n" . $date . "\n" . $canonicalizedMNSHeaders . "\n" . $canonicalizedResource;
		error_log($stringToSign);
		
		$publicKeyURL = base64_decode($headers['x-mns-signing-cert-url']);
		$publicKey = $this->get_by_url($publicKeyURL);
		$signature = $headers['Authorization'];
		
		$pass = $this->verify($stringToSign, $signature, $publicKey);
		if (!$pass)
		{
			Loger::instance()->addDebug("mqs content verify fail");
			http_response_code(400);
			return;
		}
		// 2. now parse the content
		$content = file_get_contents("php://input");
		if (!empty($contentMd5) && $contentMd5 != base64_encode(md5($content)))
		{
			Loger::instance()->addDebug("mqs content not match");
			http_response_code(401);
			return;
		}
		$msg = new SimpleXMLElement($content);
		$_msg=@unserialize($msg->Message);
		if ($_msg instanceof Message){
			try{
				$_msg->exec();
			}catch (\Exception $e){
				http_response_code(500);
				loger::instance()->addError($e);
				die($e->getMessage());
			}
		}else{
			Loger::instance()->addDebug("mqs content not match");
		}
		http_response_code(200);
		echo "ok";
	}
	public function push($message,$topic){
		$request = new CreateTopicRequest($topic);
		try
		{
			$res = $this->client->createTopic($request);
		}
		catch (MnsException $e)
		{
			Loger::instance()->addError($e);
			return false;
		}
		$topic = $this->client->getTopicRef($topic);
		// 3. send message
		$messageBody = $message;
		// as the messageBody will be automatically encoded
		// the MD5 is calculated for the encoded body
		$request = new PublishMessageRequest($messageBody);
		try
		{
			$res = $topic->publishMessage($request);
		}
		catch (MnsException $e)
		{
			Loger::instance()->addError($e);
			return false;
		}
		return true;
	}
	public function subscribe($topic,$host,$port){
		$topic = $this->client->getTopicRef($topic);
		$attributes = new SubscriptionAttributes($topic, 'http://' . $host . ':' .$port);
		try{
		$topic->subscribe($attributes);
		}
		catch (MnsException $e)
		{
			throw new Exception($e->getMessage(),$e->getCode(),$e);
		}
		return true;
	}
	public function unsubscribe($topic,$host,$port){
		$topic = $this->client->getTopicRef($topic);
		try
		{
			$topic->unsubscribe($topic);
			echo "Unsubscribe Succeed! \n";
		}
		catch (MnsException $e)
		{
			throw new Exception($e->getMessage(),$e->getCode(),$e);
		}
		return true;
	}
}