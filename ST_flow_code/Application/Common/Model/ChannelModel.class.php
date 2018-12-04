<?php

namespace Common\Model;
use Think\Model;

class ChannelModel extends Model{

    public function channelinfo($channel_id){
		$where['channel_id']=$channel_id;
		$info = M('Channel')->where($where)->find();
		if($info['status'] == 2){
			return '';
		}else{
			return $info;
		}
	}
    
    public function channelall(){
        $where['status'] = 1;
		$infoall = D('Channel')->where($where)->order("channel_id desc")->select();
		if(!$infoall){
			return '';
		}else{
			return $infoall;
		}
	}
	
	/**
	 * 获取通道省份
	 */
	public function get_channel_province_byid($channel_province_id) {
		$ret = array();
		if(is_numeric($channel_province_id) && $channel_province_id > 0) {
			$model = M('channel_province as cp');
			$model->join("left join ".C('DB_PREFIX')."channel c on cp.channel_id = c.channel_id");
			$model->join("left join ".C('DB_PREFIX')."sys_province sp on cp.province_id = sp.province_id");
			$model->join("left join ".C('DB_PREFIX')."sys_city sc on cp.city_id = sc.city_id");
			$model->field('cp.*,c.channel_name,sp.province_name,sc.city_name');
			$model->where("channel_province_id={$channel_province_id}");
			$ret = $model->find();
			//$ret = M('channel_province')->where("channel_province_id={$channel_province_id}")->find();
		}
		return $ret;
	}



	/**
	 * 编辑通道短信发送信息将指定通道账户信息调整为过期
	 * 主要将当天发送过的信息调为过期，可在次发送
	 * @$channelaccount  通道账户ID
	 */
	public function delete_channel_account_send($channelaccount){
		//读取当前通道账户的提醒额度
		$channel_account = M("channel_account")->where(array('account_id'=>$channelaccount))->field('quota_remind,account_name')->find();
		if(empty($channel_account['quota_remind']) || $channel_account['quota_remind'] <=0){
			$quota_remind = C('CHANNEL_ACCOUNT_MINIMUM_AMOUNT');
		}else{
			$quota_remind = $channel_account['quota_remind'];
		}
		//读取默认提醒内容
		$content_all = C('CHANNEL_ACCOUNT_CONTENT');
		//短信内容
		$content_mobile = str_replace("####","(".$channel_account['account_name'].")",$content_all);
		$content_mobile = str_replace("$$$$",$quota_remind,$content_mobile);
		//调用修改方法
		return $this->ecit_sms_send($content_mobile,C('CHANNEL_ACCOUNT_MSG_TYPE'));
	}

	/**
	 * 编辑通道短信发送信息将指定通道额度信息调整为过期
	 * 主要将当天发送过的信息调为过期，可在次发送
	 * @$channelprovince  通道额度ID
	 */
	public function delete_channel_province_send($channelprovince){
		//读取当前通道额度的提醒额度
		$join = array(
			"INNER JOIN ".C('DB_PREFIX').'channel as c ON cp.channel_id=c.channel_id',
			"INNER JOIN ".C('DB_PREFIX').'sys_province as sp ON cp.province_id=sp.province_id'
		);
		$channel_province = M("channel_province as cp")->join($join)->where(array('cp.channel_province_id'=>$channelprovince))->field('c.channel_name,sp.province_name,cp.quota_remind')->find();
		if(empty($channel_province['quota_remind']) || $channel_province['quota_remind'] <=0){
			$quota_remind = C('CHANNEL_PROVINCE_MINIMUM_AMOUNT');
		}else{
			$quota_remind = $channel_province['quota_remind'];
		}
		//读取默认提醒内容
		$content_all = C('CHANNEL_PROVINCE_CONTENT');
		//短信内容
		$content_mobile = str_replace("####","(".$channel_province['channel_name'].")",$content_all);
		$content_mobile = str_replace("****","(".$channel_province['province_name'].")",$content_mobile);
		$content_mobile = str_replace("$$$$",$quota_remind,$content_mobile);
		//调用修改方法
		return $this->ecit_sms_send($content_mobile,C('CHANNEL_PROVINCE_MSG_TYPE'));
	}

	/**
	 * 编辑代理商或企业短信发送信息将指定信息调整为过期
	 * 主要将当天发送过的信息调为过期，可在次发送
	 * @$type  用户类型(2=>代理商、3=>企业)
	 * @$id    用户ID
	 */
	public function delete_proxy_enterprise_send($type,$id){
		if($type==3){
			$where['enterprise_id'] = $id;
			$table_name = 'enterprise';
		}else{
			$where['proxy_id'] = $id;
			$table_name = 'proxy';
		}
		$list = M($table_name)->where($where)->field('contact_tel')->find();
		return $this->ecit_sms_send($list['contact_tel'],C('USER_SEND_MSG_TYPE'),'mobile');
	}

	/**
	 * 修改发送信息的内容状态
	 * @$content_mobile   指定要修改的内容
	 */
	private function ecit_sms_send($msg_content,$type,$table_name = 'msg_content'){
		$start_time = start_time(date("Y-m-d H:i:s"),time());	//当天开始时间
		$end_time = end_time(date("Y-m-d H:i:s"),time());		//当天结束时间
		$where['msg_type'] = $type;
		$where[$table_name] = $msg_content;
		$where['order_time'] = array("between", array($start_time, $end_time));
		//读取信息
		$list = M('sms_send')->where($where)->field('send_id')->select();
		if($list){
			//将ID转成一维数组
			$ids = implode(',',get_array_column($list,'send_id'));
			//修改之前发送过的信息，修改为删除状态
			M('sms_send')->where(array('send_id'=>array('in',$ids)))->save(array('delete_status'=>1));
		}
		return true;
	}
	
	
}