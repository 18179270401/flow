<?php
	
	#加载核心文件
	require_once dirname(__FILE__).'/../core.php'; 

	$method = substr( $_SERVER['PATH_INFO'] , 1 , strlen( $_SERVER['PATH_INFO'] )-1) ;

	$app = new App();

	if( method_exists( $app , $method ) )
	{
		try{

			$app->$method();

		}catch( Execption $e)
		{
			echo '<meta charset=utf-8>错误：'.$e->getMessage();
		}
		

	}else
	{
		echo '<meta charset=utf-8>错误：方法不存在';

	}
	
	
	class App
	{

		private $account = 'GLIUUUPY';

		private $key = 'E2D7m19Sfx56ZFGueSQHe61q2dc5xQ8y';

		private $submit_url = 'http://120.26.130.39:8083/Submit.php';

		private $query_url = 'http://120.26.130.39:8083/Query.php';

		private $phone = '13755775321';

		private $range = 0;

		private $size = '10';

		private $date;

		#初始化信息
		public function __construct()
		{
			if( isset( $_GET['account'] ) 	&&   $_GET['account'] 	!== ''	) $this->account 	= $_GET['account'];
			if( isset( $_GET['key'] ) 		&&   $_GET['key'] 		!== '' 	) $this->key 		= $_GET['key'];
			if( isset( $_GET['phone'] ) 	&&   $_GET['phone'] 	!== '' 	) $this->phone 		= $_GET['phone'];
			if( isset( $_GET['range'] ) 	&&   $_GET['range'] 	!== '' 	) $this->range 		= $_GET['range'];
			if( isset( $_GET['size'] ) 		&&   $_GET['size'] 		!== '' 	) $this->size 		= $_GET['size'];

			$this->date = date('YmdHis');
		}


		public function submit()
		{

			$array['account']			= 	$this->account;
			$array['action']  			= 	'Charge';
			$array['phone']   			= 	$this->phone;
			$array['range']   			= 	$this->range;
			$array['size']    			= 	$this->size;
			$array['timeStamp'] 		= 	$this->date;
			$array['take_effect_time'] 	=	'0';
			$array['sign']				=	$this->getsign($array);

			$result = $this->http_curl($this->submit_url , $array );

			write_down_log(array($result));

			if($result['error_code'] == 200)
			{
				$data = json_decode( $result['data'] , true );

				echo '<meta charset=utf-8>提交编码：'.$data['respCode'].'，错误信息：'.$data['respMsg'];
				if( $data['respCode'] == '0000' ) echo '，订单号：'.$data['orderID'];
			}else
			{
				throw new Exception('提交失败');
			}

		}

		public function find()
		{

		}

		public function callback()
		{
			$result = file_get_contents("php://input");

			write_down_log(array($result));

			echo 'Success';
		}


		private function getsign( $array )
		{
			$str = $this->key.'account='.$array['account'].'&action='.$array['action'].'&phone='.$array['phone'].'&range='.$array['range'].'&size='.$array['size'].'&timeStamp='.$array['timeStamp'].$this->key;
			return md5($str);
		}


		/*
		 *	接口名称:发送请求
		 *  功能描述:CURL套件发送HTTP请求
		 *  访问形式：本类调用
		 *	参数列表: 请求地址 , 请求参数 (如果附带参数二则为POST请求)
		 *  返回值  ：请求后的内容(string|NULL)
		 */
		private function http_curl( $url , $data = null , $type = null )
		{	

			if( $type === 'json' )
			{
				$header[] = 'Content-Type: application/json;charset=UTF-8';
			}

	        $curl = curl_init();

	        curl_setopt($curl, CURLOPT_URL, $url);

	        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

	        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

	        if (!empty($header)) curl_setopt($curl, CURLOPT_HTTPHEADER, $header ); 

	        if (  $data !== null ) {

	            curl_setopt($curl, CURLOPT_POST, 1);
	            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	        }

	        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

	        $output = curl_exec($curl);

	        $httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE); 

	        curl_close($curl);

	        return array('error_code' => $httpCode , 'data' => $output );
	       

		}

	}











	/**
	 * 写调试日志，供查找问题
	 * @param array $msg 日志数组
	 */
	function write_down_log($msg) {

		#序列化参数
		$msg = serialize($msg);

		#设置日志文件路径
		$path = ROOT_PATH.'/../st_flow_log/'.date('Ym').'/';

		#判断文件路径是否存在
		if(!file_exists($path)) {
			@mkdir($path, 0777, true);
			@chmod($path, 0777);
		}

		#生成文件名
		$logFile = $path.date('d').'_down'.'.log';

		#获取当前时间
		$now = date('Y-m-d H:i:s');

		#准备写入内容
		$msg = "[{$now}][".getuuid()."] {$msg} \n";

		#判断文件是否存在
		if(!file_exists($logFile)){

			#写入文件
			error_log($msg, 3, $logFile);

			#如果文件不存在，则由PHP重新将文件定义为权限
			@chmod($logFile, 0777);	

		}else{

			error_log($msg, 3, $logFile);
		}
	}


	function getuuid(){
	    if (function_exists('com_create_guid')){

	        return com_create_guid();

	    }else{

	        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
	        $charid = strtoupper(md5(uniqid(rand(), true)));
	        $hyphen = chr(45);// "-"
	        $uuid = chr(123)// "{"
	                .substr($charid, 0, 8).$hyphen
	                .substr($charid, 8, 4).$hyphen
	                .substr($charid,12, 4).$hyphen
	                .substr($charid,16, 4).$hyphen
	                .substr($charid,20,12)
	                .chr(125);// "}"
	        return $uuid;

	    }

	}