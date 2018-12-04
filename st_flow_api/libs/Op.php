<?php

require_once ROOT_PATH.'/libs/DB.php';
require_once ROOT_PATH.'/libs/Logger.php';
defined('DB_PREFIX') or define('DB_PREFIX', 't_flow_');


/**
 * 具体操作类
 */

class Op {
	

	/** 根据用户类型和ID获取相应的账户数据 */
    static public function get_account_info($user_type, $proxy_id, $enterprise_id) {
            $ret = array();
            if(1 == $user_type) {
                    $ret = DB::select_for_update(DB_PREFIX.'proxy_account', array('proxy_id'=>$proxy_id), '*', 0);
            } else if(2 == $user_type) {
                    $ret = DB::select_for_update(DB_PREFIX.'enterprise_account', array('enterprise_id'=>$enterprise_id), '*', 0);
            }

            return $ret;
    }


	/** 根据用户类型和ID获取相应数据 */
	static public function get_user_info($user_type, $proxy_id, $enterprise_id) {
		$ret = array();
		if(1 == $user_type) {
			$ret = DB::select(DB_PREFIX.'proxy', array('proxy_id'=>$proxy_id), '*', 0);
		} else if(2 == $user_type) {			
			$ret = DB::select(DB_PREFIX.'enterprise', array('enterprise_id'=>$enterprise_id), '*', 0);
		}
		
		return $ret;
	}
	


 	/**
	 * 获取所有通道id
	 * @return array 2D
	 */
	static public function get_channel_info_all() {

		$ret = array();

		$sql = "SELECT a.channel_id  FROM (
		SELECT DISTINCT   CASE order_status WHEN 0 THEN channel_id WHEN 3 THEN back_channel_id END AS channel_id 
		FROM t_flow_order_pre  WHERE is_using=0 AND order_status IN(0,3)
		) a
		INNER JOIN t_flow_channel b ON a.channel_id = b.channel_id";

		$arr_dc = DB::query2( $sql );

		if(!empty($arr_dc) && is_array($arr_dc)) $ret = $arr_dc;
	
		return $ret;
	}
	

	/**
	 * 提交订单到上游通道
	 */
	static public function make_commit() {

		try{

			//调用存储过程取到对应的数据

			$arr_order1 = DB::query2("CALL `p_get_commit_order`(2,@p_out_flag)");

			$is_commit = DB::query1('select @p_out_flag as status');


			if($is_commit['status'] && $arr_order1 ){
				$dev_str = '';
				foreach($arr_order1 as $k=>$v){
					$dev_str .= ','.$v['order_id'];
				}

				write_debug_log( array( '[流程开始][订单ID]'.$dev_str ) );

			}
			
			
		}catch(Exception $e){

			//记录日志
			write_error_log( array( '[数据库错误]'.$e->getMessage() ) );
			write_debug_log( array( '[数据库错误]'.$e->getMessage() ) );

			//关闭数据库连接
			DB::close();

			//退出脚本
			//die();
			return '';

		}

		//事务执行失败或者取出来的数组为空
		if($is_commit['status'] && $arr_order1){
			
			//将订单以通道ID为下标拼凑为二维数组 将一个通道下的订单分在一起处理
			foreach ($arr_order1 as $k1 => $order1) {
               
               $order1['old_order_status'] = $order1['order_status'];  //赋值初始状态

				//调试日志
				write_debug_log( array( '[通道分类订单ID]：'.$order1['order_id'].',[当前订单状态]：'.$order1['order_status'].'初始状态: '.$order1['old_order_status'] ) );

				//将订单状态为3的使用备用通道的ID，否则使用主要通道
				if($order1['order_status'] == 3 ) {
					$arrch[ $order1['back_channel_id'] ][] = $order1;
				} else {
					$arrch[ $order1['channel_id'] ][] = $order1;
				}
			}

			write_debug_log( array( '[通道分类完成]：',$arrch ) );

			//将拼凑好的二维数组循环发送给每个通道文件处理发送请求任务
			foreach ($arrch as $cid => $aorder) {

				$dev_str = '';
				foreach($aorder as $k=>$v){
					$dev_str .=  ','.$v['order_id'];
				}
				write_debug_log( array( '[通道分类后开始循环][通道ID]'.$cid,'[准备进入该通道订单ID]'.$dev_str) );

				$order_list = array();
				$aorderid = array();

				//由于该通道下的所有订单都走一个通道 只需要取其中一张订单的通道文件名即可
				$channel_file_name = $aorder[0]['channel_file_name'];

				write_debug_log( array( '[获取通道文件名]'.$channel_file_name,'[准备进入该通道订单ID]'.$dev_str ) );

				foreach($aorder as $k => $v){

					//以订单ID为KEY值进入处理
					$order_list[$v['order_id']] = $v;

					//获取当前要处理的通道下的所有订单的ID 
					$aorderid[] = $v['order_id'];

					write_debug_log( array( '[订单ID为下标]：'.$v['order_id'].',[当前订单状态]：'.$v['order_status'] ) );

				}
				
			
			

				//加载通道文件预警异常处理
				try{

					if( file_exists(ROOT_PATH."/channel/{$channel_file_name}.php") ){

						//加载通道对象文件
						require_once ROOT_PATH."/channel/{$channel_file_name}.php";

						write_debug_log( array( '[加载通道文件]：'.$channel_file_name ,'[订单ID]:',$aorderid ) );

					}else{

						//文件不存在则抛出异常
						throw new Exception(ROOT_PATH."/channel/{$channel_file_name}.php文件不存在！");

					}

				}catch(Exception $e){

					write_error_log( array( '[通道文件不存在]：'.$e->getMessage(),'[无法处理订单ID序列]:',$aorderid ) );
					write_debug_log( array( '[通道文件不存在]：'.$e->getMessage(),'[无法处理订单ID序列]:',$aorderid ) );
					

					continue;

				}




				//通道文件处理预警异常处理
				try{

					write_debug_log( array( '[通道文件准备进入]','[通道中预处理的订单ID]:',$aorderid ) );

					//调用通道并且执行发送请求
					$channel = new $channel_file_name($order_list, true);

					//获取返回的信息 其中包括订单请求后的状态以及说明
					$result_data = $channel->getData();

				}catch(Exception $e){

					write_debug_log( array( '[通道文件内部错误]：'.$e->getMessage(),'[通道中处理的订单ID]:',$aorderid ) );
					write_error_log( array( '[通道文件内部错误]：'.$e->getMessage(),'[通道中处理的订单ID]:',$aorderid ) );

					//跳过当前循环
					continue;

				}

				write_debug_log( array( '[通道文件执行完成]','[通道中处理的订单ID]:',$aorderid ) );
				//将订单状态写入数据库
				self::deal_commit($result_data, $aorderid,$channel_file_name);
			}
			
			write_debug_log( array( '[流程已完成]','[处理的订单ID]:',$aorderid ) );

		//取出数据为空或者事务失败处理
		}else{

			//关闭数据库连接
			DB::close();

			//退出脚本
		//	die();
         return '';
		}

	}


	
	static public function make_query() {

		$time = date('Y-m-d H:i:s',time()-600);

		DB::getDB()->beginTransaction();
		
		//查询订单状态为1,4以及未上锁的订单
		$sql = "SELECT order_id,order_status FROM t_flow_order_pre o WHERE o.`order_date` < '".$time."' and o.order_status IN (1,4) AND o.is_using = 0  ORDER BY o.`order_effect_date` ASC LIMIT 30 FOR UPDATE";
		$arr_order = DB::query2($sql);
	
		//当查询出来的数据为空 说明没有需要提交的订单 则取消本次定时任务
		if(!empty($arr_order) && is_array($arr_order)) {

			$arroid = array();
			
			//将查询出来的数据订单ID拼凑成一维数组
			foreach ($arr_order as $k => $orderinfo) {
				$arroid[] = $orderinfo['order_id'];
			}

			//将查询出来的数据全部上锁 防止重复处理（is_using = 1 为上锁 is_using = 2为已处理完成订单）
			$sql3 = "UPDATE ".DB_PREFIX."order_pre SET is_using = 1 WHERE order_id IN (".implode(',', $arroid).")";

			$rows = DB::getDB()->exec($sql3);

			write_debug_log(array('查询1,4状态上锁的SQL=>',$sql3));//调试代码

			//当查询出来的数据与影响行数一致的情况下 说明全部成功 否则事务失败处理
			if( count($arroid) == $rows ){

				//提交事务 并且解锁数据库数据锁
				DB::getDB()->commit();
				
				//将刚刚处理的上锁数据全部取出 取出数据为订单所有 以及通道文件名称
				$arr_order1 = DB::query2("SELECT o.*,c.`channel_file_name`,c.`channel_name`
						FROM ".DB_PREFIX."order_pre o
						LEFT JOIN ".DB_PREFIX."channel c ON (o.`back_channel_id` = c.`channel_id` and o.`order_status` = 4) or (o.`channel_id` = c.`channel_id` and o.`order_status` = 1)
						WHERE o.order_id in(".implode(',', $arroid).") ");
				
				$arrch = array();

				//将订单以通道ID为下标拼凑为二维数组 将一个通道下的订单分在一起处理
				foreach ($arr_order1 as $k1 => $order1) {

					$order1['old_order_status'] = $order1['order_status'];   //赋值初始状态

				write_debug_log( array($order1['order_id'].',[cx当前old订单状态]：'.$order1['old_order_status'] ) );

					//将订单状态为4的使用备用通道的ID，否则使用主要通道
					if($order1['order_status']==4) {
						$arrch[$order1['back_channel_id']][] = $order1;
					} else {
						$arrch[$order1['channel_id']][] = $order1;
					}

				}
				
				//将拼凑好的二维数组循环发送给每个通道文件处理发送请求任务
				foreach ($arrch as $cid => $aorder) {
					$order_list = array();
					$aorderid = array();
					
					//由于该通道下的所有订单都走一个通道 只需要取其中一张订单的通道文件名即可
					$channel_file_name = $aorder[0]['channel_file_name'];

					foreach($aorder as $k => $v){

						//以订单ID为下标进入处理
						$order_list[$v['order_id']] = $v;

						//获取当前要处理的通道下的所有订单的ID 
						$aorderid[] = $v['order_id'];
					}
	
					//加载通道对象文件
					require_once ROOT_PATH."/channel/{$channel_file_name}.php";

					//调用通道并且执行发送请求
					$channel = new $channel_file_name($order_list);

					//判断该通道下是否有查询函数
					if(method_exists($channel,'QueryResult')){

						write_debug_log(array('查询1,4状态准备发送数据的=>',$order_list));//调试代码

						$result_data = $channel->QueryResult();

						write_debug_log(array('返回查询结果准备处理=>',$result_data));//调试代码

						//将订单状态写入数据库
						self::deal_query($result_data, $aorderid);

					}else{

						write_error_log(array('没有查询函数被锁的订单=>',$aorderid));
					}
				}
					
			} else {

				//如果上锁没有全部上锁则事务回滚,取消本次定时任务 并且取消数据库数据锁
				DB::getDB()->rollBack();
			}
		}else{
			
		}
	}



	/**
	 * 给下游代理商或企业主动推送回调byjp2016613
	 */
	static public function make_callback() {

		//获取需要回调数据的数组
		try{

			//调用存储过程取到对应的数据

			$arr_cbinfo = DB::query2("CALL `p_get_callback_order`(0,30,@p_out_flag)");

			$is_commit = DB::query1('select @p_out_flag as status');

		}catch(Exception $e){

			//记录日志
			write_error_log( array( '[数据库错误]'.$e->getMessage() ) );
			write_debug_log( array( '[数据库错误]'.$e->getMessage() ) );

			//关闭数据库连接
			DB::close();

			//退出脚本
		//	die();
           return '';
		}

		//如果事务成功 并且数组不为空
		if($is_commit['status'] && !empty($arr_cbinfo)){

			//循环处理
			foreach ($arr_cbinfo as $k => $v) {

				$time=0;

				do{

					$time++;

					$rt = PostJSON($v['url'], $v['content']);	

				}while('Success' !== $rt && $time<3);


				if('Success' == $rt) {
					$rece_content = '回调成功';	
					$status = 1;				
				}else{
					$rece_content = '回调失败';	
					$status = 2;
				}

				//将当前处理完的回调写入数据库并移动数据库
				$arr_cbinfo = DB::query2('CALL `p_tran_callback_order`('.$v['callback_id'].',\''.$rece_content.'\','.$time.','.$status.',@p_out_flag)');

				
						
			}


		}



	}
	


	/**
	 * 处理订单提交到上游通道之后的返回数据
	 */
	static public function deal_commit($result_data, $arroid ,$channel_file_name) {


		//判断返回的数据不为空
		if(!empty($result_data)) {
			
			//循环处理
			foreach ($result_data as $k => $v) {

				write_debug_log( array( '[循环处理通道返回信息]','order_id:'.$v['order_id'].',order_status:'.$v['order_status'] ) );

				//当订单状态为成功的时候
				if($v['order_status']==2 || $v['order_status']==5) {

					write_debug_log( array( '[准备进入成功事务处理]','order_id:'.$v['order_id'].',order_status:'.$v['order_status'] ) );
					
					//成功订单操作
					Op::do_order_success($v);

					write_debug_log( array( '[处理完成成功事务处理]','order_id:'.$v['order_id'] ) );


				//当订单状态为失败的时候
				} else if(($v['order_status'] == 3 && $v['old_order_status'] !== '3') or $v['order_status'] == 6){

					write_debug_log( array( '[准备进入失败事务处理]','order_id:'.$v['order_id'].',order_status:'.$v['order_status'] ) );
					
					//失败订单操作
					Op::do_order_fail($v);

					write_debug_log( array( '[处理完成失败事务处理]','order_id:'.$v['order_id'] ) );


				//当订单状态为1,4的  修改订单状态 以及解锁数据
				} else if($v['order_status'] == 1 or $v['order_status'] == 4){

					write_debug_log( array( '[准备进入等待回调处理]','order_id:'.$v['order_id'].',order_status:'.$v['order_status'] ) );

					$setArr = array(
							'order_status'			=> 	$v['order_status'],
							'back_content'			=> 	$v['back_content'],
							'is_using'				=> 	0,
					);

					if($v['channel_order_code']){
						$setArr['channel_order_code'] = $v['channel_order_code'];
					}

					try{

						DB::update(DB_PREFIX.'order_pre', $setArr, array('order_id'=>$v['order_id']));

						write_debug_log( array( '[准备进入等待回调处理完成]','order_id:'.$v['order_id'].',order_status:'.$v['order_status'] ) );

					}catch(Exception $e){
					    sleep(1);
                        DB::update(DB_PREFIX.'order_pre', $setArr, array('order_id'=>$v['order_id']));

						write_debug_log( array( '[准备进入等待回调处理失败原因]'.$e->getMessage(),'order_id:'.$v['order_id'].',order_status:'.$v['order_status'] ) );
						write_error_log( array( '[通道文件返回的数据为空]','通道文件名：'.$channel_file_name ) );



					}
				}
			}
		}else{

			write_debug_log( array( '[通道文件返回的数据为空]','通道文件名：'.$channel_file_name ) );
	
		}

		write_debug_log( array( '[流程结束][订单ID]',$arroid,'[通道文件名]'.$channel_file_name ) );

	}
	


	/**
	 * 处理主动从上游通道查询返回的数据
	 */
	static public function deal_query($result_data , $arroid) {

		write_debug_log(array('处理查询的结果=>',$result_data));//调试代码

		//比对返回回来的数据与所有请求时数据的ID数组
		if(count($result_data)<count($arroid)) {
			$missoid = array();
			foreach ($arroid as $oid) {
				//判断返回来的数据是否都在所有id中
				if(!isset($result_data[$oid])) {
					$missoid[] = $oid;

				}
			}
		}

		if(!empty($result_data) && is_array($result_data)){
			foreach ($result_data as $k => $v) {

				if(in_array($v['order_status'], array(2, 5))) { //成功订单的处理
					self::do_order_success($v);
				} else if($v['order_status'] == 6 or $v['order_status'] == 3) { //失败订单的处理
					self::do_order_fail($v);
				} else{
					$missoid[] = $v['order_id'];
				}
			}
		}
		
		if(!empty($missoid)){
			$sql = "UPDATE t_flow_order_pre SET is_using = 0 WHERE order_id IN (".implode(',', $missoid).")";
			$r = DB::getDB()->exec($sql);
			write_debug_log(array('丢失查询信息或者没有查询到结果的的id序列=>',$missoid));//调试代码
		}
		

	}
	

	/**
	 * 处理单个订单成功之后的操作（更新订单状态、更新订单回调、更新账户表、增加账户流水记录）
	 */
	static public function do_order_success($order_info) {

		write_debug_log(array('[执行成功事务开始]','order_id:'.$order_info['order_id'].',order_status:'.$order_info['order_status']));

		$old_order_status = isset($order_info['old_order_status']) ? $order_info['old_order_status'] : $order_info['order_status'] - 1 ;
write_debug_log(array('成功订单old_order_status=>',$old_order_status));

		//订单ID
		$order_id = $order_info['order_id'];
         if(empty($order_info['orderno_id'])){
$order_info['orderno_id'] = '';
          }
		//回调内容
		$content =  str_replace('\\','\\\\',json_encode(array('respCode'=>'0002','respMsg'=>'充值成功','orderID'=>$order_info['order_code'],'phoneNo'=>$order_info['mobile'],'orderno_ID'=>$order_info['orderno_id'])));
		
		//订单结果
		$back_content = str_replace("'","",$order_info['back_content']);

		$back_content = str_replace('"', '', $back_content);

		//最终结果
		$back_fail_desc = '充值成功';

		//订单备注
		$remark = '订单成功';

		//上游订单号
		$channel_order_code = $order_info['channel_order_code'];

		try{
			$sql = 'CALL p_tran_commit_order('.$order_id.','.$old_order_status.',\''.$content.'\',\''.$back_content.'\',\''.$back_fail_desc.'\',\''.$remark.'\',\''.$channel_order_code.'\',@p_out_flag)';

			write_debug_log(array('[事务成功SQL]'.$sql));

			//调用存储过程
			DB::query2($sql);

			//判断是否执行
			$is_commit = DB::query1('select @p_out_flag as status');

		}catch(Exception $e){

			sleep(1);
			DB::query2($sql);
			//数据库错误再次执行
			$is_commit = DB::query1('select @p_out_flag as status');

			write_debug_log(array('[数据库错误][执行成功事务失败原因]'.$e->getMessage(),'order_id:'.$order_info['order_id']));
			write_error_log(array('[数据库错误][执行成功事务失败原因]'.$e->getMessage(),'order_id:'.$order_info['order_id']));
			if($is_commit['status']!==1){
            return '';
			}
			
		}
		
		//判断存储过程是否正确执行，进行日志记录
		if($is_commit['status']==1){

			write_debug_log(array('[成功事务是否执行情况][成功]','order_id:'.$order_info['order_id']));

		}else{

			write_debug_log(array('[成功事务执行情况][失败]'.$is_commit['status'],'=>order_id:'.$order_info['order_id'].',order_status:'.$order_info['order_status'].',old_order_status:'.$old_order_status));
			write_error_log(array('[成功事务执行情况][失败]','order_id:'.$order_info['order_id'].',order_status:'.$order_info['order_status'].',old_order_status:'.$old_order_status));

		}

	}
	


	/**
	 * 处理单个订单失败之后的操作（更新订单状态、更新订单回调、更新账户表）
	 */
	static public function do_order_fail($order_info) {

		write_debug_log(array('[执行失败事务开始]','order_id:'.$order_info['order_id'].',order_status:'.$order_info['order_status']));

        $old_order_status = isset($order_info['old_order_status']) ? $order_info['old_order_status'] : $order_info['order_status'] - 2 ;
write_debug_log(array('失败订单old_order_status=>',$old_order_status));
        
		//订单ID
		$order_id = $order_info['order_id'];
         if(empty($order_info['orderno_id'])){
$order_info['orderno_id'] = '';
          }
		//回调内容
		$content =  str_replace('\\','\\\\',json_encode(array('respCode'=>'0003','respMsg'=>'充值失败','orderID'=>$order_info['order_code'],'phoneNo'=>$order_info['mobile'],'orderno_ID'=>$order_info['orderno_id'])));
		
		//订单结果
		$back_content = str_replace("'","",$order_info['back_content']);

		$back_content = str_replace('"', '', $back_content);

		//最终结果
		$back_fail_desc = '充值失败';

		//订单备注
		$remark = '订单失败自动退款';

		//上游订单号
		$channel_order_code = $order_info['channel_order_code'];

		try{

			$sql = 'CALL p_tran_fail_order('.$order_id.','.$old_order_status.',\''.$content.'\',\''.$back_content.'\',\''.$back_fail_desc.'\',\''.$remark.'\',\''.$channel_order_code.'\',@p_out_flag)';
		
			write_debug_log(array('[事务失败SQL]'.$sql));

			//执行存储过程
			DB::query2($sql);

			//判断是否执行
			$is_commit = DB::query1('select @p_out_flag as status');

		}catch(Exception $e){
            sleep(1);
            DB::query2($sql);
			//数据库错误再次执行
			$is_commit = DB::query1('select @p_out_flag as status');
			write_debug_log(array('[数据库错误][执行失败事务失败原因]'.$e->getMessage(),'order_id:'.$order_info['order_id']));
			write_error_log(array('[数据库错误][执行失败事务失败原因]'.$e->getMessage(),'order_id:'.$order_info['order_id']));
			if($is_commit['status']!==1){
            return '';
			}

		}
		

		write_debug_log(array('失败事务是否执行',$is_commit['status'],$order_info['order_id']));

		//判断存储过程是否正确执行，进行日志记录
		if($is_commit['status'] == 1 ){

			write_debug_log(array('[失败事务执行情况][成功]','order_id:'.$order_info['order_id']));

		}else{

			write_debug_log(array('[失败事务执行情况][失败]'.$is_commit['status'],'=>order_id:'.$order_info['order_id'].',order_status:'.$order_info['order_status'].',old_order_status:'.$old_order_status));
			write_error_log(array('[失败事务执行情况][失败]','order_id:'.$order_info['order_id'].',order_status:'.$order_info['order_status'].',old_order_status:'.$old_order_status));
		}

	}
	
	
	
	
	
}

