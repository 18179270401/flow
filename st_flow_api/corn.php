<?php

require_once dirname(__FILE__).'/core.php';

if( time()%3 == 1)
{
	OP::make_commit();

}elseif( time()%3 == 2)
{
	OP::make_query();

}else
{
	OP::make_callback();
}

//初始化Reids对象
//$app = new APP();

#$app->reload();

#运行程序
//$app->run($argv[1]);




#退出程序 释放数据库链接
exit;

class App{

	//Redis连接IP
	private $host = '127.0.0.1' ;

	//Redis连接端口
	private $port = 6379;

	//Redis对象
	private $redis = null;

	//Redis中下标对应的值
	private $value = null;

	//Redis中的下标
	private $key = null;

	//每个操作对应的最大限额
	private $max = array('commit' => 140, 'query' => 30 , 'callback'=> 60);

	//请求参数对应的操作类型
	private $type = array(   1 => 'commit' , 2=>'query' , 3 => 'callback' );

	public function __construct(){

		try{

			$this->redis = new Redis();
			$this->redis->connect($this->host,$this->port);

			if(!$this->redis)throw new Exception('Redis连接失败!');

		//	write_debug_log( array('[Reids][连接成功]') );

		}catch(Exception $e){

			write_error_log(array('[Reids][链接失败][失败原因]'.$e->getMessage()));
			write_debug_log(array('[Reids][链接失败][失败原因]'.$e->getMessage()));

			$this->redis = null;

		}

	}

	/**
	 *	应用开始
	 */
	public function run($value){

		//初始化key值
		$this->key = $this->type[$value];

		//判断该key下的当前数量是否大于限制
		if( $this->check($this->key)){

			switch ($this->key) {
				case 'commit':
					OP::make_commit();
					break;
				
				case 'query':
					OP::make_query();
					break;
				
				case 'callback':
					OP::make_callback();
					break;

			}

			$this->over($this->key);

		}

	}


	/**
	 *	验证该key值下是否超过最大限额，验证通道则自增
	 */
	private function check($key){

		//初始化连接 如果连接失败则返回 true 防止Redis连接出错导致程序中断
		if(  $this->redis === null ) return true;

		//获取下标值
		$this->value = $this->redis->get($key.'_samton');

		//记录日志  存入当时值
		#write_debug_log( array('[Reids]['.$key.'][进程数]['.$this->value.']') );

		//如果返回的值大于最大值 则 返回 false
		if ( $this->value >= $this->max[$key] )  return false;

		//如果递增失败了则写入日志
		$this->redis->incr($key);

		#write_debug_log( array('[Redis]['.$this->key.'][启动进程]') ) ;

		return true;
	}


	/**
	 *	验证该key值执行完成后释放 递减
	 */
	private function over($key){

		//初始化连接 如果连接失败则返回 true 防止Redis连接出错导致程序中断
		if(  $this->redis === null ) return true;

		//如果递减失败了则写入日志
		$this->redis->decr($key) ;

		#write_debug_log(array('[Redis]['.$this->key.'][结束进程]')) ;

		return '';

	}


	public function reload(){

		//对操作类型key值循环
		foreach($this->type as $key => $value){

			//重置该值为0
			$this->redis->set($value.'_samton',0);

		}

		write_debug_log(array('[Redis][计数器][重置成功]')) ;

	}

}



?>
