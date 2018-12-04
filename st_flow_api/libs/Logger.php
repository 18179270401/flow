<?php 
/**
 * 程序逻辑日志
 */
class Logger
{
	static public function error($msg) {
		return self::write($msg, 'errorlog');
	}
	
	static public function debug($msg) {
		return self::write($msg, 'debuglog');
	}

	static public function warn($msg) {
		return self::write($msg, 'warnlog');
	}
	
	static public function write($msg, $type) {
		$msg = serialize($msg);
		$path = ROOT_PATH.'/logs/'.date('Ym').'/';
		if(!file_exists($path)) {
			@mkdir($path, 0777, true);
			@chmod($path, 0777);
		}
		$logFile = $path.date('d').'_'.$type.'.log'; //$path.'error_'.date('d').'.log';
		$now = date('Y-m-d H:i:s');
		$msg = "[{$now}] {$msg} \n";
		error_log($msg, 3, $logFile);
	}
	
	static public function dump($msg, $file) {
		error_log($msg, 3, $file);
	}
	
	static public function halt($msg) {
		die($msg);
	}
	
	/**
	 * @param string $funcName 操作函数名
	 * @param string $errMsg 错误信息
	 * @param arr $params 参数
	 */
	static public function dbwrite($funcName, $errMsg, $parms) {
		$parms = json_encode($parms);
		$errMsg = json_encode($errMsg);
		$path = ROOT_PATH.'/logs/'.date('Ym').'/';
		if(!file_exists($path)) {
			@mkdir($path, 0777, true);
			@chmod($path, 0777);
		}
		$logFile = $path.date('d').'_db'.'.log';
		$now = date('Y-m-d H:i:s');
		$msg = "[{$now}] [{$funcName}] [{$errMsg}] [{$parms}] \n";
		error_log($msg, 3, $logFile);
	}

}
?>