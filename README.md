# 消息队列封装

>封装消息队列,实现对外使用使保持一致接口

使用示例:

1. 实现消息体接口
```
class omsg extends \LSYS\MQ\Message{
	protected $_order_id;
	protected $_product_id;
	public function set_order_id($order_id){
		$this->_order_id=$order_id;
		return $this;
	}
	public function set_product_id($product_id){
		$this->_product_id=$product_id;
		return $this;
	}
	public function serialize () {//需要实现方法
		return json_encode(array($this->_order_id,$this->_product_id));
	}
	public function unserialize ($serialized) {//需要实现方法
		list($this->_order_id,$this->_product_id)=json_decode($serialized,true);
	}
	public function exec(){//接收到消息时候执行的代码
		print_r($this->_order_id);
		print_r($this->_product_id);
	}
}
```

2. 消息生产
```
\LSYS\MQ::instance()->push(omsg::factory()->set_order_id(1)->set_product_id(1));
```

3. 消息处理
```
//为cli运行
define('LSYS_MQ_LIMIT',100);//执行多少次后重启
define('LSYS_MQ_USER','nobody');//执行用户
require __DIR__.'/../src/unix_utils.php';
require_once  __DIR__."/Bootstarp.php";
require_once  __DIR__."/msq.php";
ini_set('memory_limit','32M');
MQ::instance()->listen();
```

备注:
> aliyun 消息处理参见 aliyun.php