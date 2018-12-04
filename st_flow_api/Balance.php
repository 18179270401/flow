<?php
//账户余额查询

require_once dirname(__FILE__).'/core.php'; //核心文件

$ret = array(
		'respCode'	=> '8000',
		'respMsg'	=> '其他错误,请联系工程师跟进!',
);

//订单查询
$post 		= $_POST;
$account 	= trim($post['account']);
$action 	= trim($post['action']);
$timeStamp  = trim($post['timeStamp']);
$sign 		= trim($post['sign']);



if(empty($account) || $action!='Balance' || empty($timeStamp) || empty($sign)) {
	$ret['respCode'] = '3008';
	$ret['respMsg'] = '参数值非法';
	write_error_log(array(__FILE__, __LINE__, $ret, $post,get_client_ip(0,true)));
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
/*
if(abs(NOW_TIME - $timeStamp) > 60) { //有效期10分钟
	$ret['respCode'] = '3009';
	$ret['respMsg'] = '请求超时，请重新查询';
	write_error_log(array(__FILE__, __LINE__, $ret, date('Y-m-d H:i:s',$timeStamp)));
	echo json_encode($ret);
	exit;
}
*/
$pre_str = "{$api_key}account={$account}&action=Balance&timeStamp={$timeStamp}{$api_key}";
$pre_sign = md5($pre_str);
if($sign != $pre_sign) {
	$ret['respCode'] = '1002';
	$ret['respMsg'] = '签名校验失败';
	write_error_log(array(__FILE__, __LINE__, $ret, $pre_str, $post));
	echo json_encode($ret);
	exit;
}

$account_info = Op::get_account_info($api_info['user_type'], $api_info['proxy_id'], $api_info['enterprise_id']);
if(empty($account_info)) {
	$ret['respCode'] = '1006';
	$ret['respMsg'] = '您查询的账户不存在';
	write_error_log(array(__FILE__, __LINE__, $ret, $account_info, $api_info));
	echo json_encode($ret);
	exit;
}

$ret['respCode'] = '0004'; //操作成功
$ret['respMsg'] = $account_info['account_balance']; //余额 值
echo json_encode($ret);
exit;

