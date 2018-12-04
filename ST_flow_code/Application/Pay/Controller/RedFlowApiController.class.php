<?php
namespace Pay\Controller;
use Think\Controller;

class RedFlowApiController extends Controller {
	function __construct() {
		parent::__construct();
		$this -> hostIp = gethostwithhttp();
		//$this->hostIp = 'http://1028fd24.ngrok.io';
	}

	/**
	 * 引入支付宝支付类,用于个人充值
	 */
	public function _initialize() {
		Vendor('WxPayApp.Api');
		Vendor('WxPayApp.WxAppPayConfig');
		Vendor('WxPayApp.Exception');
		Vendor('WxPayApp.Notify');
		Vendor('WxPayApp.NativePay');
		Vendor('WxPayApp.Native_notify');
		Vendor('WxPayApp.JsApiPay');
		Vendor('AlipayApp.Notify');
		Vendor('AlipayApp.Corefunction');
		Vendor('AlipayApp.Rsafunction');
	}

	public function localencode($data) {
		for ($i = 0; $i < strlen($data); $i++) {
			$ord = ord($data[$i]);
			$ord += 20;
			$string = $string . chr($ord);
		}
		$string = base64_encode($string);
		return $string;
	}

	/**
	 * 微信红包
	 *
	 *  获取全国所有流量包
	 */
	public function PacketAll() {

		$pre_sign = array();
		$this -> checkSign($pre_sign);

		$discountNumber = 0.00;
		$dicountsData = $this -> get_dicounts();
		
		//商品打折

		//获取移动全国流量包
		$map = array('operator_id' => 1, 'status' => 1, 'province_id' => 1);
		$msg = D('ChannelProduct') -> get_product_list($map);
		$packety = array();
		//全国包
		foreach ($msg as $key => $row) {
			$size = ($row['size'] >= 1024) ? ($row['size'] / 1024) . "G" : ($row['size']) . "M";
			$discountNumber = $dicountsData['mobile_discount'];
			if ((float)$discountNumber == 0 || empty($discountNumber)) {
				$discountNumber = 10;
			}
			$discount_money = round($row['price'] * $discountNumber / 10.0, 2);
			$discount_money = number_format($discount_money, 2, '.', '');
			if ($discount_money <= 0.01) {

				$discount_money = 0.01;
				$discount_money = number_format($discount_money, 2, '.', '');
			}
			//										var_dump($discount_money);

			//建立订单
			$mid = array('id' => $row['product_id'], 'size' => $size, 'price' => $discount_money, 'price_market' => floatval($row['price']));
			$packety[] = $mid;
		}

		//获取联通全国流量包
		$map1 = array('operator_id' => 2, 'status' => 1, 'province_id' => 1);
		$msg1 = D('ChannelProduct') -> get_product_list($map1);
		$packetl = array();
		//全国包
		foreach ($msg1 as $key => $row) {
			$size = ($row['size'] >= 1024) ? ($row['size'] / 1024) . "G" : ($row['size']) . "M";
			$discountNumber = $dicountsData['unicom_discount'];
			if ((float)$discountNumber == 0 || empty($discountNumber)) {
				$discountNumber = 10;
			}
			$discount_money = round($row['price'] * $discountNumber / 10.0, 2);
			$discount_money = number_format($discount_money, 2, '.', '');
			if ($discount_money <= 0.01) {
				$discount_money = 0.01;
				$discount_money = number_format($discount_money, 2, '.', '');

			}
			$mid = array('id' => $row['product_id'], 'size' => $size, 'price' => $discount_money, 'price_market' => floatval($row['price']));
			$packetl[] = $mid;
		}

		//获取电信全国流量包
		$map2 = array('operator_id' => 3, 'status' => 1, 'province_id' => 1);
		$msg2 = D('ChannelProduct') -> get_product_list($map2);
		$packetd = array();
		//全国包
		foreach ($msg2 as $key => $row) {
			$size = ($row['size'] >= 1024) ? ($row['size'] / 1024) . "G" : ($row['size']) . "M";
			$discountNumber = $dicountsData['telecom_discount'];
			if ((float)$discountNumber == 0 || empty($discountNumber)) {
				$discountNumber = 10;
			}
			$discount_money = round($row['price'] * $discountNumber / 10.0, 2);
			$discount_money = number_format($discount_money, 2, '.', '');
			if ($discount_money <= 0.01) {
				$discount_money = 0.01;
				$discount_money = number_format($discount_money, 2, '.', '');

			}
			$mid = array('id' => $row['product_id'], 'size' => $size, 'price' => $discount_money, 'price_market' => floatval($row['price']));
			$packetd[] = $mid;
		}

		//组装返回数组
		$packet = array( array('type' => '移动', 'packet' => $packety, ), array('type' => '联通', 'packet' => $packetl, ), array('type' => '电信', 'packet' => $packetd, ), );
		//返回数据
		$this -> ReturnJson('success', '', $packet);
	}

	// 生成订单
	public function generate_order() {

		$STAppKey = I('STAppKey');
		$pay_id = I('payId', '', 'int');
		$orderJsonStr = htmlspecialchars_decode(I('orderJsonStr'));
		$orderJson = json_decode($orderJsonStr, true);
		$red_order_code = apply_number2("", 6);

		$preSign = array();
		$preSign['payId'] = $pay_id;
		$preSign['orderJsonStr'] = $orderJsonStr;
		$this -> checkSign($preSign);
		$products = $orderJson['orderItems'];
		//		var_dump($orderJson);
		//		var_dump($products);
		$productTotal = 0.0;

		$discountNumber = 0.00;
		$dicountsData = $this -> get_dicounts();

		foreach ($products as $product) {
			$where['product_id'] = $product['productId'];
			$productItem = M("channel_product") -> where($where) -> find();
			if ((int)$productItem['operator_id'] == 1) {
				$discountNumber = $dicountsData['mobile_discount'];
			} else if ((int)$productItem['operator_id'] == 2) {
				$discountNumber = $dicountsData['unicom_discount'];

			} else if ((int)$productItem['operator_id'] == 3) {
				$discountNumber = $dicountsData['telecom_discount'];
			}

			if ((float)$discountNumber == 0 || empty($discountNumber)) {
				$discountNumber = 10;
			}

			//商品打折
			$discount_money = round($productItem['price'] * $discountNumber / 10.0, 2);
			$discount_money = number_format($discount_money, 2, '.', '');
			if ($discount_money <= 0.01) {
				$discount_money = 0.01;
				$discount_money = number_format($discount_money, 2, '.', '');

			}
			$productTotal += $discount_money * intval($product['trafficCount']);
		}
		//生成预支付订单串
		$rsdata = $this -> paymentflowred($STAppKey, $orderJson, $red_order_code, $productTotal, $dicountsData, $pay_id);
		//		$productTotal = 0.01;
		//		$productTotal =0.01;
		//var_dump($productTotal);
		$appInfo = M('user_set') -> where(array('third_app_key' => I('st_app_id'))) -> find();

		if ($pay_id == 1) {
			//			$da['notify_url'] = "http://www.ds400.com/index.php/home/Api/Notifyurl";
			//支付宝支付订单信息
			$dataString = "";
			$url = $this -> hostIp . "/index.php/Pay/RedFlowApi/Notifyurl";
			$da['notify_url'] = $url;
			$para['partner'] = $appInfo['alipay_key'];
			$para['seller_id'] = $appInfo['alipay_partner'];
			$para['out_trade_no'] = $red_order_code;
			$para['subject'] = "流量包";
			$para['body'] = "流量包";
			$para['total_fee'] = $productTotal;
			$para['notify_url'] = $da['notify_url'];
			$para['service'] = "mobile.securitypay.pay";
			$para['payment_type'] = "1";
			$para['_input_charset'] = "utf-8";
			$para['it_b_pay'] = "30m";
			$para['show_url'] = "m.alipay.com";
			$data = "";
			while (list($key, $val) = each($para)) {
				$data .= $key . '="' . $val . '"&';
				if ($key == 'out_trade_no' || $key == 'notify_url') {
					$dataString .= $key . '="' . '%@' . $key . '"&';
				} else {
					$dataString .= $key . '="' . $val . '"&';
				}
			}
			//去掉最后一个&字符
			$data = substr($data, 0, count($data) - 2);
			$dataString = substr($dataString, 0, count($dataString) - 2);

			$priKey = file_get_contents("." . $appInfo['alipay_pem_file_two']);
			//			$priKey = file_get_contents("./Public/key/rsa_private_key.pem");

			$res = openssl_get_privatekey($priKey);
			openssl_sign($data, $sign, $res);
			openssl_free_key($res);
			//base64编码
			$sign = urlencode(base64_encode($sign));
			$data = $data . '&sign="' . $sign . '"&sign_type="RSA"';
			//			$sign = urlencode($sign);
			$dataString = $dataString . '&sign="' . $sign . '"&sign_type="RSA"';

			$rsData['data'] = $data;
			$rsData['type'] = '1';
			$rsData['rsdata'] = $dataString;
			$rsData['notify_url'] = $this -> GetRSAData($url);
			$rsData['out_trade_no'] = $this -> GetRSAData($red_order_code);
			$this -> ReturnJson('success', '生成流量链接成功', $rsData);

		} else {
			$url = gethostwithhttp() . "/index.php/Pay/RedFlowApi/GetWechatCallback";

			$input = new \WxPayUnifiedOrder;
			$input -> SetBody("流量充值");
			$input -> SetAttach("流量充值");
			$input -> SetOut_trade_no($red_order_code);
			$input -> SetTotal_fee($productTotal * 100);
			//$input->SetTotal_fee(1); //单位 分
			$input -> SetTime_start(date("YmdHis"));
			$input -> SetTime_expire(date("YmdHis", time() + 600));
			$input -> SetNotify_url($url);
			//$input->SetProduct_id($red_order_code);
			$input -> SetTrade_type("APP");

			$config['APPID'] = $appInfo['app_appid'];
			$config['MCHID'] = $appInfo['app_mchid'];
			$config['KEY'] = $appInfo['app_key'];
			$config['SSLCERT_PATH'] = $appInfo['app_pem_file_one'];
			$config['SSLKEY_PATH'] = $appInfo['app_pem_file_two'];

			$order = \WxPayApi::unifiedOrder($input, 6, $config);
			$tools = new \JsApiPay($config);
			$jsApiParameters = $tools -> GetApiParameters($order);
			$rsData["red_order_code"] = $red_order_code;
			$rsData['jsApiParameters'] = $jsApiParameters;
			$rsData['type'] = '1';
			$this -> ReturnJson('success', '生成流量链接成功', $rsData);
		}
	}

	private function app_wx_config($user_type, $user_id) {
		if ($user_type == 1) {
			$map['proxy_id'] = $user_id;
		} else {
			$map['enterprise_id'] = $user_id;
		}
		$result = M("user_set") -> where($map) -> find();
		//默认还是企业端收款
		$config = array();
		$config['APPID'] = $result['app_appid'];
		$config['APPSECRET'] = $result['app_appsecret'];
		$config['MCHID'] = $result['app_mchid'];
		$config['KEY'] = $result['app_key'];
		return $config;
	}

	//微信回调
	public function GetWechatCallback() {
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		$res = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		$pre = M('red_order') -> where(array('red_order_code' => $res['out_trade_no'])) -> find();
		$user_type = $pre["user_type"];
		if ($user_type == 1) {
			$user_id = $pre['proxy_id'];
		} else {
			$user_id = $pre['enterprise_id'];
		}
		$config = $this -> app_wx_config($user_type, $user_id);
		$notify = new \PayNotifyCallBack($config);
		$result = $notify -> Handle(false);
		$pre_deal = M('red_order') -> where(array('red_order_code' => $result['out_trade_no'])) -> find();

		if (2 == $pre_deal['pay_status']) {//2 已支付
			$notify = new \WxPayNotify();
			$notify -> SetReturn_code('SUCCESS');
			$notify -> SetReturn_msg('OK');
			$xml = $notify -> ToXml();
			\WxpayApi::replyNotify($xml);
		} else if (1 == $pre_deal['pay_status']) {

			//1 未支付
			if ($result['result_code'] == 'SUCCESS') {
				$upd = array(
				//                  'order_code'    => $order_id,
				'number' => $result['transaction_id'], 'pay_status' => 2, 'pay_date' => date('Y-m-d H:i:s'), );
				/*$fp = fopen("access_token.json", "w");
				 fwrite($fp, json_encode("收到回调"));
				 fwrite($fp, json_encode($upd));
				 fclose($fp);*/
				M('red_order') -> where(array("red_order_code" => $result['out_trade_no'])) -> save($upd);
				S('success', 0);
			}
		}
	}

	private function app_alipy_config($user_type, $user_id) {
		if ($user_type == 1) {
			$map['proxy_id'] = $user_id;
		} else {
			$map['enterprise_id'] = $user_id;
		}
		$result = M("user_set") -> where($map) -> find();
		$alipay_config['partner'] = $result['alipay_partner'];
		//这里是你在成功申请支付宝接口后获取到的PID；
		$alipay_config['key'] = $result['alipay_key'];
		//这里是你在成功申请支付宝接口后获取到的Key
		$alipay_config['sign_type'] = strtoupper('RSA');
		$alipay_config['ali_public_key_path'] = $result['alipay_pem_file'];
		$alipay_config['public_key_path'] = $result['alipay_pem_file_two'];
		$alipay_config['input_charset'] = strtolower('utf-8');
		$alipay_config['cacert'] = './Public/key/cacert.pem';
		$alipay_config['transport'] = "http";
		return $alipay_config;
	}

	/**
	 * 支付宝服务器异步通知页面方法
	 */
	public function Notifyurl() {
		$pre = M('red_order') -> where(array('red_order_code' => $_POST['out_trade_no'])) -> find();
		$user_type = $pre["user_type"];
		if ($user_type == 1) {
			$user_id = $pre['proxy_id'];
		} else {
			$user_id = $pre['enterprise_id'];
		}
		$alipay_config = $this -> app_alipy_config($user_type, $user_id);
		$alipayNotify = new \AlipayNotify($alipay_config);
		$verify_result = $alipayNotify -> verifyNotify();
		if ($verify_result) {
			//write_debug_log(array(__METHOD__.':'.__LINE__, '支付宝异步通知数据==', $_POST));
			//验证成功
			//获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
			$out_trade_no = $_POST['out_trade_no'];
			//商户订单号
			$trade_no = $_POST['trade_no'];
			//支付宝交易号
			/*$trade_status = $_POST['trade_status'];      //交易状态
			 $total_fee = $_POST['total_fee'];         //交易金额
			 $notify_id = $_POST['notify_id'];         //通知校验ID。
			 $notify_time = $_POST['notify_time'];       //通知的发送时间。格式为yyyy-MM-dd HH:mm:ss。
			 $buyer_email = $_POST['buyer_email'];       //买家支付宝帐号；
			 $parameter = array(
			 "out_trade_no" => $out_trade_no, //商户订单编号；
			 "trade_no" => $trade_no,     //支付宝交易号；
			 "total_fee" => $total_fee,    //交易金额；
			 "trade_status" => $trade_status, //交易状态
			 "notify_id" => $notify_id,    //通知校验ID。
			 "notify_time" => $notify_time,  //通知的发送时间。
			 "buyer_email" => $buyer_email,  //买家支付宝帐号；
			 );*/

			if (($_POST['trade_status'] == 'TRADE_FINISHED') || ($_POST['trade_status'] == 'TRADE_SUCCESS')) {//交易结束

				$payOrrdermap = array();
				$payOrrdermap['red_order_code'] = $out_trade_no;
				$middle = M('red_order') -> where($payOrrdermap) -> find();

				switch (intval($middle['pay_status'])) {
					case 1 :
						//未支付
						$upd = array(
						// 'order_code'    => $order_code,
						'number' => $trade_no, 'pay_status' => 2, 'pay_date' => date('Y-m-d H:i:s'), );
						M('red_order') -> where("red_order_code='{$out_trade_no}'") -> save($upd);
						break;
					case 2 :
						//已支付
						break;
				}
			}
			//其他状态不处理
			echo "success";
			//请不要修改或删除
		} else {
			//验证失败
			echo "fail";
		}
	}

	private function get_dicounts() {
		$st_app_id = I('st_app_id');
		$appInfo = M('user_set') -> where(array('third_app_key' => I('st_app_id'))) -> find();
		$user_type = $appInfo["user_type"];

		if ($user_type == 1) {
			$data['proxy_id'] = $appInfo['proxy_id'];
		} else {
			$data['enterprise_id'] = $appInfo['enterprise_id'];
		}
		
		//
		$data['discount_type'] = 2;
		//通过省份
		$data['province_id'] = 1;
		//通过运营商
		$data['operator_id'] = 1;
		//移动
		$dicountsData = M('person_discount') -> where($data) -> find();
		$mobile_discount = $dicountsData["charge_discount"];
		if ((float)$mobile_discount == 0 || empty($mobile_discount)) {
			$mobile_discount = 10;
		}
		//联通
		$data['operator_id'] = 2;
		$dicountsData = M('person_discount') -> where($data) -> find();
		$unicom_discount = $dicountsData["charge_discount"];
		if ((float)$unicom_discount == 0 || empty($unicom_discount)) {
			$unicom_discount = 10;
		}
		//电信
		$data['operator_id'] = 3;
		$dicountsData = M('person_discount') -> where($data) -> find();
		$telecom_discount = $dicountsData["charge_discount"];
		if ((float)$telecom_discount == 0 || empty($telecom_discount)) {
			$telecom_discount = 10;
		}

		return array("mobile_discount" => $mobile_discount, "unicom_discount" => $unicom_discount, "telecom_discount" => $telecom_discount);
	}


	//红包列表
	public function get_flow_orders() {
		$status = "error";
		$msg = "系统错误";
		$userId = trim(I("user_id"));
		$this -> checkSign($preSign);
		$user = array();
		$user['other_userid'] = $userId;
		$where['thir_app_key'] = trim(I("st_app_id"));
		$user['pay_status'] = 2;
		$enterprise = M('user_set') -> where($where) -> find();
		$user['proxy_id'] = $enterprise['proxy_id'];
		$user['enterprise_id'] = $enterprise['enterprise_id'];
		if (empty($userId)) {
			$this -> ReturnJson($status, "参数错误", "");
		}
		$packets = M('red_order') -> where($user) -> order('order_date DESC') -> select();
		$data = array();
		foreach ($packets as $p) {
			$da = array();
			$da['red_order_id'] = $p['red_order_id'];
			$da['mobile'] = $p['mobile'];
			$da['out_packages_count'] = 0;
			$da['out_packages_count'] = 0;
			if (count($p['out_packages']) > 0) {
				$da['out_packages_count'] = count(explode(",", $p['out_packages']));
			}
			if (count($p['packages']) > 0) {
				$da['packages_count'] = count(explode(",", $p['packages']));
			}
			// $da['packages'] = $p['packages'];
			// $da['out_packages'] = $p['out_packages'];
			$da['order_date'] = $p['order_date'];
			$da['pay_price'] = $p['pay_price'];
			array_push($data, $da);
		}
		$this -> ReturnJson("success", "查询成功", $data);
	}

	//红包详情
	public function get_flow_red_order_detail() {
		$status = "error";
		$msg = "系统错误";
		$orderId = trim(I("order_id"));
		$data = array();
		$preSign['order_id'] = $orderId;
		$this -> checkSign($preSign);
		$where['third_app_key'] = trim(I("st_app_id"));
		$enterprise = M('user_set') -> where($where) -> find();
		$order['c.proxy_id'] = $enterprise['proxy_id'];
		$order['c.enterprise_id'] = $enterprise['enterprise_id'];
		$order['r.proxy_id'] = $enterprise['proxy_id'];
		$order['r.enterprise_id'] = $enterprise['enterprise_id'];
		if (empty($orderId)) {
			$this -> ReturnJson($status, "参数错误", "");
		}
		$order['r.red_order_id'] = $orderId;
		$join = array(C('DB_PREFIX') . 'red_record as r ON r.red_order_id = c.red_order_id', C('DB_PREFIX') . 'channel_product as p ON r.product_id = p.product_id');
		$packet_detail_list = M('red_order as c') -> where($order) -> join($join, "left") -> field("r.mobile,c.remark,r.product_name,c.packages,c.out_packages,r.receive_date,c.order_date,c.share_link,p.operator_id") -> order('r.receive_date DESC') -> select();
		if (empty($packet_detail_list)) {
			$oid['red_order_id'] = $orderId;
			$check_empty_pl = M('red_order') -> where($oid) -> find();
			if (empty($check_empty_pl)) {
				$this -> ReturnJson($status, "订单ID错误", "");
			} else {
				$ce = explode(",", $check_empty_pl['packages']);
				foreach ($ce as $d) {
					$where['product_id'] = $d;
					$product = M("channel_product") -> where($where) -> find();
					$da = array();
					$da['mobile'] = "";
					$da['product_name'] = $product['product_name'];
					$da['packages'] = "";
					$da['out_packages'] = "";
					$da['order_date'] = $check_empty_pl['order_date'];
					// var_dump($da['order_date']);
					$da['receive_date'] = "";
					$da['share_link'] = $check_empty_pl['share_link'];
					$da['remark'] = $check_empty_pl['remark'];
					$da['operator_id'] = $product['operator_id'];
					array_push($data, $da);
				}
				$this -> ReturnJson("success", "查询成功", $data);
			}
		}
		$a1 = explode(",", $packet_detail_list[0]['packages']);
		$a2 = explode(",", $packet_detail_list[0]['out_packages']);
		// 获取未领取的红包
		foreach ($a2 as $v) {
			array_splice($a1, array_search($v, $a1), 1);
		}
		$diff = $a1;
		foreach ($packet_detail_list as $p) {
			$da = array();
			$da['mobile'] = $p['mobile'];
			$da['product_name'] = $p['product_name'];
			$da['packages'] = $p['packages'];
			$da['out_packages'] = $p['out_packages'];
			$da['order_date'] = $p['order_date'];
			$da['receive_date'] = $p['receive_date'];
			$da['order_date'] = $p['order_date'];
			$da['remark'] = $p['remark'];
			$da['share_link'] = $p['share_link'];
			$da['operator_id'] = $p['operator_id'];
			array_push($data, $da);
		}
		foreach ($diff as $d) {
			$where['product_id'] = $d;
			$product = M("channel_product") -> where($where) -> find();
			$da = array();
			$da['mobile'] = "";
			$da['product_name'] = $product['product_name'];
			$da['packages'] = "";
			$da['out_packages'] = "";
			$da['order_date'] = $packet_detail_list[0]['order_date'];
			$da['remark'] = $packet_detail_list['0']['remark'];
			// var_dump($da['order_date']);
			$da['receive_date'] = "";
			$da['share_link'] = $packet_detail_list[0]['share_link'];
			$da['operator_id'] = $product['operator_id'];
			array_push($data, $da);
		}
		$this -> ReturnJson("success", "查询成功", $data);
	}

	private function checkSign($contentArray) {
		$sign = I("sign");
		//签名
		$st_app_id = I('st_app_id');
		$user_id = I('user_id');
		$contentArray['st_app_id'] = $st_app_id;
		$contentArray['user_id'] = $user_id;
		//		var_dump($this -> field_order($contentArray));

		$text = md5(md5($this -> field_order($contentArray)));
		if ($sign != $text) {
			$this -> ReturnJson(false, "失败：签名错误！");
		}
	}

	private function field_order($content) {
		$appInfo = M('user_set') -> where(array('third_app_key' => I('st_app_id'))) -> find();
		//		var_dump($appInfo['third_app_code']);
		if (!empty($content) && is_array($content)) {
			ksort($content);
			foreach ($content as $key => $val) {
				$list[] = $key . "&" . $val;
			}
			$list[] = "key_code&" . $appInfo['third_app_code'];
		}
		return empty($list) ? $content : implode("&", $list);
	}

	protected function GetRSAData($data) {
		$encrypted = "";
		$pu_key = file_get_contents("./Public/RSAKEY/rsa_public_key.pem");
		$res = openssl_get_publickey($pu_key);
		openssl_public_encrypt($data, $encrypted, $pu_key);
		//公钥加密
		//		var_dump($encrypted);
		$encrypted = base64_encode($encrypted);
		return $encrypted;
	}

	//
	public function add_flow_red_order() {
		//       $mobile = trim(I('phone'));  //电话号码
		//       $this->ReturnJson('1');

		//		$red_order_id=trim(I("red_order_id"));
		//      $data['red_order_id']=$red_order_id;

		$STAppKey = I('STAppKey');
		$red_order_code = I('red_order_code');
		$data = $this -> getShareLink($red_order_code);

		if ($data) {
			$paraData['share_link'] = $order['share_link'];
			$this -> ReturnJson('success', '生成流量链接成功', $data);
			//          $this->display("share_flow_red");
		} else {
			$this -> error("参数错误！");
		}
	}

	//获取生成分享链接
	private function getShareLink($red_order_code) {
		$map['red_order_code'] = $red_order_code;
		$data = M("red_order") -> where($map) -> find();
		$pay_price = $data['pay_price'];

		return $data['share_link'];
	}

	//支付
	public function paymentflowred($STAppKey, $orderJsonStr, $red_order_code, $pay_price, $dicountsData, $pay_id) {
		$openid = $STAppKey;
		//		var_dump ($orderJsonStr);

		$orderJson = $orderJsonStr;
		//json_decode(htmlspecialchars_decode($orderJsonStr), true);

		$data['red_order_code'] = $red_order_code;

		$pay_price = $pay_price;
		// $orderJson['totalAmount'];
		//I("pay_price");
		if (empty($pay_price) || $pay_price <= 0) {
			$this -> error("金额有误！");
		}
		$appInfo = M('user_set') -> where(array('third_app_key' => I('st_app_id'))) -> find();

		$user_type = $appInfo['user_type'];
		if ($user_type == 1) {
			$data['enterprise_id'] = 0;
			$user_id = $appInfo['proxy_id'];

		} else {
			$data['proxy_id'] = 0;
			$user_id = $appInfo['enterprise_id'];
		}
		$data['enterprise_id'] = $user_id;
		$data['user_type'] = $user_type;
		//1表示支付宝 2标志网页端微信 3标志sdk支付宝
		$data['pay_type'] = $pay_id;
		$data['wx_openid'] = $openid;
		$data['pay_price'] = $pay_price;
		$data['discount_price'] = $pay_price;
		//      $data['discount_price']=trim(I("discount_price"));
		$data['order_date'] = date("Y-m-d H:i:s", time());
		$data['pay_status'] = 1;
		//备注信息
		$data['remark'] = $orderJson['orderRemark'];
		$data['other_userid'] = I('user_id');

		if ($dicountsData) {
			$data['discount'] = $dicountsData['mobile_discount'] . "," . $dicountsData['unicom_discount'] . "," . $dicountsData['telecom_discount'];
		} else {
			$data['discount'] = "10,10,10";
		}
		$packages = "";
		$products = $orderJson['orderItems'];
		foreach ($products as $product) {
			//			$where['product_id'] = $product['productId'];
			//			$productItem = M("channel_product") -> where($where) -> find();
			for ($i = 0; $i < intval($product['trafficCount']); $i++) {
				if ($packages == "") {
					$packages = $packages . $product['productId'];
				} else {
					$packages = $packages . "," . $product['productId'];
				}
			}

		}
		$data['packages'] = $packages;
		$data['mobile'] = $orderJsonStr['salePhone'];

		//分享内容
		$data1 = $this -> localencode($user_type . "," . $user_id . "," . $data['red_order_code']);
		$share_link = gethostwithhttp() . "/index.php/Pay/AppRedFlow/index?" . $data1;
		//trim(I("remark"));
		//$share_link = $this -> hostIp . "/index.php/Pay/AppRedFlow/index/red_order_code/" . $data['red_order_code'] . "/user_type/" . $user_type . "/user_id/" . $user_id;

		$data['share_link'] = $share_link;
		if ( M("red_order") -> add($data)) {
			return $data;
		} else {
			$this -> display("下单数据错误！");
		}
	}

	//返回值
	protected function ReturnJson($status, $msg = '', $data = '') {
		$status = $status == "success" ? 1 : 0;
		$array = array('status' => $status, 'msg' => $msg, 'data' => $data, );

		$this -> ajaxReturn($array);
	}

	//获取信息config
	public function getconfig($user_type, $user_id) {
		if ($user_type == 1) {
			$map['proxy_id'] = $user_id;
		} else {
			$map['enterprise_id'] = $user_id;
		}
		$result = M("user_set") -> where($map) -> find();

		//payment_type  int  1表示运营方收款    2表示企业收款
		$payment_type = $result['payment_type'];
		if ($payment_type == 1) {
			$paymentmap['enterprise_id'] = 90;
			//运营方尚通科技收款
			$result = M("user_set") -> where($paymentmap) -> find();
		}

		//默认还是企业端收款
		$config = array();
		$config['APPID'] = $result['wx_appid'];
		$config['APPSECRET'] = $result['wx_appsecret'];
		$config['MCHID'] = $result['wx_mchid'];
		$config['KEY'] = $result['wx_key'];
		$config['SSLCERT_PATH'] = $result['wx_pem_file_one'];
		$config['SSLKEY_PATH'] = $result['wx_pem_file_two'];
		return $config;
	}

}
