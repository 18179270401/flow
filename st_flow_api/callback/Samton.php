<?php
#加载核心文件
require_once dirname(dirname(__FILE__)).'/core.php';

#获取JSON数据流
$json = file_get_contents("php://input");

#记录日志
write_debug_log(array('[尚通科技回调信息]',$json));

#JSON转为数组
$post = json_decode($json,true);

#获取参数
$order_Status 			= 	$post['respCode'];
$order_code 			= 	$post['orderno_ID'];
$channel_order_code  	= 	$post['orderID'];
$msg 					= 	$post['respMsg'];

#如果参数不为空的话
if(!empty($post)){

	$order_info = DB::select(DB_PREFIX.'order_pre', array('channel_order_code'=>$channel_order_code,'order_code'=>$order_code), '*', 0);

	if(!empty($order_info)) {

		if(in_array($order_info['order_status'], array(1, 4))) {

			#执行成功函数
			if($order_Status == '0002') { 
				$order_info['order_status']			+= 1;
				$order_info['back_content']			.= '->尚通科技：充值成功(回调) '.date('Y-m-d H:i:s');
				Op::do_order_success($order_info);

			#执行失败函数
			} elseif($order_Status == '0003') {

				$order_info['order_status']			+= 2;
				$order_info['back_content']			.=  '->尚通科技：充值失败(回调)；原因：'.$msg.' '.date('Y-m-d H:i:s');
				Op::do_order_fail($order_info);

			}
		}
	} 
}

#反馈信息
echo 'Success';







