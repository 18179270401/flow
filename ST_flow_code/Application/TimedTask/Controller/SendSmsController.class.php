<?php
namespace TimedTask\Controller;
use Think\Controller;

class SendSmsController extends Controller {

	/*
	 * 读取通道账户与通道额度不足的信息发送提醒
	 */
	public function get_send_sms() {
		set_time_limit(0);
        $cond = array(
            'send_state'    => array('eq', 0),
            'order_time'    => array('elt', date('Y-m-d H:i:s')),
            'send_times'    => array('lt', 1),
        );
		$list = M('sms_send')->where($cond)->select();
        if(!empty($list) && is_array($list)) {
            foreach($list as $k => $v) {
                $mobile = $v['mobile'];
                $content = $v['msg_content'];
                $rt = send_sms($mobile, $content);
                $upd = array(
                    'send_time'         => date('Y-m-d H:i:s'),
                    'modify_date'       => date('Y-m-d H:i:s'),
                );
                if($rt <= 0) {
                    $upd['send_times']  = array('exp', 'send_times+1');
                    M('sms_send')->where("send_id={$v['send_id']}")->save($upd);
                    write_error_log(array(__METHOD__.':'.__LINE__, '短信发送失败，错误编号：'.$rt, $v));
                } else {
                    $upd['send_times'] = array('exp', 'send_times+1');
                    M('sms_send')->where("send_id={$v['send_id']}")->save($upd);
                }
            }
        }
	}

}