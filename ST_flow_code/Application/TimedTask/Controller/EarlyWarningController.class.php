<?php
namespace TimedTask\Controller;
use Think\Controller;
class EarlyWarningController extends Controller {

	/**
	 * 读取通道账户不足的信息发送提醒
	 */
	public function channel_account_warning(){
		set_time_limit(0);
		$send_ids = C('CHANNEL_ACCOUNT_SEND_IDS');				//事务ID号
		$minimum_amount = C('CHANNEL_ACCOUNT_MINIMUM_AMOUNT');	//最小额度
		$send_times = C('CHANNEL_ACCOUNT_SEND_TIMES');			//发送次数
		$start_time = start_time(date("Y-m-d H:i:s"),time());	//当天开始时间
		$end_time = end_time(date("Y-m-d H:i:s"),time());		//当天结束时间
		//读取通道账号不足提醒金额的通道信息
		$sql = "SELECT ca.quota_remind,ca.account_name FROM `t_flow_channel_account` as ca WHERE case when ca.quota_remind is null or ca.quota_remind <= 0.00 then ".$minimum_amount." else ca.quota_remind end > ca.surplus_money;";
		$channelaccount = m()->query($sql);
		if($channelaccount) {
			//调用方法获取发送人的ID号和用户电话号码
			$list_mobile = $this->get_select_mobile($send_ids);
			//读取默认提醒内容
			$content_all = C('CHANNEL_ACCOUNT_CONTENT');
			//循环发送信息到指定接收人
			foreach ($channelaccount as $v) {
				$v['quota_remind'] = $v['quota_remind'] <= 0.00 ? $minimum_amount : $v['quota_remind'];
				//事务内容
				$content = str_replace("####","【".$v['account_name']."】",$content_all);
				$content = str_replace("$$$$","【".$v['quota_remind']."】",$content);
				//短信内容
				$content_mobile = str_replace("####","(".$v['account_name'].")",$content_all);
				$content_mobile = str_replace("$$$$",$v['quota_remind'],$content_mobile);
				//读取当天是否发送过当前通道的事务,没有则发送事务
				if($list_mobile['quota_user']){
					$where['remind_content'] = $content;
					$where['create_date'] = array("between", array($start_time, $end_time));
					if (!M("sys_remind_content")->where($where)->field("content_id")->find()) {
						//未读取到信息在接收人不为空时即添加事务
						R('Admin/ObjectRemind/send_user', array($send_ids, $content, $list_mobile['quota_user']));
					}
				}
				//添加待发送短信
				if ($list_mobile['quota_mobile']) {
					//读取当天当前通道已发送的手机号码
					$where2['msg_type'] = C('CHANNEL_ACCOUNT_MSG_TYPE');
					$where2['msg_content'] = $content_mobile;
					$where2['order_time'] = array("between", array($start_time, $end_time));
					$where2['delete_status'] = 0;
					//判断手机号发送次数是否达到限制
					foreach ($list_mobile['quota_mobile'] as $mobile) {
						$where2['mobile'] = $mobile;
						$send_mobile = M("sms_send")->where($where2)->count();
						if($send_mobile < $send_times){
							$mobile_add['msg_type'] = C('CHANNEL_ACCOUNT_MSG_TYPE');
							$mobile_add['timing'] = 0;
							$mobile_add['order_time'] =date("Y-m-d H:i:s", time());
							$mobile_add['msg_content'] = $content_mobile;
							$mobile_add['mobile'] = $mobile;
							$mobile_add['send_state'] = 0;
							$mobile_add['delete_status'] = 0;
							$send_add[] = $mobile_add;
						}
					}
					if ($send_add) {
						M("sms_send")->addAll($send_add);
						unset($send_add);	//空清添加变量

					}
					unset($send_mobile);	//清空读取变量
				}
			}
			unset($channelaccount, $list_mobile);	//清空通道变量、发送的号码与事务人的ID号
		}
		//调用通道额度不足的提醒方法
		$this->channel_quota_warning();
	}

	/**
	 * 读取通道额度不足的信息发送提醒
	 */
	public function channel_quota_warning(){
		set_time_limit(0);
		$send_ids2 = C('CHANNEL_PROVINCE_SEND_IDS');			//事务ID号
		$minimum_amount = C('CHANNEL_PROVINCE_MINIMUM_AMOUNT');	//最小额度
		$send_times = C('CHANNEL_PROVINCE_SEND_TIMES');			//发送次数
		$start_time2 = start_time(date("Y-m-d H:i:s"),time());	//当天开始时间
		$end_time2 = end_time(date("Y-m-d H:i:s"),time());		//当天结束时间
		//读取通道额度不足提醒额度的通道信息
		$sql2 = "SELECT cp.quota_remind,c.channel_name,sp.province_name FROM `t_flow_channel_province` as cp INNER JOIN `t_flow_channel` as c on cp.channel_id = c.channel_id INNER JOIN `t_flow_sys_province` as sp on sp.province_id = cp.province_id WHERE case when cp.quota_remind is null or cp.quota_remind <= 0.00 then ".$minimum_amount." else cp.quota_remind end > cp.province_money;";
		$channelprovince = m()->query($sql2);
		if($channelprovince){
			//调用方法获取发送人的ID号和用户电话号码
			$list_mobile2 = $this->get_select_mobile($send_ids2);
			//读取默认提醒内容
			$content_all2 = C('CHANNEL_PROVINCE_CONTENT');
			//循环发送信息到指定接收人
			foreach($channelprovince as $v2){
				$v2['quota_remind'] = $v2['quota_remind'] <= 0.00 ? $minimum_amount : $v2['quota_remind'];
				//事务内容
				$content2 = str_replace("####","【".$v2['channel_name']."】",$content_all2);
				$content2 = str_replace("****","【".$v2['province_name']."】",$content2);
				$content2 = str_replace("$$$$","【".$v2['quota_remind']."】",$content2);
				//短信内容
				$content_mobile2 = str_replace("####","(".$v2['channel_name'].")",$content_all2);
				$content_mobile2 = str_replace("****","(".$v2['province_name'].")",$content_mobile2);
				$content_mobile2 = str_replace("$$$$",$v2['quota_remind'],$content_mobile2);
				//读取当天是否发送过当前通道的事务,没有则发送事务
				if($list_mobile2['quota_user']){
					$where['remind_content'] = $content2;
					$where['create_date'] = array("between", array($start_time2, $end_time2));
					if (!M("sys_remind_content")->where($where)->field("content_id")->find()) {
						//未读取到信息在接收人不为空时即添加事务
						R('Admin/ObjectRemind/send_user', array($send_ids2, $content2, $list_mobile2['quota_user']));
					}
				}
				//添加待发送短信
				if ($list_mobile2['quota_mobile']) {
					//读取当天当前通道已发送的手机号码
					$where2['msg_type'] = C('CHANNEL_PROVINCE_MSG_TYPE');
					$where2['msg_content'] = $content_mobile2;
					$where2['order_time'] = array("between", array($start_time2, $end_time2));
					$where2['delete_status'] = 0;

					foreach ($list_mobile2['quota_mobile'] as $mobile) {
						$where2['mobile'] = $mobile;
						$send_mobile = M("sms_send")->where($where2)->count();
						if ($send_mobile < $send_times) {
							$mobile_add['msg_type'] = C('CHANNEL_PROVINCE_MSG_TYPE');
							$mobile_add['timing'] = 0;
							$mobile_add['order_time'] =date("Y-m-d H:i:s", time());
							$mobile_add['msg_content'] = $content_mobile2;
							$mobile_add['mobile'] = $mobile;
							$mobile_add['send_state'] = 0;
							$mobile_add['delete_status'] = 0;
							$send_add[] = $mobile_add;
						}
					}
					if ($send_add) {
						M("sms_send")->addAll($send_add);
						unset($send_add);	//空清添加变量
					}
					unset($send_mobile);	//清空读取变量
				}
			}
			unset($channelprovince, $list_mobile2);	//清空通道变量、发送的号码与事务人的ID号
		}
		//调用理商或企业金额不足提醒功能
		$this->get_company_warning();
	}
	/**
	 * 读取指定事务的提醒人的ID与用户的电话码号
	 */
	private function get_select_mobile($remind_type_id){
		//读取系统设置中额度、余额不足提醒人的电话号码
		$set_mobile = M('sys_set')->order("set_id asc")->field('channel_quota_remind')->find();
		$quota_mobile = array();
		if($set_mobile['channel_quota_remind']!=""){
			$quota_mobile = explode(",",$set_mobile['channel_quota_remind']);
		}
		//读取事务中设置提醒人的电话号码
		$join = C('DB_PREFIX').'sys_user as su ON sru.user_id=su.user_id and su.status=1';
		$where['sru.remind_type_id'] = $remind_type_id;
		$remind_mobile = M("sys_remind_user as sru")->join($join)->where($where)->field("sru.user_id,su.mobile")->select();
		$quota_user = array();
		if($remind_mobile){
			foreach($remind_mobile as $v){
				//讲需要发送事务的人写入数组中
				$quota_user[] = $v['user_id'];
				//将正确的手机号码写入数组中
				if(isMobile2($v['mobile'])){
					$quota_mobile[] = $v['mobile'];
				}
			}
		}
		$data['quota_user'] = $quota_user;
		//去掉重复的电话号码
		$data['quota_mobile'] = array_unique($quota_mobile);
		return $data;
	}

	/**
	 * 读取代理商和企业金额不足需提配的信息
	 */
	public function get_company_warning(){
		set_time_limit(0);
		$pe_open_amount = C('USER_PE_OPEN_AMOUNT');				//开始金额
		$p_minimum_amount = C('USER_P_MINIMUM_AMOUNT');			//代理商最小额度
		$e_minimum_amount = C('USER_E_MINIMUM_AMOUNT');			//企业最小额度
		$send_times = C('USER_SEND_TIMES');						//发送次数
		$start_time3 = start_time(date("Y-m-d H:i:s"),time());	//当天开始时间
		$end_time3 = end_time(date("Y-m-d H:i:s"),time());		//当天结束时间

		//读取代理商和企业的最底提醒金额-公共信息
		$sysset = M("SysSet")->order("set_id asc")->field("proxy_quota_remind as p_quota_remind,enterprise_quota_remind as e_quota_remind")->find();
		//给定默认提醒金额
		$quota['p_quota_remind'] = $sysset['p_quota_remind']>0?$sysset['p_quota_remind']:$p_minimum_amount;
		$quota['e_quota_remind'] = $sysset['e_quota_remind']>0?$sysset['e_quota_remind']:$e_minimum_amount;

		//给出24之间的时间
		/*$time_a = date("Y-m-d H:i:s",time()-86400);
		$time_b = date("Y-m-d H:i:s",time());*/
		$time_b = date("Ymd",time());

			//读取前24小时订单中所包含的代理商ID关联代理商账户获取账户不足提醒的代理商
		$sql_p = "SELECT pa.account_balance,pa.new_quota_remind, p.contact_tel AS mobile, su.proxy_id,su.user_type 
				FROM t_flow_order_user o
				INNER JOIN `t_flow_proxy_account` AS pa ON pa.proxy_id = o.user_id
				INNER JOIN `t_flow_sys_user` AS su ON su.proxy_id = o.user_id AND su.user_type = 2
				INNER JOIN `t_flow_proxy` AS p ON p.proxy_id = o.user_id 
				INNER JOIN `t_flow_proxy` AS tp ON tp.proxy_id = p.top_proxy_id and ( tp.proxy_id = 1 or tp.proxy_type = 1)
				WHERE pa.account_balance BETWEEN $pe_open_amount  AND pa.new_quota_remind AND o.user_type = 1
				AND o.complete_time =$time_b AND su.is_manager = 1 AND p. STATUS = 1 AND p.approve_status = 1
				GROUP BY p.proxy_id";

		//加入代理商联系人开始
		$p_list = m()->query($sql_p);
		$str="";
		foreach($p_list as $v){
			$str=$v['proxy_id'].",";
		}
		if($str != ""){
			$str=substr($str,0,-1);
			$sql_pa="select company_id,tel from t_flow_user_contact where company_id in ($str) and company_type =1 and status=1";
			$cp_list=M()->query($sql_pa);
			if($cp_list){
				foreach ($p_list as $c){
					foreach ($cp_list as $v){
						if($c['proxy_id']=$v['company_id']){
							$c['mobile']=$v['tel'];
							array_push($p_list,$c);
						}
					}
				}
			}
		}
		//加入代理商联系人结束
		//读取前24小时订单中所包含的企业ID关联企业账户获取账户不足提醒的代理商
		$sql_e = "SELECT pa.account_balance,pa.new_quota_remind, e.contact_tel AS mobile, su.enterprise_id,su.user_type 
				FROM t_flow_order_user o
				INNER JOIN `t_flow_enterprise_account` AS pa ON pa.enterprise_id = o.user_id
				INNER JOIN `t_flow_sys_user` AS su ON su.enterprise_id = o.user_id AND su.user_type = 3
				INNER JOIN `t_flow_enterprise` AS e ON e.enterprise_id = o.user_id
				INNER JOIN  `t_flow_proxy`  AS p ON p.proxy_id = e.top_proxy_id and proxy_type = 1 
				WHERE pa.account_balance BETWEEN $pe_open_amount  AND pa.new_quota_remind AND o.user_type = 2
				AND o.complete_time =$time_b AND su.is_manager = 1 AND e. STATUS = 1 AND e.approve_status = 1
				GROUP BY e.enterprise_id";
		$e_list = m()->query($sql_e);
		$str="";
		foreach($e_list as $v){
			$str=$v['enterprise_id'].",";
		}
		if($str != ""){
			$str=substr($str,0,-1);
			$sql_ea="select company_id,tel from t_flow_user_contact where company_id in ($str) and company_type =2 and status=1";
			$ce_list=M()->query($sql_ea);
			if($ce_list){
				foreach ($p_list as $c){
					foreach ($ce_list as $v){
						if($c['enterprise_id']=$v['company_id']){
							$c['mobile']=$v['tel'];
							array_push($e_list,$c);
						}
					}
				}
			}
		}
		//合并两个数组内容
		if($p_list && $e_list){
			$content = array_merge($p_list,$e_list);
		}else{
			$content = $p_list?$p_list:$e_list;
		}
		//注销变量
		unset($p_list,$e_list);
		//添加待发送短信
		if ($content) {
			//读取当天当前通道已发送的手机号码
			$where3['msg_type'] = C('USER_SEND_MSG_TYPE');
			$where3['order_time'] = array("between", array($start_time3, $end_time3));
			$where3['delete_status'] = 0;

			//读取默认提醒信息内容
			$content_all3 = C('USER_SEND_CONTENT');
			foreach ($content as $v) {
				//将电话号码中的横线去掉
				$v['mobile'] = str_replace("-","",$v['mobile']);
				if(isMobile2($v['mobile'])) {
					$where3['mobile'] = $v['mobile'];
					$send_mobile = M("sms_send")->where($where3)->count();
					if ($send_mobile < $send_times) {
						//判断代理商或企业的提醒金额
						//$quota_remind = $v['user_type'] == 2 ? $quota['p_quota_remind'] : $quota['e_quota_remind'];
						//组合内容
						$send_content = str_replace("$$$$$",$v['new_quota_remind'],$content_all3);
						$send_content = str_replace("$$$$",$v['account_balance'],$send_content);

						$mobile_add['msg_type'] = C('USER_SEND_MSG_TYPE');
						$mobile_add['timing'] = 0;
						$mobile_add['order_time'] = date("Y-m-d H:i:s", time());
						$mobile_add['msg_content'] = $send_content;
						$mobile_add['mobile'] = $v['mobile'];
						$mobile_add['send_state'] = 0;
						$mobile_add['delete_status'] = 0;
						$send_add[] = $mobile_add;
						unset($mobile_add);
					}
				}
			}
			if ($send_add) {
				M("sms_send")->addAll($send_add);
				unset($send_add);	//空清添加变量
			}
			unset($send_mobile);	//清空读取变量
		}
		//调用短信发送方法
		$this->get_send_sms();
	}

	/**
	 * 读取通道账户与通道额度不足的信息发送提醒
	 */
	public function get_send_sms() {
		set_time_limit(0);
		$cond = array(
			'send_state'    => array('in', (string)C('SEND_STATE')),
			'order_time'    => array('elt', date('Y-m-d H:i:s')),
			'send_times'    => array('lt', C('SEND_TIMES')),
			'delete_status' => array('in', (string)C('DELETE_STATUS')),
		);
		$list = M('sms_send')->where($cond)->field("send_id,mobile,msg_content")->select();
		if(!empty($list) && is_array($list)) {
			foreach($list as $k => $v) {
				$mobile = $v['mobile'];
				$content = $v['msg_content'];
				$rt = send_sms($mobile, $content);
				$upd = array(
					'send_time'         => date('Y-m-d H:i:s'),
					'modify_date'       => date('Y-m-d H:i:s'),
					'send_times'		=> array('exp', 'send_times+1'),
				);
				M('sms_send')->where("send_id={$v['send_id']}")->save($upd);
				if($rt <= 0) {
					write_error_log(array(__METHOD__.':'.__LINE__, '短信发送失败，错误编号：'.$rt, $v));
				}
			}
		}
	}


}