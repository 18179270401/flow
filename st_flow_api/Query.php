<?php

require_once dirname(__FILE__).'/core.php'; //核心文件

$ret = array(
		'respCode'	=> '8000',
		'respMsg'	=> '其他错误,请联系工程师跟进!',
);

//订单查询
$post 		= $_POST;
$account 	= trim($post['account']);
$action 	= trim($post['action']);
$orderID 	= trim($post['orderID']);
$timeStamp  = trim($post['timeStamp']);
$sign 		= trim($post['sign']);

if(empty($account) || $action!='Query' || empty($timeStamp) || empty($sign) || empty($orderID)) {
	$ret['respCode'] = '3008';
	$ret['respMsg'] = '参数值非法';
	write_error_log(array(__FILE__, __LINE__, $ret, $post));
	echo json_encode($ret);
	exit;
}

$api_info = DB::select(DB_PREFIX.'sys_api', array('api_account'=>$account), '*', 0);
if(empty($api_info) || !in_array($api_info['user_type'], array(1,2))) {
	$ret['respCode'] = '1000';
	$ret['respMsg'] = '用户不存在';
	write_error_log(array(__FILE__, __LINE__, $ret, $account, $api_info));
	echo json_encode($ret);
	exit;
}
$api_key = $api_info['api_key'];

$access_ip = get_client_ip(0,true);
if(!empty($api_info['api_callback_ip']) && '255.255.255.255'!=$access_ip && !in_array($access_ip, explode(',', $api_info['api_callback_ip']))) {
	$ret['respCode'] = '1001';
	$ret['respMsg'] = 'IP鉴权失败';
	write_error_log(array(__FILE__, __LINE__, $ret, $access_ip));
	echo json_encode($ret);
	exit;
}

/* if(abs(NOW_TIME - $timeStamp) > 600) { //有效期10分钟
	$ret['respCode'] = '3009';
	$ret['respMsg'] = '请求超时，请重新查询';
	write_error_log(array(__FILE__, __LINE__, $ret, date('Y-m-d H:i:s',$timeStamp)));
	echo json_encode($ret);
	exit;
} */

$pre_str = "{$api_key}account={$account}&action=Query&orderID={$orderID}&timeStamp={$timeStamp}{$api_key}";
$pre_sign = md5($pre_str);
if($sign != $pre_sign) {
	$ret['respCode'] = '1002';
	$ret['respMsg'] = '签名校验失败';
	write_error_log(array(__FILE__, __LINE__, $ret, $pre_str, $post));
	echo json_encode($ret);
	exit;
}
/*
if($api_info['user_type']==1){
$where =" AND `proxy_id` = {$api_info['proxy_id']}";
}elseif ($api_info['user_type']==2) {
$where =" AND `enterprise_id` = {$api_info['enterprise_id']}";
}

//$order_info = DB::select(DB_PREFIX.'order', array('order_code'=>$orderID), '*', 0);

$order_info_cache = DB::query1("SELECT user_type,proxy_id,enterprise_id,order_status,back_fail_desc FROM `t_flow_order_cache` WHERE (`order_code` = "."'".$orderID."'"." OR `orderno_id` = "."'".$orderID."'".")".$where);

if(!empty($order_info_cache)){
	$order_info=$order_info_cache;
}else{
$order_info_pre = DB::query1("SELECT user_type,proxy_id,enterprise_id,order_status,back_fail_desc FROM `t_flow_order_pre` WHERE (`order_code` = "."'".$orderID."'"." OR `orderno_id` = "."'".$orderID."'".")".$where);	
if(!empty($order_info_pre)){
$order_info=$order_info_pre;	
}else{
$order_info_o = DB::query1("SELECT user_type,proxy_id,enterprise_id,order_status,back_fail_desc FROM `t_flow_order` WHERE (`order_code` = "."'".$orderID."'"." OR `orderno_id` = "."'".$orderID."'".")".$where);
$order_info=$order_info_o;
}	
}

*/

if($api_info['user_type']==1){
$where = $api_info['proxy_id'];
}elseif($api_info['user_type']==2){
$where = $api_info['enterprise_id'];
}

try{

$sql = "CALL p_get_order_status("."'".$orderID."'".",".$api_info['user_type'].",".$where.")";

write_debug_log(array('[查询执行的SQL]'.$sql));

$order_info=DB::query1($sql);

DB::close();

		}catch(Exception $e){

			//记录日志
			write_error_log( array( '[数据库错误]'.$e->getMessage() ) );
			write_debug_log( array( '[数据库错误]'.$e->getMessage() ) );

			//关闭数据库连接
			DB::close();
			echo json_encode($ret);
exit;
		}

if(empty($order_info)){
	$ret['respCode'] = '1003';
	$ret['respMsg'] = '该订单不存在';
	write_error_log(array(__FILE__, __LINE__, $ret, "order_code=={$orderID}"));
	echo json_encode($ret);
	exit;
}

if($api_info['user_type']!=$order_info['user_type'] || $api_info['proxy_id']!=$order_info['proxy_id'] || $api_info['enterprise_id']!=$order_info['enterprise_id']) {
	$ret['respCode'] = '1004';
	$ret['respMsg'] = '您无权查看别人的订单状态';
	write_error_log(array(__FILE__, __LINE__, $ret, $api_info, $order_info));
	echo json_encode($ret);
	exit;
}

if(2==$order_info['order_status'] || 5==$order_info['order_status']) {
	$ret['respCode'] = '0002';
	$ret['respMsg'] = '充值成功';
	write_error_log(array(__FILE__, __LINE__, $ret, "order_code=={$orderID}"));
	echo json_encode($ret);
	exit;
} else if(6==$order_info['order_status']) {
	$ret['respCode'] = '0003';
	$ret['respMsg'] = $order_info['back_fail_desc']; //失败原因描述
	write_error_log(array(__FILE__, __LINE__, $ret, "order_code=={$orderID}"));
	echo json_encode($ret);
	exit;
} else {
	$ret['respCode'] = '0001';
	$ret['respMsg'] = '充值中';
	write_error_log(array(__FILE__, __LINE__, $ret, "order_code=={$orderID}"));
	echo json_encode($ret);
	exit;
}



