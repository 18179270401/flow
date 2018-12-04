<?php

	//函数库
	require_once dirname(__FILE__).'/libs/DB.php';
	defined('DB_PREFIX') or define('DB_PREFIX', 't_flow_');


	/**
	 * 获取客户端IP地址
	 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
	 * @param boolean $adv 是否进行高级模式获取（有可能被伪装） 
	 * @return mixed
	 */
	function get_client_ip($type = 0,$adv=false) {
	    $type       =  $type ? 1 : 0;
	    static $ip  =   NULL;
	    if ($ip !== NULL) return $ip[$type];
	    if($adv){
	        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
	            $pos    =   array_search('unknown',$arr);
	            if(false !== $pos) unset($arr[$pos]);
	            $ip     =   trim($arr[0]);
	        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
	            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
	        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
	            $ip     =   $_SERVER['REMOTE_ADDR'];
	        }
	    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
	        $ip     =   $_SERVER['REMOTE_ADDR'];
	    }
	    // IP地址合法验证
	    $long = sprintf("%u",ip2long($ip));
	    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
	    return $ip[$type];
	}


	/**
	 * 用正则表达式验证手机号码的格式是否正确(中国大陆区)
	 * @param integer $tel    所要验证的手机号
	 * @return boolean true格式正确/false格式错误
	 */
	function isMobile($tel) {
		return preg_match("/^1[34578][0-9]{9}$/", $tel) ? true : false;
	}



	/**
	 * 写错误日志，供查找问题
	 * @param array $msg 日志数组
	 */
	function write_error_log($msg) {

		$msg = serialize($msg);

		$path = ROOT_PATH.'/logs/'.date('Ym').'/';

		if(!file_exists($path)) {

			@mkdir($path, 0777, true);
			@chmod($path, 0777);

		}

		$logFile = $path.date('d').'_error'.'.log';

		$now = date('Y-m-d H:i:s');

		$msg = "[{$now}] {$msg} \n";

		if(!file_exists($logFile)){

			error_log($msg, 3, $logFile);
			@chmod($logFile, 0777);	

		}else{

			error_log($msg, 3, $logFile);
		}
	}

	/**
	 * 写调试日志，供查找问题
	 * @param array $msg 日志数组
	 */
	function write_debug_log($msg) {

		$msg = serialize($msg);

		$path = ROOT_PATH.'/logs/'.date('Ym').'/';

		if(!file_exists($path)) {

			@mkdir($path, 0777, true);
			@chmod($path, 0777);

		}

		$logFile = $path.date('d').'_debug'.'.log';

		$now = date('Y-m-d H:i:s');

		$msg = "[{$now}] {$msg} \n";

		if(!file_exists($logFile)){

			error_log($msg, 3, $logFile);
			@chmod($logFile, 0777);	
		}else{

			error_log($msg, 3, $logFile);

		}
	}


	function write_deal_log($msg) {

		$msg = serialize($msg);

		$path = ROOT_PATH.'/logs/'.date('Ym').'/';

		if(!file_exists($path)) {

			@mkdir($path, 0777, true);
			@chmod($path, 0777);
		}

		$logFile = $path.date('d').'_deal'.'.log';

		$now = date('Y-m-d H:i:s');

		$msg = "[{$now}] {$msg} \n";

		error_log($msg, 3, $logFile);
	}




	/**
	 * 验证手机号并返回结果(已检查的手机号会存数据库)
	 * @param unknown $mobile
	 * @return string|Ambigous <string, mixed>
	 */
	function CheckMobile($mobile) {

		if(!isMobile($mobile)) {
			$json['status'] = 'error';
			$json['msg'] = "失败：手机号不符合基本规则。";
			return $json;
		}

	    //读取数据是否有数据
	    $result = DB::select(DB_PREFIX.'sys_mobile_dict', array('mobile' => $mobile), '*', 0);
	    if ($result) {
	        $result['status'] = 'success';
	        return $result;
	    }

		$ret = DB::getDB()->query("CALL p_get_mobile_info('{$mobile}');")->fetch();

		if(empty($ret)) {
			$ret['status'] = 'error';
			$ret['msg'] = "判断运营商失败。";

		} else {
			$ret['status'] = 'success';
			$ret['mobile'] = $mobile;

	        $ins = array(
	            'mobile'        => $mobile,
	            'operator_id'   => $ret['operator_id'],
	            'operator_name' => $ret['operator_name'],
	            'province_id'   => $ret['province_id'],
	            'province_name' => $ret['province_name'],
	            'area_code'     => $ret['area_code'],
	            'city_name'     => $ret['city_name'],
	            'card'          => '',
	            'city_id'		=>	$ret['city_id'],
	            //'postcode'      => '',
	        );

	        DB::insert(DB_PREFIX.'sys_mobile_dict', $ins);

		}

		return $ret;

	}



	/** 发送 JSON 数据 */
	function PostJSON($url, $jsonstr) {

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonstr);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT,30); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($jsonstr)));
		$result = curl_exec($ch);

		if(curl_errno($ch)==28){

			$result ='timeout';

		}

		curl_close($ch);

		write_debug_log('[回调信息]回调地址：'.$url.',发送数据：'.$jsonstr.',接收数据：'.$result);

		return $result;

	}

	/**
	 * 生成订单、代理商和企业的流水编号
	 * @$number 代理商、企业、手机号
	 * @$digit  生成随时的个数(默认为4位)
	 */

	function apply_number($number,$digit = 4){
		$salttype = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
		$keys = array_rand($salttype, $digit);
		foreach ($keys as $v) {
			$ints.= $salttype[$v];
		}
		return $number.round(microtime(true) * 1000).$ints;
	}





