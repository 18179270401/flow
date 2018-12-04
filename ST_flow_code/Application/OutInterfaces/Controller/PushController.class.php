<?php
namespace OutInterfaces\Controller;
use Think\Controller;

class PushController extends CommonController {
	private $client;

	public function _initialize(){
		Vendor("JPush.JPush");
		$app_key = C('JG_PUSH_APP_KEY');
		$master_secret = C('JG_PUSH_MASTER_SECRET');
		$this->client = new \Jpush ($app_key, $master_secret);
	}
    /**
	 * 极光推送测试样例
     */
	public function Test() {
		$client = $this->client;
		// 简单推送示例
		$result = $client->push()
			->setPlatform('all')
			->addAllAudience()
			->setNotificationAlert('Hi, JPush')
			->send();

		echo 'Result=' . json_encode($result);
	}

	/**
	 * 未完成记录的订单进行推送提醒
	 *
	 */
	public function push_waiting_order_info(){
		$result['user'] = '';
		$user_id = 1;
		$channel_id = '';
		$bc_channel_id = '';
		$operator_id = -1;
		$province_id = -1;
		$order_status = '';
		$start_date = date('Y-m-d H:i:s',strtotime("-1 hour"));
		$end_date =  date('Y-m-d H:i:s');

		$list2 = D('Order')->order_storing_process(1,$user_id,'','','','',$channel_id,$bc_channel_id,$operator_id,$province_id,$order_status,$start_date,$end_date,'',1,-1);
		$info['wait_count'] = $list2['count']['@p_wait_count']+$list2['count']['@p_faile_count'];
		$info['wait_amount'] = $list2['count']['@p_wait_amount']+$list2['count']['@p_faile_amount'];
		$info['wait_price'] = $list2['count']['@p_wait_price']+$list2['count']['@p_faile_price'];

		$info['submit_success_count'] = $list2['count']['@submit_success_count'];
		$info['submit_success_amount'] = $list2['count']['@submit_success_amount'];
		$info['submit_success_price'] = $list2['count']['@p_submit_success_price'];

		$total_count = $info['wait_count'] + $info['submit_success_count'];

		//如果没有超过阈值，就不需要推送信息
		if($total_count < C('JG_PUSH_THRESHOLD_VALUE')){
			return return_tidy_result($result);
		}

		//获取需要提醒的设备
		$user_name_str = M('sys_set')->order('set_id desc')->field('card_warning_people')->find();
		$user_name_str = $user_name_str['card_warning_people'];
		if(empty($user_name_str)){
			//不做处理
		}elseif($user_name_str == '全部'){
			$result['user'] = 'all';
			$result['jg_push'] =  $this->client->push()
				->setPlatform(array('ios', 'android'))
				->addAllAudience()
				->setNotificationAlert('警报：最近一个小时未完成充值订单量：'.$total_count)
				->setOptions(null,3600,null,null,null)
				->send();

		}else{
			$user_name_str = "'" . str_replace(',', "','", $user_name_str) . "'";
			$jg_where = "user_type=1 and registration_id<>'' and login_name in ($user_name_str)";
			$reg_id_arr = M('sys_user')->field('registration_id')->where($jg_where)->select();

			$reg_id_arr = get_array_column($reg_id_arr, 'registration_id');

			$result['user'] = $reg_id_arr;
			$result['jg_push'] =  $this->client->push()
				->setPlatform(array('ios', 'android'))
				->addRegistrationId($reg_id_arr)
				->setNotificationAlert('警报：最近一个小时未完成充值订单量：'.$total_count)
				->setOptions(null,3600,null,null,null)
				->send();
		}
		return return_tidy_result($result);
	}

}
