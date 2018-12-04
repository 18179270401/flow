<?php
namespace Sdk\Controller;
use Think\Controller;
class ApiController extends Controller {

    function __construct() {
        parent::__construct();
		$this->hostIp = gethostwithhttp();
		//$this->hostIp = 'http://1028fd24.ngrok.io';
    }

	public function _initialize() {
		Vendor("WxPayR.Api");
		Vendor("WxPayR.JsApiPay");
		Vendor("WxPayR.WxPayConfig");
		Vendor('WxPayR.WxPayData');
		Vendor('WxPayR.Exception');
		Vendor('AlipayApp.Notify');
		Vendor('AlipayApp.Corefunction');
		Vendor('AlipayApp.Rsafunction');
	}

	//向流量服务器下单
	public function wx_recharge() {
		$status = "error";
		$msg = "系统错误";
		$user_type = trim(I("user_type"));
		$user_id = trim(I("user_id"));
		if (empty($user_type)) {
			$this -> ReturnJson($status, "参数错误", "");
		}

		if ($user_type == 1) {
			$proxy_id = $user_id;
			$map['proxy_id'] = $proxy_id;
		} else {
			$enterprise_id = $user_id;
			$map['enterprise_id'] = $enterprise_id;
		}
		//$sys_api=$this->get_sys_api($map);
		$sys_api = M("sys_api") -> where($map) -> find();
		$phone = trim(I("phone"));
		$out_trade_on = I("out_trade_no");
		$product_id = trim(I("product_id"));
		// $pay_order_code = trim(I("pay_order_code"));
		$pre_deal = M('pay_order') -> where(array('pay_order_code' => $out_trade_on, 'pay_status' => 2)) -> find();
	
				
		
		//＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊
		$pay_type = I("pay_type");//如果用户为再次送充时,将之前订单号去除
		if(!empty($pay_type) && $pay_type == "pay_repay")
		{
			//清除支付订单号。再次下单
			$pre_deal['order_code'] = 0;
		}
		//********************************
		if ($pre_deal) {
			if ($pre_deal['order_code'] > 0) {
				//已经下单之后
				exit();
			}
		} else {
			exit();
		}
		if (!$phone) {
			$msg = '请输入电话号码';
			$this -> ReturnJson($status, $msg);
		}
		$rule = "/^0?(13[0-9]|15[012356789]|18[0-9]|14[57]|17[0-9])[0-9]{8}$/";
		$tag = preg_match($rule, $phone);
		if ($tag == 0) {
			$this -> ReturnJson($status, "失败：手机号码错误！");
		}
		$result = CheckMobile($phone);
		
		$map1['product_id'] = $product_id;
		$products = $this->CheckPacketMsg($map1);//检查流量包
		$size = $products['size'];
		$submiturl = C("URL");
		$phone = $phone;
		if($products['province_id']==1){
			$range=0;
		}else{
			$range=1;
		}


		//$range = $products["product_type"];
		$size = $size;
		//单位 M
		//$account    = 'LKKKUZMO';
		//$api_key    = 'KN0lqsyYbWsB65LCZSo13f2zfZhCPdyW';
		$account = $sys_api['api_account'];
		$api_key = $sys_api['api_key'];
		$timeStamp = time();
		$pd = array('account' => $account, 'action' => 'Charge', 'phone' => $phone, 'range' => $range, 'size' => $size, 'timeStamp' => $timeStamp);
		$pre_str = "{$api_key}account={$account}&action=Charge&phone={$phone}&range={$range}&size={$size}&timeStamp={$timeStamp}{$api_key}";
		$pd['sign'] = md5($pre_str);
		$rt = https_request($submiturl, $pd);
		$ret = json_decode($rt, true);
		//{"api_id":"154","user_type":"2","proxy_id":"0","enterprise_id":"90","api_account":"LKKKUZMO","api_key":"qwdsadadasdsadsad","api_callback_address":"http:\/\/120.26.93.95:8081\/testcb.php","api_callback_ip":"","is_activity":"0","is_option_user":"0"}
		$orderID = $ret['orderID'];
		if(empty($orderID))
		{
			//{"account":"LKKKUZMO","action":"Charge","phone":"18720073829","range":"1","size":"70","timeStamp":"1490940072","sign":"bd555f258fc5f9208fd2ae0905dfd55a"}
			$msg = $ret['respCode'].",".$ret['respMsg'];
			$order['remark'] = $msg;
			$title =  $ret['respMsg'];//json_encode($pd);//
			$order['order_code'] = "";
			$status = "error";
		}
		else
		{
			$title = "下单成功";
			$order['order_code'] = $orderID;
			$status = "success";
		}
		
		$order['pay_date'] = date("Y-m-d H:i:s", time());
		$data =	M('pay_order') -> where(array('pay_order_code' =>$out_trade_on)) -> save($order);
		$this -> ReturnJson($status, $title);
//		if(!$orderID){
//			$wx_data=M('pay_order') -> where(array('pay_order_code' =>$out_trade_on))->find();
//			$this->wx_refund($wx_data);
//		}
	}

	//退款
	private function wx_refund($data)
	{
		$transaction_id = $data["number"];
		$total_fee = $data["price"] * 100;
		$refund_fee = $data["price"] * 100;
		if ($data['user_type'] == 1) {
			$user_id = $data['proxy_id'];
		} else {
			$user_id = $data['enterprise_id'];
		}
		$config = $this->wx_getconfig($data['user_type'], $user_id);
		if (empty($config['SSLCERT_PATH']) || empty($config['SSLKEY_PATH'])) {
			$order['refund_status'] = 1;//未退款
			M('pay_order')->where(array('pay_order_code' => $data['pay_order_code']))->save($order);
			exit();
		}
		$input = new \WxPayRefund;
		$input->SetTransaction_id($transaction_id);
		$input->SetTotal_fee($total_fee);
		$input->SetRefund_fee($refund_fee);
		$input->SetOut_refund_no($config['MCHID'] . date("YmdHis"));
		$input->SetOp_user_id($config['MCHID']);
		$return = \WxPayApi::refund($input, $config);
		if ($return["result_code"] == "SUCCESS" && $return["return_code"] == "SUCCESS") {
			$order['refund_status'] = 2;//已退款
			M('pay_order')->where(array('pay_order_code' => $data['pay_order_code']))->save($order);
			exit();
		} else {
			$order['refund_status'] = 1;//退款退款
			M('pay_order')->where(array('pay_order_code' => $data['pay_order_code']))->save($order);
			exit();
		}
	}

	//获取信息config
	public function  wx_getconfig($user_type,$user_id){
		if($user_type==1){
			$map['proxy_id']=$user_id;
		}else{
			$map['enterprise_id']=$user_id;
		}
		$result=M("user_set")->where($map)->find();

		//payment_type  int  1表示运营方收款    2表示企业收款
		$payment_type = $result['payment_type'];
		if($payment_type == 1)
		{
			$paymentmap['enterprise_id'] = 90;
			//运营方尚通科技收款
			$result=M("user_set")->where($paymentmap)->find();
		}

		//默认还是企业端收款
		$config=array();
		$config['APPID']=$result['wx_appid'];
		$config['APPSECRET']=$result['wx_appsecret'];
		$config['MCHID']=$result['wx_mchid'];
		$config['KEY']=$result['wx_key'];
		$config['SSLCERT_PATH']=$result['wx_pem_file_one'];
		$config['SSLKEY_PATH']=$result['wx_pem_file_two'];
		return $config;
	}

	//对外api接口 h5获取流量所有包
	public function packet_all() {
		
		$user_type = trim(I("user_type"));
		$user_id = trim(I("user_id"));
		if ($user_type == 1) {
			$data['proxy_id'] = $user_id;
		} else {
			$data['enterprise_id'] = $user_id;
		}
		//
		$dicountsData = $this->get_dicounts($user_type,$user_id);

		$mobile_discount= $dicountsData['mobile_discount'];
		$unicom_discount = $dicountsData['unicom_discount'];
		$telecom_discount = $dicountsData['telecom_discount'];
		
		
		//获取移动全国流量包
		$map = array('operator_id' => 1, 'status' => 1, 'province_id' => 1);
		$msg = D('ChannelProduct') -> get_product_list($map,$user_id);
		$packety = array();
		$list=get_scene_product(1);
		//全国包
		foreach ($msg as $key => $row) {
			$is_status=1;//表示产品有效
			if($list){
				foreach ($list as $l){
					if($l==$row['size']){
						$is_status=2;//表示该产品无效
						break;
					}
				}
			}
			$s=($row['size'] >= 1024) ? ($row['size'] / 1024) : 1;//判断是不是特殊包
			if(is_int($s) && $is_status==1){
				$size = ($row['size'] >= 1024) ? ($row['size'] / 1024) . "G" : ($row['size']) . "M";
				$price_discount_final = round($row['price']*$mobile_discount/10.0,2);
				$mid = array('id' => $row['product_id'], 'size' => $size, 'price' => $row['price'], 'price_market' => floatval($price_discount_final));
				$packety[] = $mid;
			}
		}

		//获取联通全国流量包
		$map1 = array('operator_id' => 2, 'status' => 1, 'province_id' => 1);
		$msg1 = D('ChannelProduct') -> get_product_list($map1,$user_id);
		$packetl = array();
		$list1=get_scene_product(2);
		//全国包
		foreach ($msg1 as $key => $row) {
			$is_status=1;//表示产品有效
			if($list1){
				foreach ($list1 as $l){
					if($l==$row['size']){
						$is_status=2;//表示该产品无效
						break;
					}
				}
			}
			$s=($row['size'] >= 1024) ? ($row['size'] / 1024) : 1;//判断是不是特殊包
			if(is_int($s) && $is_status==1){
				$size = ($row['size'] >= 1024) ? ($row['size'] / 1024) . "G" : ($row['size']) . "M";
				//打折
				$price_discount_final = round($row['price']*$unicom_discount/10.0,2);
				$mid = array('id' => $row['product_id'], 'size' => $size, 'price' => $row['price'], 'price_market' => floatval($price_discount_final));
				$packetl[] = $mid;
			}
		}

		//获取电信全国流量包
		$map2 = array('operator_id' => 3, 'status' => 1, 'province_id' => 1);
		$msg2 = D('ChannelProduct') -> get_product_list($map2,$user_id);
		$packetd = array();
		$list2=get_scene_product(3);
		//全国包
		foreach ($msg2 as $key => $row) {
			$is_status=1;//表示产品有效
			if($list2){
				foreach ($list2 as $l){
					if($l==$row['size']){
						$is_status=2;//表示该产品无效
					}
				}
			}
			$s=($row['size'] >= 1024) ? ($row['size'] / 1024) : 1;//判断是不是特殊包
			if(is_int($s) && $is_status==1) {
				$size = ($row['size'] >= 1024) ? ($row['size'] / 1024) . "G" : ($row['size']) . "M";
				$price_discount_final = round($row['price'] * $telecom_discount / 10.0, 2);
				$mid = array('id' => $row['product_id'], 'size' => $size, 'price' => $row['price'], 'price_market' => floatval($price_discount_final));
				$packetd[] = $mid;
			}
		}

		//组装返回数组
		$packet = array( array('type' => '移动', 'packet' => $packety, ), array('type' => '联通', 'packet' => $packetl, ), array('type' => '电信', 'packet' => $packetd, ), );
		//返回数据
		$this -> ReturnJson('success', '', $packet);
	}

	//获取运营商折扣设置
	private function get_dicounts($user_type, $user_id) {
		if ($user_type == 1) {
			$data['proxy_id'] = $user_id;
		} else {
			$data['enterprise_id'] = $user_id;
		}
		//
		$data['discount_type'] = 1;
        //通过省份
		$data['province_id'] = 1;
        //通过运营商
		$data['operator_id'] = 1;//移动
		$dicountsData = M('person_discount') -> where($data) -> find();
        $mobile_discount = $dicountsData["charge_discount"];
		if((float)$mobile_discount == 0 || empty($mobile_discount))
		{
			$mobile_discount = 10;
		}
		//联通
		$data['operator_id'] = 2;
		$dicountsData = M('person_discount') -> where($data) -> find();
        $unicom_discount = $dicountsData["charge_discount"];
		if((float)$unicom_discount == 0 || empty($unicom_discount))
		{
			$unicom_discount = 10;
		}
		//电信
		$data['operator_id'] = 3;
		$dicountsData = M('person_discount') -> where($data) -> find();
        $telecom_discount = $dicountsData["charge_discount"];
		if((float)$telecom_discount == 0 || empty($telecom_discount))
		{
			$telecom_discount = 10;
		}

		return array(
			"mobile_discount" => $mobile_discount,
			"unicom_discount" => $unicom_discount,
			"telecom_discount" => $telecom_discount
		);
	}
	//返回值
	protected function ReturnJson($status, $msg = '', $data = '') {
		$status = $status == "success" ? 1 : 0;
		$array = array('status' => $status, 'msg' => $msg, 'data' => $data, );

		$this -> ajaxReturn($array);
	}

	private function CheckPacketMsg($packet_msg) {
		if (empty($packet_msg) || !is_array($packet_msg)) {
			$this->ReturnJson(false, '未传入流量包信息！');
		}
		$packet_msg = D('ChannelProduct')->channelproductinfo($packet_msg['product_id']);
		if (empty($packet_msg)) {
			$this->ReturnJson(false, '流量包信息有误！');
		} else {
			return $packet_msg;
		}
	}

	//判定是否还有余额去支付这个流量包
	public function GetBalance()
	{
		$user_type = I("user_type");
		$user_id = I("user_id");

		if ($user_type == 1) {
			$map['proxy_id'] = $user_id;
		} else {
			$map['enterprise_id'] = $user_id;
		}
		$sys_api = M("sys_api") -> where($map) -> find();
		$submiturl = C("API_BALANC");
		//单位 M
		//$account    = 'LKKKUZMO';
		//$api_key    = 'KN0lqsyYbWsB65LCZSo13f2zfZhCPdyW';
		$account = $sys_api['api_account'];
		$api_key = $sys_api['api_key'];

		$timeStamp = time();
		$bd = array(
		'account'	=> $account,
		'action'	=> 'Balance',
		'timeStamp'	=> $timeStamp,
		);
		$bd['sign'] = md5("{$api_key}account={$account}&action=Balance&timeStamp={$timeStamp}{$api_key}");
		$rt = https_request($submiturl, $bd);
		$ret = json_decode($rt, true);
		$lastmoney = $ret['respMsg'];
		$this->ReturnJson(true,"",$lastmoney);
	}




	/**
	 * 读取当前手机所能使用的产品
	 */
	public function check_mobile() {
		$mobile = trim(I('phone'));
		$user_type=I('user_type');
		$user_id=I("user_id");
		$p_ty=I("p_ty");
		//检查手机号码
		if (!isMobile2($mobile)) {
			$this -> ReturnJson(false, "失败：手机号码错误！");
		}
		//手机结构数组
		$result = CheckMobile($mobile);
		if($result['operator_id']==0){
			$this -> ReturnJson(false, "失败：手机号码错误！");
		}
		

		//调用聚合数据接口
		//聚合接口没有返回数据
		if ($result['status'] = false)
			$this -> ReturnJson(false, $result['msg']);
		//筛选出省流量包和全国流量包
		$p_ids = 1;
		if ($result['province_id']) {
			$p_ids = $p_ids . "," . $result['province_id'];
		}

		
		
		$map = array('operator_id' => $result['operator_id'], 'status' => 1, 'province_id' => array('IN', $p_ids));
		
		//全国包
		$packetQ = array();
		//省内包
		$packetS = array();
		
		$list = get_scene_product($result['operator_id']);//获取该网没有的产品
		if($user_id == 977)
		{
			//将2个公司全国id：100947 977  名称：易到    分省id： 100957    名称：易到分省 987
			//当该手机号码。所属分省通道。则使用该省流量包
			$yduser_id = "987"; //获取分省流量包
			$product_list = D('ChannelProduct')->get_product_list($map,$yduser_id,$result);
			if(empty($product_list))
			{
				//如果定向省内通道为空
				//则启用全国通道
				$product_list = D('ChannelProduct')->get_product_list($map,$user_id,$result);
			}
			else
			{
				//如果用户有渠道则产品包连接未渠道
				$user_id = $yduser_id;
			}
		}
		else
		{
			//所有通道产品 
			$product_list = D('ChannelProduct')->get_product_list($map,$user_id,$result);
		}


		
		//折扣获取＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊

		

		// $dicountsData = M('person_discount')->where($discountdata)->find();
        // $dicountData = $dicountsData["charge_discount"];
		// if((float)$dicountData == 0 || empty($dicountData))
		// {	
		// 	//如果用户没有设定该省折扣则用全国折扣
        //     $discountdata['province_id'] = 1;
        //     $dicountsData = M('person_discount')->where($discountdata)->find();
        //     $dicountData = $dicountsData["charge_discount"];
        //     if((float)$dicountData == 0 || empty($dicountData))
        //     {
		// 	    $dicountData = 10;
        //     }
		// }


        if((int)$user_type==1){
          $discountdata['user_type']=1;
          $discountdata['proxy_id']=$user_id;
        }else{
          $discountdata['user_type']=2;
          $discountdata['enterprise_id']=$user_id;
        }
		//查看折扣类型设置
        $usescece =M('user_set as u')
            ->join('left join t_flow_user_sceneset as s on s.user_set_id = u.account_id')
            ->field('s.user_province_type')
            ->where($discountdata)
            ->find();
		$user_province_type = $usescece["user_province_type"];
		if(empty($user_province_type))
		{
			//默认为全国折扣
			$user_province_type = 1;
		}


		//1为微信 2为app
		$discountdata['discount_type'] = 1;
        //通过运营商
		$discountdata['operator_id'] = $result["operator_id"];


		//为全国折扣
		//全国包折扣
		$discountdata['province_id'] = 1;
		$dicountsData = M('person_discount') -> where($discountdata) -> find();
		$dicountData = $dicountsData["charge_discount"];
		if ((float)$dicountData == 0 || empty($dicountData)) {
			$dicountData = 10;
		}

		//省内包折扣
		$discountdata['province_id'] = $result["province_id"];
		$dicountsData = M('person_discount') -> where($discountdata) -> find();
		$provincedicountData = $dicountsData["charge_discount"];
		if ((float)$provincedicountData == 0 || empty($provincedicountData)) {
						//如果选择为分省折扣
				if($user_province_type == 2)
				{
						//当用户没有设置当前城市折扣时取用全国折扣
					$discountdata['province_id'] = 1;
					$dicountsData = M('person_discount') -> where($discountdata) -> find();
					$provincedicountData = $dicountsData["charge_discount"];
				}
				if ((float)$dicountData == 0 || empty($dicountData)) {
					$provincedicountData = 10;
				}
		}


		if($user_province_type == 2)
		{
       		//手机号省份
			//表示为分省折扣
			$dicountData = $provincedicountData;
			
		}


		//折扣获取＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊

		foreach ($product_list as $key => $row) {
			$is_status=1;//表示产品有效
			if($list){
				foreach ($list as $l){
					if($l==$row['size']){
						$is_status=2;//表示该产品无效
					}
				}
			}
			$s = ($row['size'] >= 1024) ? ($row['size']/1024):1;
			if(is_int($s) && $is_status==1){
				$size = ($row['size'] >= 1024) ? ($row['size']/1024)."G" : ($row['size'])."M";
				$discount = D('ChannelProduct')->get_product_discount(array('product_name'=>$row['product_name'],'operator_id'=>$row['operator_id'],'province_id'=>$row['province_id']));
				$packet_discount = $discount;
				$price_discount = round($packet_discount * $row['price'], 2);
				$price_discount_final = round($row['price']*$dicountData/10.0,2);
				$price_provincediscount_final = round($row['price']*$provincedicountData/10.0,2);

				if ((int)$row['province_id'] == 1) {
					$mid = array('id' => $row['product_id'], 'productuser_id'=>$user_id, 'size' => $size, 'price' => $price_discount,'price_discount'=>number_format($price_discount_final, 2, '.', ''), 'price_market' => $row['price']);
					//默认折扣为全国折扣
					$packetQ[] = $mid;
				} else {
					//省内折扣
					$mid = array('id' => $row['product_id'], 'productuser_id'=>$user_id, 'size' => $size, 'price' => $price_discount,'price_discount'=>number_format($price_provincediscount_final, 2, '.', ''), 'price_market' => $row['price']);
					$packetS[] = $mid;
				}
			}
		}

		//返回数据
		$product = I("product");
		$province_name = $result['province_name'];
		$province_name = str_replace("省","",$province_name);	
		$operator_id = $result['operator_id'];
		switch($operator_id)
		{
			case 1:
			{
				//移动
				$province_name = $province_name . "移动";
			}
			break;
			case 2:
			{
				//联通
				$province_name = $province_name . "联通";
			}
			break;
			case 3:
			{
				//电信
				$province_name = $province_name . "电信";
			}
			break;
		}
		
		
		
		if($product == 1)
		{
			$packetAll = $this->changelist($packetQ,$packetS);
			//组装返回数组
			$packet = array('num' => $mobile,'attribution' => $province_name, 'belong' => $result['area'] . $result['operator_name'], 'packetAll' => $packetAll);
		}
		else
		{
			//组装返回数组
			$packet = array('num' => $mobile,'attribution' => $province_name, 'belong' => $result['area'] . $result['operator_name'], 'Type' => array( array('type' => '全国', 'packet' => $packetQ, ), array('type' => '省内', 'packet' => $packetS, ), ), );
		}

		$this -> ReturnJson('success', '', $packet);
	}

	//处理列表油画。针对易道用车版
	public function changelist($packetQ,$packetS) {
		//同类包
		$packetAll = array();
		foreach ($packetQ as $Qpacket)
		{
			$find = false;
			foreach ($packetS as $Spacket)
			{
				if($Spacket['size']==$Qpacket['size'])
				{
					//相同的产品
					$spacketAll = array('packetQ' => $Qpacket, 'packetS' => $Spacket);
					$packetAll[] = $spacketAll;
					$find = true;
				}
			}
			if($find == false)
			{
				//全国有，省内没有的包。单独提取处理
				$spacketAll = array('packetQ' => $Qpacket);
				$packetAll[] = $spacketAll;
			}
		}	
		
		foreach ($packetS as $Spacket)
		{
			$find = false;
			foreach ($packetQ as $Qpacket)
			{
				if($Spacket['size']==$Qpacket['size'])
				{
					$find = true;
				}
			}
			if($find == false)
			{
				//省内有，全国没有的包。单独提取处理
				$spacketAll = array('packetS' => $Spacket);
				$packetAll[] = $spacketAll;
			}
		}	

		//省内有，全国没有的包

		return $packetAll;
	}

	private function get_sys_api($map){
		$us = M("user_set") -> where($map) -> find();
		$user=M("sys_user")->where(array('user_id'=>$us['modify_user_id']))->find();
		if(!empty($user['proxy_id']) && $user['proxy_id']>0){
			$where['user_type']=1;
			$where['proxy_id']=$user['proxy_id'];
		}else{
			$where['user_type']=2;
			$where['enterprise_id']=$user['enterprise_id'];
		}
		$sys_api = M("sys_api") -> where($where) -> find();
		return $sys_api;
	}
}
