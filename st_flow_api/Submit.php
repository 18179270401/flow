<?php

ini_set("display_errors", "Off");
//error_reporting(E_ALL | E_STRICT);

require_once dirname(__FILE__).'/core.php'; //核心文件

$ret = array(
		'respCode'	=> '8000',
		'respMsg'	=> '其他错误,请联系工程师跟进!',
		'orderID'	=> '',
);


//订单提交
$post = $_POST;
$account 			= trim($post['account']);
$action 			= trim($post['action']);
$phone 				= trim($post['phone']);
$range      		= (trim($post['range'])) ? trim($post['range']) : 0 ;
$size       		= intval(trim($post['size']));
$timeStamp  		= trim($post['timeStamp']);
$sign 				= trim($post['sign']);
$take_effect_time 	= trim($post['take_effect_time']);
$source_type 		= trim($post['source_type']);
$orderno_id         = trim($post['orderNo']);
$callbackurl        = urldecode(trim($post['callbackurl']));



if(empty($account) || $action!='Charge' || !isMobile($phone) || empty($size) || !in_array($range, array(0,1)) || empty($timeStamp) || empty($sign)) {
	$ret['respCode'] = '3008';
	$ret['respMsg'] = '参数值非法';
write_error_log(array(__FILE__.':'.__LINE__, $ret, 'POST参数：'.json_encode($post).'GET参数：'.json_encode($_GET),get_client_ip(0,true)));
	echo json_encode($ret);
	exit;
}

$take_effect_time 	= !empty($take_effect_time) ? $take_effect_time : 1; //生效时间  1立即 2下月
$source_type 		= !empty($source_type) ? $source_type : 1; //来源类型 1接口 2平台 3网站

$api_info = DB::select(DB_PREFIX.'sys_api', array('api_account'=>$account), '*', 0);
if(empty($api_info) || !in_array($api_info['user_type'], array(1,2))) {
	$ret['respCode'] = '1000';
	$ret['respMsg'] = '用户不存在';
	write_error_log(array(__FILE__, __LINE__, $ret, $account, $api_info));
	echo json_encode($ret);
	exit;
}

$api_key = $api_info['api_key'];

$phone_info = CheckMobile($phone); //检查手机号
if ($phone_info['status'] == 'error') {
	$ret['respCode'] = '3002';
	$ret['respMsg'] = '无效手机号';
	write_error_log(array(__FILE__, __LINE__, $ret, $phone_info, $phone));
	echo json_encode($ret);
	exit;
}


$access_ip = get_client_ip(0,true);

if(!empty($api_info['api_callback_ip']) && '255.255.255.255'!=$api_info['api_callback_ip'] && !in_array($access_ip, explode(',', $api_info['api_callback_ip']))) {
	$ret['respCode'] = '1001';
	$ret['respMsg'] = 'IP鉴权失败';
	write_error_log(array(__FILE__, __LINE__, $ret, $access_ip));
	echo json_encode($ret);
	exit;
}


$pre_str = "{$api_key}account={$account}&action=Charge&phone={$phone}&range={$range}&size={$size}&timeStamp={$timeStamp}{$api_key}";
$pre_sign = md5($pre_str);
if($sign != $pre_sign) {
	$ret['respCode'] = '1002';
	$ret['respMsg'] = '签名校验失败';
	write_error_log(array(__FILE__, __LINE__, $ret, $pre_str, $post));
	echo json_encode($ret);
	exit;
}



//检查充值区域
if($range == 0) {
	//$where_packet['province_id'] = 1; //全国
} else if($range == 1) {
	//$where_packet['province_id'] =  $phone_info['province_id'];
} else {
	$ret['respCode'] = '3003';
	$ret['respMsg'] = '无效区域代号';
	write_error_log(array(__FILE__, __LINE__, $ret, $range));
	echo json_encode($ret);
	exit;
}

//检查流量包大小
if(!in_array($size, array(10,100,300,1024,11264,150,20,200,2048,30,3072,4096,5,50,500,6144,70,700,400,7168,800))) {
	$ret['respCode'] = '3004';
	$ret['respMsg'] = '无效流量包大小';
	write_error_log(array(__FILE__, __LINE__, $ret, $size));
	echo json_encode($ret);
	exit;
} 

$user_info = Op::get_user_info($api_info['user_type'], $api_info['proxy_id'], $api_info['enterprise_id']);
if(empty($user_info)) {
	$ret['respCode'] = '1000';
	$ret['respMsg'] = '用户不存在';
	write_error_log(array(__FILE__, __LINE__, $ret, $api_info));
	echo json_encode($ret);
	exit;
}

if( false === strpos($user_info['operator'], strval($phone_info['operator_id']))) {
	$ret['respCode'] = '3007';
	$ret['respMsg'] = '用户无权限给此运营商的手机号充流量';
	write_error_log(array(__FILE__, __LINE__, $ret, $user_info, $phone_info));
	echo json_encode($ret);
	exit;
}

if(!empty($callbackurl)){

$cburl = $callbackurl;

}else{
$cburl = $api_info['api_callback_address'];	
}

	
	$take_effect_time = (1==$take_effect_time) ? date('Y-m-d H:i:s') : date('Y-m-01 00:00:00', strtotime('+1 month'));

	$order_code = apply_number($phone,6);
	
	// write_error_log(array('用户的proxy_id:'.$api_info['proxy_id'].',enterprise_id:'.$api_info['enterprise_id']));
	
	if(!isset($api_info['enterprise_id']) or !$api_info['enterprise_id'] or $api_info['enterprise_id'] == '' or $api_info['enterprise_id'] == null){
		$api_info['enterprise_id'] = 0;
	}
	if(!isset($api_info['proxy_id']) or !$api_info['proxy_id'] or $api_info['proxy_id'] == '' or $api_info['proxy_id'] == null){
		$api_info['proxy_id'] = 0;
	}

	$sql = 'CALL p_tran_create_order('.$size.','.$phone_info['operator_id'].','
		.$phone_info['province_id'].','.$phone_info['city_id'].','.$api_info['user_type'].','.$api_info['proxy_id'].','
		.$api_info['enterprise_id'].','.$range.',\'\',\''.$order_code.'\',\''.$phone.'\',\''
		.$take_effect_time.'\','.$source_type.',\''.$orderno_id.'\',\''.$cburl.'\',\'\',@p_out_flag)';
		
	//echo $sql;
	write_debug_log(array('[下单执行的SQL]'.$sql));

try{
	

	//调用存储过程
	DB::query2($sql);

	//判断是否执行
	$is_commit = DB::query1('select @p_out_flag as status');

	switch($is_commit['status']){
		case 0 :

			$ret['respCode'] = '8000';
			$ret['respMsg'] = '其他错误,请联系工程师跟进!';

		break;

		case -1:

			$ret['respCode'] = '3005';
			$ret['respMsg'] = '无法找到相应的产品';

		break;

		case -2:

			$ret['respCode'] = '4000';
			$ret['respMsg'] = '余额不足';

		break;

		case -3:

			$ret['respCode'] = '3006';
			$ret['respMsg'] = '此号码为平台黑名单';

		break;

		case -4:

			$ret['respCode'] = '3009';
			$ret['respMsg'] = '请勿重复提交订单';

		break;


        case -8:

            $ret['respCode'] = '4003';
            $ret['respMsg'] = '号码充值失败次数过多, 请暂时不要再试';

        break;

		case 1:

			$ret['respCode']	= '0000';
			$ret['respMsg']		= '下单成功';
			$ret['orderID']		=  $order_code;

		break;


	}

	write_debug_log( array('[下单情况]'.$access_ip.'[用户]'.$api_info['proxy_id'].'-'.$api_info['enterprise_id'].'[号码]'.$phone.'[状态]'.$is_commit['status'].'[信息]'.$ret['respMsg'].'[回调]'.$cburl ) );

	echo json_encode($ret);

	//断掉数据库链接
	DB::close();

	//退出
	exit;


}catch(Exception $e){

	//写入日志
	write_debug_log(array('[数据库错误]'.$e->getMessage() ));

	//返回错误信息
	echo json_encode($ret);

	//断掉数据库链接
	DB::close();

	//退出
	exit;
	

}


