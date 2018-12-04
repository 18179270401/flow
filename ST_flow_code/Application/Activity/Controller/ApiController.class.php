<?php
/*
 * 流量活动下单控制器
 *
 */
namespace Activity\Controller;
use Think\Controller;
class ApiController extends Controller {

    function __construct() {
        parent::__construct();
		$this->hostIp = gethostwithhttp();
		//$this->hostIp = 'http://1028fd24.ngrok.io';
    }
	public function test()
	{
		$role = "/Application/Activity/View/Active/RoundAboutModule/";
//				$this -> display("Active/RoundAboutModule/index");
		$this->assign('role',$role);
		$this->assign('rt','{data:[10,20,30,100,1024]}');
		$this -> display("Active/RoundAboutModule/index");
	}
	
	//随机领红包
	public function flow_recharge() {
		$status = "error";
		$msg = "系统错误";
		$user_type = trim(I("user_type"));
		$user_id = trim(I("user_id"));
		$user_activity_id = trim(I("user_activity_id"));
		if (empty($user_type)) {
			$this -> ReturnJson($status, "参数错误", "");
		}
		if ($user_type == 1) {
			$proxy_id = $user_id;
			$map['proxy_id'] = $user_id;
		} else {
			$enterprise_id = $user_id;
			$map['enterprise_id'] = $user_id;
		}
		$activity_id = I("activity_id");
		$openid = I('openid');
		if (empty($activity_id)) {
			$msg = "yw";
			$this -> ReturnJson($status, $msg, "活动信息失效");
		}
//		if (empty($openid)) {
//			$openid = cookie("openid");
//			if (empty($openid)) {
//				$msg = "yw";
//				$this -> ReturnJson($status, $msg, "微信认证信息失效");
//			}
//		}
		$map['activity_id'] = $activity_id;
		$map['activity_status'] = 1;
		$map['user_activity_id'] = $user_activity_id;

		$sua = M("scene_user_activity") -> where($map) -> find();
		
		//读出活动的而信息
		$newtime = date("Y-m-d H:i:s", time());
		if ($newtime > $sua['end_date']) {
			$msg = "yw";
			$this -> ReturnJson($status, $msg, "结束时间早于开始时间");
		} elseif ($newtime < $sua['start_date']) {
			$msg = "yw";
			$this -> ReturnJson($status, $msg, "现在时间小于开始时间");
		}
		//$sua['frequency'] 1为每天，2为每周，3为每月，4为开始-结束
		if ($sua['frequency'] == 1) {
			$start_date = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
			$end_date = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1);
		} elseif ($sua['frequency'] == 2) {
			$start_date = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d') - date('w') + 1, date('Y')));
			$end_date = date("Y-m-d H:i:s", mktime(23, 59, 59, date('m'), date('d') - date('w') + 7, date('Y')));
		} elseif ($sua['frequency'] == 3) {
			$start_date = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), 1, date('Y')));
			$end_date = date("Y-m-d H:i:s", mktime(23, 59, 59, date('m'), date('t'), date('Y')));
		} else {
			$start_date = $sua['start_date'];
			$end_date = $sua['end_date'];
		}
		$phone = trim(I("phone"));
		if (!$phone) {
			$msg = '请输入电话号码';
			$this -> ReturnJson($status, $msg);
		}

		$result = CheckMobile($phone);

		$rule = "/^0?(13[0-9]|15[012356789]|18[02356789]|14[57]|17[0-9])[0-9]{8}$/";
		$tag = preg_match($rule, $phone);
		if ($tag == 0) {
			$this -> ReturnJson($status, "失败：手机号码错误！");
		}
		//判断是否为流量券活动
		if($sua['user_activity_type'] == 2){
			$where['receive_time'] = array('between', array(start_time($start_date), end_time($end_date)));
			$where['mobile'] = $phone;
			$where['user_activity_id'] = $sua["user_activity_id"];
			$sr = M("ticket_receive") -> where($where) -> count();

			// if ($sr >= $sua['number']) {
			// 	$msg = "yw";
			// 	$this -> ReturnJson($status, $msg, "剩余数量不够");
			// }
			if ($sr >= 1) {
				$msg = "yw";
				$this -> ReturnJson($status, $msg, "该手机号码已经领取过一次");
			}
//			if ($sr >= $sua['number']) {
//				$msg = "yw";
//				$this -> ReturnJson($status, $msg, "剩余数量不够");
//			}
	
			// //查询最后一条数据
			$lastrecord = M("ticket_receive") -> where($where) -> order("receive_time desc")->find();
			$lastreceive_date = $lastrecord["receive_time"];
			$lasttime = strtotime($lastreceive_date);
	     	$nowtime = time();
			if(($nowtime - $lasttime) < 5)
			{
	     		// //5秒之内再领一次就return
				$msg = $lasttime."nowtime =".$nowtime;
				$this -> ReturnJson($status, $msg, "请求次数过多");
			}
		}else{
			$where['receive_date'] = array('between', array(start_time($start_date), end_time($end_date)));
			$where['openid'] = $openid;
			$where['user_activity_id'] = $sua["user_activity_id"];
			$sr = M("scene_record") -> where($where) -> count();
	
			
			if ($sr >= $sua['number']) {
				$msg = "yw";
				$this -> ReturnJson($status, $msg, "剩余数量不够");
			}
	
			// //查询最后一条数据
			$lastrecord = M("scene_record") -> where($where) -> order("receive_date desc")->find();
			$lastreceive_date = $lastrecord["receive_date"];
			$lasttime = strtotime($lastreceive_date);
	     	$nowtime = time();
			if(($nowtime - $lasttime) < 5)
			{
	     		// //5秒之内再领一次就return
				$msg = $lasttime."nowtime =".$nowtime;
				$this -> ReturnJson($status, $msg, "请求次数过多");
			}
		}
		//获取大转盘流量包大小
		//$array = $this -> get_packet_size_func($phone);
		$array = $this->get_packet_size_config($phone, $user_type, $user_id, $activity_id,$user_activity_id);
		$data = array();
		$probablyValueArr = array();
		$flowprice = array();
		if ($array['status'] == "success") {
			$message = $array['data'];
			$packetCount = count($message);
			if($packetCount >= 4){
				foreach($message as $row){
					$data[] = $row['size'];
					$flowprice[] = $row['price'];
					$probablyValueArr[] = $row['probability'];
					//流量券需要用到的参数
					if($sua['user_activity_type'] == 2){
						$operatorId[] = $row['operator_id'];
						$productId[] = $row['product_id'];
					}
				}
			}else{
				for($index = 0; $index < 4; $index++){
					if($index < $packetCount){
						$row = $message[$index];
						$data[$index] =  $row['size'];
						$flowprice[$index] =  $row['price'];
						$probablyValueArr[$index] = $row['probability'];
						//流量券需要用到的参数
						if($sua['user_activity_type'] == 2){
							$operatorId[$index] = $row['operator_id'];
							$productId[$index] = $row['product_id'];
						}
					}else{
						$row = $message[0];
						$data[$index] =  $row['size'];
						$flowprice[$index] =  $row['price'];
						//流量券需要用到的参数
						if($sua['user_activity_type'] == 2){
							$operatorId[$index] = $row['operator_id'];
							$productId[$index] = $row['product_id'];
						}
						//$probablyValueArr[$index] = $message[0]['probability'];
					}
				}
			}
		} else {
			$this -> ReturnJson($array['status'], $array['msg']);
		}

		$sectionIndex = $this->drawGrand($probablyValueArr);
		if($sectionIndex != -1)
		{
			$size = $data[$sectionIndex];
			$pflowprice = $flowprice[$sectionIndex];
			//流量券需要用到的参数
			if($sua['user_activity_type'] == 2){
				$poperatorId = $operatorId[$sectionIndex];
				$pproductId  = $productId[$sectionIndex];
			}
		}
		else
		{
			$size = 0;
			$pflowprice = 0;
			//流量券需要用到的参数
			if($sua['user_activity_type'] == 2){
				$poperatorId = 0;
				$pproductId  = 0;
			}
		}
		
		//判断是否为流量券活动
		if($sua['user_activity_type'] == 2){
			if($size != 0)
			{
				//插入流量券到数据库
				$flowTicketReceive['operator_id'] = $poperatorId;
				$flowTicketReceive['product_id'] = $pproductId;
				$flowTicketReceive['mobile'] = $phone;
				$flowTicketReceive['receive_time'] = date("Y-m-d H:i:s", time());
				$flowTicketReceive['flowticket_status'] = 0;
				$flowTicketReceive['effective_duration'] = $sua["ticket_effective_duration"];
				$flowTicketReceive['user_activity_id'] = $sua["user_activity_id"];
				$flowTicketReceive['redeem_code'] = $this->createNonceStr();
				M('ticket_receive') -> add($flowTicketReceive);
				
				//计算流量包大小
				if($size >= 1024)
				{
			   		$flowsize = intval($size/1024);
					$flowsize = $flowsize."G";
				}
				else
				{
					$flowsize = $size."M";
				}
				
				$status = "success";
				$msg = "恭喜您领到了".$flowsize;
				$data['orderID'] = $ret['orderID'];
				$data['size'] = $size;
				$this -> ReturnJson($status, $msg, $data);
			}else{
				$flowTicketReceive['product_id'] = 0;
				$flowTicketReceive['flowticket_status'] = 0;
				//插入领取失败的记录
	            $flowTicketReceive['operator_id'] = $poperatorId;
				$flowTicketReceive['mobile'] = $phone;
				$flowTicketReceive['receive_time'] = date("Y-m-d H:i:s", time());
				$flowTicketReceive['user_activity_id'] = $sua["user_activity_id"];
				M('ticket_receive') -> add($flowTicketReceive);
	
				$msg = "您没有领到流量";
				$this -> ReturnJson($status, $msg, "");
			}
		}
		else
		{
			//读出已用金额
			$used_money = $sua["used_money"];
			if(empty($used_money))
				$used_money = 0;
			//活动开启最大的金额
			$activity_money = $sua["activity_money"];
			if($activity_money > 0 && !empty($activity_money))
			{
				$lastmoney = (float)$activity_money - (float)$used_money;
				//剩余钱不够。或者此单的价格不足以抵扣这次活动的剩余价格时
				if($lastmoney < 0 || $lastmoney < $pflowprice)
				{
					//如果已金额大于最大金额时。不再下单。
					$msg = "您没有领到流量";
					$this -> ReturnJson($status, $msg, "");
				}
			}


			$record['user_type'] = $user_type;
			if ($user_type == 1) {
				$record['proxy_id'] = $user_id;
				$record['enterprise_id'] = 0;
			} else {
				$record['enterprise_id'] = $user_id;
				$record['proxy_id'] = 0;
			}
			//查询api
			$sys_api = M("sys_api") -> where($record) -> find();
			//下单
			
			$respinfo = $this->Apiflowsubmit($size,$phone,$sys_api);
			$respCode = $respinfo["respCode"];
			$orderID = $respinfo["orderID"];
			//获取下单后的信息

			$headimgurl = I("headimgurl");
			$nickname = I("nickname");
			if(empty($nickname))
			{
				$nickname = "订阅号";
			}
			
			$record['order_id'] = $orderID;
			$record['user_activity_id'] = $sua["user_activity_id"];
			$record['openid'] = $openid;

			if($size != 0)
			{
				//将价格写入已使用
				(float)$used_money = (float)$used_money + (float)$pflowprice;
				$sua["used_money"] = $used_money;
				//将数据录入数据库
				M("scene_user_activity") -> where($map) -> save($sua);
	
				//计算流量包大小
				if($size >= 1024)
				{
		   			$flowsize = intval($size/1024);
					$flowsize = $flowsize."G";
				}
				else
				{
					$flowsize = $size."M";
				}
	
				//插入领取成功的记录
	            $record['product_name'] = $flowsize;
				$record['receive_date'] = date("Y-m-d H:i:s", time());
				$record['wx_photo'] = $headimgurl;
				$record['wx_name'] = $nickname;
				$record['mobile'] = $phone;
		        M('scene_record') -> add($record);
	
		        //领取提示返回
				$status = "success";
				$msg = "恭喜您领到了".$flowsize;
				$data['orderID'] = $orderID;
				$data['size'] = $size;
	
	
				$this -> ReturnJson($status, $msg, $data);
			}
			else
			{
				//插入领取失败的记录
	            $record['product_name'] = "";
				$record['receive_date'] = date("Y-m-d H:i:s", time());
				$record['wx_photo'] = $headimgurl;
				$record['wx_name'] = $nickname;
				$record['mobile'] = $phone;
		        M('scene_record') -> add($record);
	
				$msg = "您没有领到流量";
				$this -> ReturnJson($status, $msg, "");
			}
		}
		
	}
	private function Apiflowsubmit($size, $phone,$sys_api)
	{
		$submiturl = C("API_SUBMIT");
		$phone = $phone;
		$range = 0;
		//单位 M
		//$account    = 'LKKKUZMO';
		//$api_key    = 'KN0lqsyYbWsB65LCZSo13f2zfZhCPdyW';
		$account = $sys_api['api_account'];
		$api_key = $sys_api['api_key'];
		$timeStamp = time();
		$pd = array('account' => $account, 'action' => 'Charge', 'phone' => $phone, 'range' => $range, 'size' => $size, 'timeStamp' => $timeStamp, );
		$pre_str = "{$api_key}account={$account}&action=Charge&phone={$phone}&range={$range}&size={$size}&timeStamp={$timeStamp}{$api_key}";
		$pd['sign'] = md5($pre_str);
		$rt = https_request($submiturl, $pd);
		$ret = json_decode($rt, true);
		
		$fp = fopen("access_token.json","a");
		fwrite($fp, "orderID = ".$ret['orderID']."respCode =".$ret['respCode']);
		fclose($fp);
		return array("orderID"=>$ret['orderID'],"respCode"=>$ret['respCode']);
	}

    //检查当前流量活动手机号是否已领取流量券
    public function checkNumTicket(){
    	$status = "error";
		$msg = "系统错误";
		$user_type = trim(I("user_type"));
		$user_id = trim(I("user_id"));
		if (empty($user_type)) {
			$this -> ReturnJson($status, "参数错误", "");
		}
		if ($user_type == 1) {
			$proxy_id = $user_id;
			$map['proxy_id'] = $user_id;
		} else {
			$enterprise_id = $user_id;
			$map['enterprise_id'] = $user_id;
		}
		$activity_id = I("activity_id");
		if (empty($activity_id)) {
			$msg = "yw";
			$this -> ReturnJson($status, $msg, "活动信息失效");
		}
		$map['activity_id'] = $activity_id;
		$map['activity_status'] = 1;
		$sua = M("scene_user_activity") -> where($map) -> find();
		//读出活动的而信息
		$newtime = date("Y-m-d H:i:s", time());
		if ($newtime > $sua['end_date']) {
			$msg = "yw";
			$this -> ReturnJson($status, $msg, "现在时间大于结束时间");
		} elseif ($newtime < $sua['start_date']) {
			$msg = "yw";
			$this -> ReturnJson($status, $msg, "现在时间小于开始时间");
		}
		//$sua['frequency'] 1为每天，2为每周，3为每月，4为开始-结束
		if ($sua['frequency'] == 1) {
			$start_date = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
			$end_date = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1);
		} elseif ($sua['frequency'] == 2) {
			$start_date = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d') - date('w') + 1, date('Y')));
			$end_date = date("Y-m-d H:i:s", mktime(23, 59, 59, date('m'), date('d') - date('w') + 7, date('Y')));
		} elseif ($sua['frequency'] == 3) {
			$start_date = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), 1, date('Y')));
			$end_date = date("Y-m-d H:i:s", mktime(23, 59, 59, date('m'), date('t'), date('Y')));
		} else {
			$start_date = $sua['start_date'];
			$end_date = $sua['end_date'];
		}
		
		$flowTicketMap['receive_time'] = array('between', array(start_time($start_date), end_time($end_date)));
		$flowTicketMap['mobile'] = trim(I("phone"));
		$flowTicketMap['user_activity_id'] = $sua["user_activity_id"];
		$count = M("ticket_receive") -> where($flowTicketMap) -> count();
		//领取提示返回
		$status = "success";
		$this -> ReturnJson($status, $msg, $count);
    }

    //生成32位随机码
	private function createNonceStr($length = 32) {
	    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	    $str = "";
	    for ($i = 0; $i < $length; $i++) {
	      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
	    }
	    return $str;
	}

	//概率
	public function drawGrand($probablyValueArr){

		$randValue = rand(1, 100);
		$sectionIndex = -1;
		//var_dump($probablyValueArr);
		//$probablyValueArr = array(97, 1, 1, 1);
		$sectionValueArr = array();
		$sectionValueTmp = 0;

		//echo "randValue = ".$randValue;

		for ($i = 0; $i < count($probablyValueArr); $i++) {

			$sectionValueArr[$i] = $sectionValueTmp + $probablyValueArr[$i];
			$sectionValueTmp = $sectionValueArr[$i];
		}

		for ($j = 0; $j < count($sectionValueArr); $j++) {

			if ($randValue <= $sectionValueArr[$j]) {

				$sectionIndex = $j;
				break;
			}
		}

		//echo "sectionIndex = ".$sectionIndex;
		return $sectionIndex;
	}


	//获取所有活动可用流量包size
	public function get_packet_size() {
		$mobile = trim(I('phone'));
		$user_type = trim(I('user_type'));
		$user_id = trim(I('user_id'));
		$activity_id = trim(I('activity_id'));
		$user_activity_id = trim(I('user_activity_id'));
		//电话号码
		//$array = $this -> get_packet_size_func($mobile);
		//echo "phone=".$mobile." user_type=".$user_type." user_id=".$user_id." activity_id=".$activity_id."\n";
		$array = $this->get_packet_size_config($mobile, $user_type, $user_id, $activity_id,$user_activity_id);
		//echo "packetList info = ".$array;
		//dump($array);
		if($array['status'] == false){
			$this -> ReturnJson($array['status'], $array['msg'], $array['data']);
		}else{
			$message = $array['data'];
			$packetList = array();
			$packetCount = count($message);
			//echo '$packetCount = '.$packetCount.'\n';
			if($packetCount >= 4){
				foreach($message as $row){
					//$size = ($row['size'] >= 1024) ? ($row['size'] / 1024) . "G" : ($row['size']);
					$packetList[] = $row['size'];
				}
			}else{
				for($index = 0; $index < 4; $index++){
					if($index < $packetCount){
						$row = $message[$index];
						$packetList[$index] = $row['size'];
					}else{
						$row = $message[0];
						$packetList[$index] = $row['size'];
					}
				}
			}

//			echo '<pre>';
			//var_dump($packetList);
//			echo '</pre>';
			$this -> ReturnJson($array['status'], $array['msg'], $packetList);
		}
		//$this -> ReturnJson($array['status'], $array['msg'], $array['data']);
	}

	protected function get_packet_size_config($mobile, $user_type, $user_id, $activity_id,$user_activity_id){
		$rule = "/^0?(13[0-9]|15[012356789]|18[02356789]|14[57]|17[0-9])[0-9]{8}$/";
		$tag = preg_match($rule, $mobile);
		if ($tag == 0) {

			$array = array('status' => false, 'msg' => "失败：手机号码错误！");
			return $array;
		}
		$result = CheckMobile($mobile);


		if ($user_type == 1) {
			//$proxy_id = $user_id;
			$map['sc.proxy_id'] = $user_id;
			$where['proxy_id'] = $user_id;
		} else {
			//$enterprise_id = $user_id;
			$map['sc.enterprise_id'] = $user_id;
			$where['enterprise_id'] = $user_id;
		}

		$where['user_activity_id'] = $user_activity_id;
		$where['activity_id'] = $activity_id;
		$activity = M('scene_user_activity')->where($where)->find();

//		echo "about user_activity_id = ".$activity['user_activity_id'];

		$map['sc.user_activity_id'] = $activity['user_activity_id'];
		$map['sc.operator_id'] = $result['operator_id'];

		$flowSceneConfigTable = M('scene_configuration as sc');

		/*$join = array(
			//C('DB_PREFIX').'product as p ON p.product_id=sc.product_id',
            //C('DB_PREFIX').'channel as c ON p.channel_id=c.channel_id',
            C('DB_PREFIX').'sys_operator as o ON sc.operator_id=o.operator_id',
            //C('DB_PREFIX').'channel_product as cp on p.product_name= cp.product_name
            //	and p.operator_id = cp.operator_id and p.province_id = cp.province_id'
        );*/

		$flowSceneConfigTable->join("inner join ".C('DB_PREFIX')."sys_operator o on sc.operator_id = o.operator_id");


		//$flowProductConfigList = $flowSceneConfigTable->where($map)->join($join,"left")->field("sc.*, o.operator_name, cp.size")->select();
		$flowProductConfigList = $flowSceneConfigTable->where($map)->field("sc.*,o.operator_name")->select();
		if(!empty($flowProductConfigList)&&is_array($flowProductConfigList)) {
			foreach($flowProductConfigList as $k => &$v){
				$product_id = $v['product_id'];
				
				
				$v['size'] = D('ChannelProduct')->get_size_by_pid($product_id,$user_id);
				$v['price'] = D('ChannelProduct')->get_price_by_pid($product_id,$user_id);
			}
		}
		usort($flowProductConfigList, function($a, $b) {
	        $al = $a['size'];
	        $bl = $b['size'];
	        if ($al == $bl)
	         	return 0;
	        return ($al > $bl) ? 1 : -1;  //降序
	    });

//		dump($flowProductConfigList);
		$array = array('status' => "success", 'msg' => "", 'data' => $flowProductConfigList);
		return $array;
	}

	//返回值
	protected function ReturnJson($status, $msg = '', $data = '') {
		$status = $status == "success" ? 1 : 0;
		$array = array('status' => $status, 'msg' => $msg, 'data' => $data, );

		$this -> ajaxReturn($array);
	}
}
