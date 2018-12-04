<?php
require_once ROOT_PATH.'/libs/Logger.php';

/**
 *  数据库类库
 */
class DB
{
	//Master数据库连接 
	private static $_dbh = null;	
	
	/**
	 * 获取数据库链接
	 */

	public static function getDB() {
		if (is_null(self::$_dbh))
		{

			require_once ROOT_PATH.'/conf/config.php';
			$db = $config;
			self::$_dbh = self::_connect($db);
		}
		return self::$_dbh;
	}
	
	/**
	 * 连接数据库
	 */
	private static function _connect($dbConf) {
		try {
		    $dbh = new PDO('mysql:host='.$dbConf['hostname'].';dbname='.$dbConf['database'], $dbConf['username'], $dbConf['password']);
		    $dbh->exec("set names utf8");
		} catch (PDOException $e) {

		    throw new PDOException($e->getMessage());

		}
		return $dbh;
	}
	
	private static function Exception($obj){
		$error_array = $obj->errorinfo();
		throw new PDOException('数据库报错信息：'.$error_array[2]);
		
	}

	/**
	 * 断开数据库连接
	 */
	public static function close(){

		self::$_dbh = null;

	}


	/**
	 * 更新操作
	 * @param string $table 表名
	 * @param array $setArr 要更新的键值对数组
	 * @param array $whereArr 要更新的条件对数组
	 * @param string $limit 限制更新的数量
	 * @return bool true/false
	 */
	static public function update($table, $setArr, $whereArr, $limit=' 1 ') {
		if(empty($table) || !is_array($setArr) || count($setArr)<=0 || !is_array($whereArr) || count($whereArr)<=0)
		{
			return false;
		}
		
		$sc = array();
		foreach($setArr as $keys => $vals)
		{
			$sc[] = "`{$keys}`=:{$keys}";
		}
		$setCond = implode(',', $sc);
		
		$wc = array();
		foreach($whereArr as $keyw => $valw)
		{
			$wc[] = "`{$keyw}`=:{$keyw}";
		}
		$whereCond = implode(' AND ', $wc);
		
		$sql = "UPDATE `{$table}` SET {$setCond} WHERE {$whereCond}  "; //LIMIT $limit
		//if(in_array($table, array('t_flow_enterprise_account', 't_flow_proxy_account'))) {
			//Logger::dbwrite(__METHOD__, $sql, func_get_args());
		//}

		$sth = DB::getDB()->prepare($sql);
		foreach($setArr as $key_sc => $val_sc)
		{
			$sth->bindValue(':'.$key_sc, $val_sc);
		}
		
		foreach($whereArr as $key_wc => $val_wc)
		{
			$sth->bindValue(':'.$key_wc, $val_wc);
		}

		$ret = $sth->execute();

		if(!$ret){

			DB::Exception($sth);
			return false;
		}

		$row = $sth->rowCount();

		return true;
		//return $row > 0 ? true : false;
	}
	
	/**
	 * 插入操作
	 * @param string $table 表名
	 * @param array $set 要插入的键值对数组     1D
	 * @return bool/int 
	 */
	static public function insert($table, $setArr) {
		$condition = array();
		
		foreach ($setArr as $key=>$val)
		{
			$condition[] = "`{$key}`=:{$key}";
		}
		
		$setStr = implode(',', $condition);
		$sql = "INSERT INTO `{$table}` SET {$setStr} ";
		$dbh = DB::getDB();
		$sth = $dbh->prepare($sql);
		foreach ($setArr as $k => $v)
		{
			$sth->bindValue(':'.$k, $v);
		}
		
		//Logger::dbwrite(__METHOD__, $sql, func_get_args());
		
		$ret = $sth->execute();

		if(!$ret){

			DB::Exception($sth);
			return 0;
		}

		$id = $dbh->lastInsertId();
		return $id;
	}
	

	/**
	 * 插入操作
	 * @param string $table 表名
	 * @param string $sql
	 * @return bool/int 
	 */
	static public function insert2($sql) {
		$ret = 0;
		if(!empty($sql)) {
			$sth = DB::getDB()->prepare($sql);
			$rt = $sth->execute();

			if(!$rt) {

				DB::Exception($sth);
				return 0;

			} else {
				$row = $sth->rowCount();
				$ret = $row;
			}
		}
		return $ret;
	}
	
	/**
	 * 通过条件查找信息
	 * @param string $table 表名
	 * @param int $whereArr  条件数组
	 * @param string $fields 字段列表默认全部*
	 * @param int $multi 是否二维数组，默认二维
	 * @return array/bool 1D/2D
	 */
	static public function select($table, $whereArr, $fields = '*', $multi=1) {
		$condition = array();
		
		foreach ($whereArr as $key=>$val)
		{
			$condition[] = "{$key}=:{$key}";
		}
		
		$whereStr = implode(' AND ', $condition);
		
		$fields = empty($fields)?' * ':$fields;
		
		$sql = "SELECT {$fields} FROM {$table} WHERE {$whereStr} ";

		$sth = DB::getDB()->prepare($sql);
		
		foreach ($whereArr as $k => $v)
		{
			$sth->bindValue(':'.$k, $v);
		}
		
		$ret = $sth->execute();

		if(!$ret){

			DB::Exception($sth);

			return array();
		}


		if($multi) {
			$row = $sth->fetchAll(PDO::FETCH_ASSOC);
		} else {
			$row = $sth->fetch(PDO::FETCH_ASSOC);
		}

		return $row;
	}
	
	/**
	 * 通过条件查找信息
	 * @param string $table 表名
	 * @param int $whereArr  条件数组
	 * @param string $fields 字段列表默认全部*
	 * @param int $multi 是否二维数组，默认二维
	 * @return array/bool 1D/2D
	 */
	static public function select_for_update($table, $whereArr, $fields = '*', $multi=1) {
		$condition = array();
	
		foreach ($whereArr as $key=>$val)
		{
			$condition[] = "{$key}=:{$key}";
		}
	
		$whereStr = implode(' AND ', $condition);
	
		$fields = empty($fields)?' * ':$fields;
	
		$sql = "SELECT {$fields} FROM {$table} WHERE {$whereStr} FOR UPDATE";
	
		$sth = DB::getDB()->prepare($sql);
	
		foreach ($whereArr as $k => $v)
		{
			$sth->bindValue(':'.$k, $v);
		}
	
		$ret = $sth->execute();

		if(!$ret){

			DB::Exception($sth);

			return array();
		}

		if($multi) {
			$row = $sth->fetchAll(PDO::FETCH_ASSOC);
		} else {
			$row = $sth->fetch(PDO::FETCH_ASSOC);
		}
		
		return $row;
	}
	
	/**
	 * 直接执行一条sql查询语句
	 * @param string $sql 
	 * @return array 1D
	 */
	static public function query1($sql) {
		$ret = array();
		if(!empty($sql)) {
			$sth = DB::getDB()->prepare($sql);
			$rt = $sth->execute();
			$row = $sth->fetch(PDO::FETCH_ASSOC);
			if(!$rt) {

				DB::Exception($sth);

				return array();

			} else {
				$ret = $row;
			}
		}
		return $ret;
	}
	
	/**
	 * 直接执行一条sql查询语句
	 * @param string $sql 
	 * @return array 2D
	 */
	static public function query2($sql) {
		$ret = array();
		if(!empty($sql)) {
			$sth = DB::getDB()->prepare($sql);
			$rt = $sth->execute();
			$row = $sth->fetchAll(PDO::FETCH_ASSOC);
			if(!$rt) {

				DB::Exception($sth);

				return array();

			} else {
				$ret = $row;
			}
		}
		return $ret;
	}
	
	/**
	 * 删除记录
	 * @param string $table 表名
	 * @param array $whereArr  条件数组
	 * @return array/bool
	 */
	static public function delete($table, $whereArr) {
		$condition = array();
		
		foreach ($whereArr as $key=>$val)
		{
			$condition[] = "{$key}=:{$key}";
		}
		$whereStr = implode(' AND ', $condition);
		$sql = "DELETE FROM {$table} WHERE {$whereStr}";
		$sth = DB::getDB()->prepare($sql);
		foreach ($whereArr as $k => $v)
		{
			$sth->bindValue(':'.$k, $v);
		}
		$ret = $sth->execute();

		if(!$ret){

			DB::Exception($sth);

			return false;
		}

		$row = $sth->rowCount();

		return  $row > 0 ? true : false;
	}


	
	
}