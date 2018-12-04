<?php
namespace Sdk\Controller;
use Think\Controller;
class WebPaymentController extends Controller {
	public function _initialize() {
		Vendor("WxPayR.Api");
		Vendor("WxPayR.JsApiPay");
		Vendor("WxPayR.WxPayConfig");
		Vendor('WxPayR.Notify');
		Vendor('WxPayR.Native_notify');
		Vendor('WxPayR.WxPayData');
		Vendor('WxPayR.Exception');
		Vendor('WxPayR.NativePay');

		Vendor('Alipay.Corefunction');
		Vendor('Alipay.Md5function');
		Vendor('Alipay.Notify');
		Vendor('Alipay.Submit');
	}

	public function aindex() {
		$rsaKey = $_SERVER["QUERY_STRING"];
		$tmp = stripos($rsaKey, "&");
		if ($tmp != false) {
			$rsaKey = substr($rsaKey, 0, $tmp);
		}

		$strArray = $this -> localdecode($rsaKey);
		$InfoArray = explode(",", $strArray);
		$user_type = $InfoArray[0];
		$user_id = $InfoArray[1];
		$recharge_sources = $InfoArray[2];
		$this -> wx_index($user_type, $user_id,$recharge_sources);
	}

	public function localdecode($data) {
		$data = base64_decode($data);
		for ($i = 0; $i < strlen($data); $i++) {
			$ord = ord($data[$i]);
			$ord -= 20;
			$string = $string . chr($ord);
		}
		return $string;
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

	public function index() {
		$user_type = I("user_type");
		$user_id = I("user_id");
		$recharge_sources = I("recharge_sources");
		$this -> wx_index($user_type, $user_id, $recharge_sources);
	}

	public function paytype()
	{
		$user_type = I("user_type");
		$user_id = I("user_id");
		$pid = I("pid");
		$phone = I("phone");

		$recharge_sourceskey = $user_type . $user_id;
		$recharge_sources = cookie($recharge_sourceskey);

		$pack['packet_id'] = $pid;
		$product = $this -> CheckPacketMsg($pack);
		//检查流量包
		if (!$product) {
			die();
		}
		$data = array();
		$data['pay_order_code'] = apply_number2($phone, 6);
		if ($user_type == 1) {
			$data['user_type'] = 1;
			$data['proxy_id'] = $user_id;
			$discountdata["proxy_id"] = $user_id;
		} else {
			$data['user_type'] = 2;
			$data['enterprise_id'] = $user_id;
			$discountdata["enterprise_id"] = $user_id;
		}

		//商品打折
		$result = CheckMobile($phone);
		//1为微信 2为app
		$discountdata['discount_type'] = 1;
		//通过省份
		$discountdata['province_id'] = $result["province_id"];
		//通过运营商
		$discountdata['operator_id'] = $result["operator_id"];
		$dicountsData = M('person_discount') -> where($discountdata) -> find();
		$dicountData = $dicountsData["charge_discount"];
		if ((float)$dicountData == 0 || empty($dicountData)) {
			//如果用户没有设定该省折扣则用全国折扣
			$discountdata['province_id'] = 1;
			$dicountsData = M('person_discount') -> where($discountdata) -> find();
			$dicountData = $dicountsData["charge_discount"];
			if ((float)$dicountData == 0 || empty($dicountData)) {
				$dicountData = 10;
			}
		}

		//商品打折
		$discount_money = round($product['price'] * $dicountData / 10.0, 2);
		$discount_money = number_format($discount_money, 2, '.', '');

		//payment_type  int  1表示运营方收款    2表示企业收款  3代理商收款
		if ($user_type == 1) {
			$map['proxy_id'] = $user_id;
			$map['enterprise_id'] = 0;
			$map['user_type'] = 1;
		} else {
			$map['enterprise_id'] = $user_id;
			$map['proxy_id'] = 0;
			$map['user_type'] = 2;
		}
		//运营商还是代理商购买
		$userset = M("user_set") -> where($map) -> find();
		$data['payment_type'] = $userset['payment_type'];

		/////////////
		//通过省份
		$map['province_id'] = $result["province_id"];
		//通过运营商
		$map['operator_id'] = $result["operator_id"];
		/////////////
		$discount = M("discount") -> where($map) -> find();
		$discount_number = $discount["discount_number"];
		if (empty($discount_number)) {
			//查询全国
			$map['province_id'] = 1;
			$map['operator_id'] = $result["operator_id"];
			$discount = M("discount") -> where($map) -> find();
			$discount_number = $discount["discount_number"];
			if (empty($discount_number)) {
				$discount_number = 1;
			}
		}
		$data['deduct_price'] = $discount_number * $packet_msg['price'];

		//将代理商价格录入购买记录
		$data['product_id'] = $pid;
		$data['mobile'] = $phone;
		$data['pay_type'] = 2;
		$data['price'] = $product['price'];
		$data['discount_price'] = $discount_money;
		$data['pay_status'] = 1;
		$data['order_date'] = date("Y-m-d H:i:s", time());
		$data['recharge_sources'] = $recharge_sources;
		$pay_order_id = M("pay_order") -> add($data);


		if ($result['operator_id'] == 1) {
			$operatorname = "移动";
		} elseif ($product['operator_id'] == 2) {
			$operatorname = "联通";
		} else {
			$operatorname = "电信";
		}		


		$size = $product["size"];
		$sizename = "【官方】全国".$operatorname."号码:国内通用流量叠加包  ".$size."M";
		//商品号
		$product_id = $data['pay_order_code'];
		//$this->qrcodepay($user_type,$user_id,$product_id);
		$this->totalpayment($user_type,$user_id,$product_id,$discount_money);

        $role = "/Application/Sdk/View/WebPayment/";
        $this -> assign("discount_money", $discount_money);
        $this -> assign("role", $role);
        $this -> assign("pid", $pid);
        $this -> assign("pay_order_id", $pay_order_id);
        $this -> assign("phone", $phone);
        $this -> assign("pay_order_code", $product_id);
        $this -> assign("sizename", $sizename);


        //生成密钥
		if(empty($recharge_sources))
		{
			$keycode = $this->localencode($user_type.",".$user_id);
		}
		else
		{
			$keycode = $this->localencode($user_type.",".$user_id.",".$recharge_sources);
		}
        $this -> assign("keycode", $keycode);

		$this->display("prepaid");
	}

	public function wx_index($user_type, $user_id, $recharge_sources) {
		//版本号更新css js更新
 		$this->assign('version_number', C('VERSION_NUMBER'));
		

		$recharge_sourceskey = $user_type . $user_id;
		cookie($recharge_sourceskey, $recharge_sources);
		if (empty($user_type)) {
			$this -> error("信息错误");
		}

		if ($user_type == 1) {
			$data['user_type'] = 1;
			$data['proxy_id'] = $user_id;
		} else {
			$data['user_type'] = 2;
			$data['enterprise_id'] = $user_id;
		}
		$dataOut = M('user_set') -> where($data) -> find();
		$consumer_phone = $dataOut['consumer_phone'];

        $role = "/Application/Sdk/View/WebPayment/";
        $this -> assign("user_type", $user_type);
        $this -> assign("user_id", $user_id);
        $this -> assign("role", $role);
        $this -> assign("consumer_phone", $consumer_phone);
		
		$explanation = $dataOut["pc_explanation"];
		
		if(empty($explanation))
		{
			
			$explanation = '1、为什么我在充值的时候，提示我“该号码不可充流量”？
根据相关规定，对于部分如非3G号码/欠费/非实名制/运营商黑名单用户，暂时不能使用流量充值服务。对于实名制等业务的办理方法，建议联系归属地运营商。

2、为什么我充值后，使用流量还被运营商扣了额外的费用？
充值完成后，第三方中间服务商可能因为网络原因没有及时将充值电子订单发送至运营商，运营商对您的充值情况不知情。运营商会在收到第三方服务方订单后为您充值，并在成功后为您发送到账短信。请确保您在收到到账短信后再使用流量，以防出现流量包之外的费用。出现流量不到账的时候，建议您先参考信息5，确认您的订单情况。

3、充值的流量可以漫游吗?
充值时请关注页面提示，如果显示“全国可用”表示可以全国漫游。

4、充值的流量有效期是多长?
大部分省份运营商充值流量当月有效，月底失效，部分面额30天内有效及三个月有效，灵活账期用户月结日失效。请以短信通知及在运营商处查询的信息为准。

5、我充值后怎么查看是否到账?
充值后一般10分钟-30分钟内流量会到账，同时会收到运营商官方号码（10086、10010、10000）发来的短信通知，如果收到通知即到到账。
也有部分情况（每个月的5号之前、每个月最后两天）由于运营商BOSS系统延时，会收不到短信（或短信通知较晚）。
出现上述情况时可以有两种方式可明确是否到账：
第一、直接致电运营商官方客服查询即可。
第二、登陆运营商官方网站查询即可。

6、流量充错号码怎么办?
非常抱歉，充错号码后运营商（移动、联通、电信）是不会办理退款的。 由于充值成功后，交易就已经完成，运营商不会将已经充上的流量退还给供货商，所以我们也无法给您办理退款。您可以选择如下几种方式尝试弥补损失： 1、联系实际充值的号码机主，与对方协商是否愿意为此补偿您的流量； 2、联系运营商客服（移动10086、联通10010、电信10000），咨询是否能够退还已经充值成功的流量； 给您带来的不便，尽请谅解！并希望您在下次操作的时候注意核对号码是否正确，谢谢您的支持！

7、充值失败后，为什么我没有收到退款?
通常情况下，充值失败时我们会立即为您办理退款，如果您使用银行卡或者零钱支付，退款会立即退回微信钱包。信用卡退款时间可能会较长，您可以直接致电银行或登录网银查看退款情况。

8、为什么我的号码一直充值失败？
由于联通存在每个面额每个月充值5次的限制，充值超过5次（不限微信平台）将会出现失败退款。建议您充值其他面额的流量。
另外，号码欠费、套餐互斥、非实名认证、运营商黑名单等原因也会导致充值失败退款。

9、充值流量后能取消吗？
不可以。流量充值接近实时交易，支付完成后，交易系统会在数秒中向运营商发起充值请求并且充值到账。充值中的订单也会锁定，无法进行资金回退交易。您需要在充值前确定需要充值流量。';
		}
//		var_dump($explanation);

		$explanation=str_replace("\n", "<br/>",$explanation);
		$pc_notice = $dataOut["pc_notice"];
		$pc_notice=str_replace("\n", "<br/>",$pc_notice);
		
		$this->assign("explanation",$explanation);
		$this->assign("pc_notice",$pc_notice);
        $this -> display("index");
	}

	public function GetFlowProtuct() {
		$phoneNumber = I("phone");
		$user_type = I("user_type");
		$user_id = I("user_id");
		$submiturl = gethostwithhttp() . "/index.php/Sdk/Api/check_mobile";
		$pd = array('phone' => $phoneNumber, 'user_type' => $user_type, 'user_id' => $user_id);
		$rt = https_request($submiturl, $pd);
		echo $rt;
	}

	//统一下单
	public function totalpayment($user_type,$user_id,$product_id,$money)
	{
		$config = $this -> getconfig($user_type, $user_id);
		//appid
		$appid = $config["APPID"];
		//商户号
		$mch_id = $config["MCHID"];
		//设备号
		$device_info = "WEB";
		//随机字符串
		$nonce_str = $this->createNonceStr();
		//时间轴
		$time_stamp = time();
		//商品描述
		$body = "在线流量充值";
		$input = new \WxPayUnifiedOrder;
		$input -> SetBody("微信支付");
		$input -> SetAttach("微信支付");
		$input -> SetOut_trade_no($product_id);
		$input -> SetTotal_fee($money*100);
		$input -> SetTime_start(date("YmdHis"));
		$input -> SetTime_expire(date("YmdHis", time() + 600));
		$input -> SetGoods_tag("test");
		$url = gethostwithhttp() . "/index.php/Sdk/WebPayment/notify";
		$input -> SetNotify_url($url);
		$input -> SetTrade_type("NATIVE");
		$input-> SetProduct_id("123456789");


		$notify = new \NativePay;
		$result = $notify->GetPayUrl($input,$config);
		$qrurl = $result["code_url"];
		//二维码生成链接
		$src = 'http://pan.baidu.com/share/qrcode?w=150&h=150&url='.$qrurl;
		$this->assign("qrurl",$src);
	}

	public function notify() {
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		$res = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		$pre = M('pay_order') -> where(array('pay_order_code' => $res['out_trade_no'])) -> find();
		$user_type = $pre["user_type"];
		if ($user_type == 1) {
			$user_id = $pre['proxy_id'];
		} else {
			$user_id = $pre['enterprise_id'];
		}

		$config = $this -> getconfig($user_type, $user_id);

		$notify = new \PayNotifyCallBack($config);
		$result = $notify -> Handle(false);
		S('resultwx', $result);
		$pre_deal = M('pay_order') -> where(array('pay_order_code' => $result['out_trade_no'], 'pay_status' => 2)) -> find();
		if ($pre_deal) {
			$notify = new \WxPayNotify();
			$notify -> SetReturn_code('SUCCESS');
			$notify -> SetReturn_msg('OK');
			$xml = $notify -> ToXml();
			\WxpayApi::replyNotify($xml);
			S('success', 1);
		} else {
			if ($result['result_code'] == 'SUCCESS') {
				
				//下单
				$r = M('pay_order') -> where(array('pay_order_code' => $result['out_trade_no'])) -> find();
				$mobile = $r['mobile'];
				$product_id = $r['product_id'];
				$data['pay_type'] = 4;
				$data['pay_status'] = 2;
				$data['pay_date'] = date("Y-m-d H:i:s", time());
				$data['number'] = $result['transaction_id'];
				M('pay_order') -> where(array('pay_order_code' => $result['out_trade_no'])) -> save($data);
				//下单
				$this->pc_recharge($result['out_trade_no']);
			} else {
				S('success', 0);
			}
			/* $notify = new \PayNotifyCallBack();
			 $notify->Handle(false);*/

		}
	}

	private function createNonceStr($length = 16) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}


	private function CheckPacketMsg($packet_msg) {
		if (empty($packet_msg) || !is_array($packet_msg)) {
			$this -> ReturnJson(false, '未传入流量包信息！');
		}

		$packet_msg = D('ChannelProduct') -> channelproductinfo($packet_msg['packet_id']);
		if (empty($packet_msg)) {
			$this -> ReturnJson(false, '流量包信息有误！');
		} else {
			return $packet_msg;
		}
	}

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
		return $config;
	}

	public function limitmoney() {
		$role = "/Application/Sdk/View/WxFlowPayment/";
		$this -> assign("role", $role);
        $imgPath = $role."images/flowisempty.png";
        $this -> assign("imgpath", $imgPath);
		$this -> display("limitmoney");
	}
	public function pc_get_alipay($user_type,$user_id){
		if($user_type==1){
			$where['proxy_id']=$user_id;
		}else{
			$where['enterprise_id']=$user_id;
		}
		$result = M("user_set") -> where($where) -> find();
		$data['seller_email']=$result['pc_alipay_account'];
		$data['partner'] = $result['pc_alipay_partner'];   //这里是你在成功申请支付宝接口后获取到的PID；
        $data['key'] = $result['pc_alipay_key'];  //这里是你在成功申请支付宝接口后获取到的Key
        $data['sign_type'] = strtoupper('MD5');
        $data['input_charset'] = strtolower('utf-8');
        $data['cacert'] = getcwd() . '\\cacert.pem';
        $data['transport'] = 'http';
		return $data;
	}
	public function pc_alipay(){
		$pay_order_id=trim(I("pay_order_id"));
		$where['pay_order_id']=$pay_order_id;//获取订单号
		$order=M("pay_order")->where($where)->find();//获取订单信息
		if($order){
			if($order['user_type']==1){
				$user_id=$order['proxy_id'];
			}else{
				$user_id=$order['enterprise_id'];
			}

            //信息加密
			     //生成密钥
			$user_type = $order['user_type'];
			$recharge_sources = $order['$recharge_sources'];
			if(empty($recharge_sources))
			{
				$keycode = $this->localencode($user_type.",".$user_id);
			}
			else
			{
				$keycode = $this->localencode($user_type.",".$user_id.",".$recharge_sources);
			}
			
            //信息加密

			$data=$this->pc_get_alipay($order['user_type'],$user_id);
			$payment_type = "1"; //支付类型 //必填，不能修改
			$notify_url = gethostwithhttp()."/index.php/Sdk/webPayment/notify_url"; //服务器异步通知页面路径
			$return_url = gethostwithhttp()."/index.php/Sdk/webPayment/paysuccess?".$keycode; //页面跳转同步通知页面路径
			$seller_email = $data['seller_email'];//卖家支付宝帐户必填
			$out_trade_no = $order['pay_order_code'];//商户订单号 通过支付页面的表单进行传递，注意要唯一！
			$subject = "流量充值";  //订单名称 //必填 通过支付页面的表单进行传递
			$total_fee = $order['discount_price'];   //付款金额  //必填 通过支付页面的表单进行传递
			$body = "流量充值";  //订单描述 通过支付页面的表单进行传递
			$show_url = null;  //商品展示地址 通过支付页面的表单进行传递
			$anti_phishing_key = "";//防钓鱼时间戳 //若要使用请调用类文件submit中的query_timestamp函数
			$exter_invoke_ip = get_client_ip(); //客户端的IP地址
			//构造要请求的参数数组，无需改动
			$parameter = array(
				"service" => "create_direct_pay_by_user",
				"partner" => $data['partner'],
				"payment_type" => $payment_type,
				"notify_url" => $notify_url,
				"return_url" => $return_url,
				"seller_email" => $seller_email,
				"out_trade_no" => $out_trade_no,
				"subject" => $subject,
				"total_fee" => $total_fee,
				"body" => $body,
				"show_url" => $show_url,
				"anti_phishing_key" => $anti_phishing_key,
				"exter_invoke_ip" => $exter_invoke_ip,
				"_input_charset" => trim(strtolower($data['input_charset']))
			);
			//建立请求
			$alipaySubmit = new \AlipaySubmit($data);
			$html_text = $alipaySubmit->buildRequestForm($parameter, "post", "确认");
			header("Content-type: text/html; charset=utf-8");
			echo $html_text;
		}else{
			exit();
		}
	}

	//获取支付状态
	public function get_paystatue()
	{
		$pay_order_id = I("pay_order_id");
		$where['pay_order_id']=$pay_order_id;//获取订单号
		$order=M("pay_order")->where($where)->find();//获取订单信息
		if ($order["pay_status"] == 1) {
			//未支付的订单
			$paystatue = 1;
		}
		else
		{
			$paystatue = 2;
		}
		$array = array('status' => 1, 'msg' => "查询成功", 'data' => $paystatue);
		$this -> ajaxReturn($array);
	}
	
	//支付成功
	public function paysuccess()
	{
		$rsaKey = $_SERVER["QUERY_STRING"];
		$tmp = stripos($rsaKey, "&");
		if ($tmp != false) {
			$rsaKey = substr($rsaKey, 0, $tmp);
		}

		$url = gethostwithhttp() . "/index.php/Sdk/WebPayment/aindex?".$rsaKey;
        $this -> assign("url", $url);
        $role = "/Application/Sdk/View/WebPayment/";
        $this -> assign("role", $role);
		$this->display("success");
	}
	//支付宝回调
	public function notify_url(){
		$pre= M('pay_order')->where(array('pay_order_code'=>$_POST['out_trade_no']))->find();
		$user_type=$pre["user_type"];
		if($user_type==1){
			$user_id=$pre['proxy_id'];
		}else{
			$user_id=$pre['enterprise_id'];
		}
		$alipay_config=$this->pc_get_alipay($user_type,$user_id);
		$alipayNotify = new \AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();
		if ($verify_result) {
			$out_trade_no = $_POST['out_trade_no'];      //商户订单号
			$trade_no = $_POST['trade_no'];          //支付宝交易号
			if ($_POST['trade_status'] == 'TRADE_FINISHED') {
				$this->payOver($out_trade_no, $trade_no);
			} else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
				$this->payOver($out_trade_no, $trade_no);
			}
			//其他状态不处理
			echo "success";        //请不要修改或删除
		} else {
			//验证失败
			echo "fail";
		}
	}
	//获取支付宝回调修改数据库数据
	private function payOver($order_id, $trade_no) {
		if (empty($order_id)) {
			exit('异常错误：支付宝未返回订单号');
		}
		$payOrrdermap = array();
		$payOrrdermap['pay_order_code']=$order_id;
		$middle = M('pay_order')->where($payOrrdermap)->find();
		if(empty($middle)) {
			write_error_log(array(__METHOD__.':'.__LINE__, '订单号无数据,pay_order_id=='.$order_id));
			write_error_log('异常错误：支付宝返回订单号不存在');
		}
		switch (intval($middle['pay_status'])) {
			case 1: //未支付
				$order = array(
					'pay_type'    => 5,//表示pc端支付宝
					'number'        => $trade_no,
					'pay_status'    => 2,
					'pay_date'      => date('Y-m-d H:i:s'),
				);
				M('pay_order')->where("pay_order_code='{$order_id}'")->save($order);
				$this->pc_recharge($order_id);
//		       	$fp = fopen("access_token.json","a");
//				fwrite($fp, $order_code);
//				fclose($fp);
				break;
			case 2: //已支付
				break;
		}
	}

	//向流量服务器下单
	private function pc_recharge($pay_order_code ) {
		$pre_deal = M('pay_order') -> where(array('pay_order_code' => $pay_order_code, 'pay_status' => 2)) -> find();
		if ($pre_deal) {
			if ($pre_deal['order_code'] > 0) {
				exit();
			}
		} else {
			exit();
		}
		if($pre_deal['user_type']==1){
			$map['proxy_id']=$pre_deal['proxy_id'];
		}else{
			$map['enterprise_id']=$pre_deal['enterprise_id'];
		}
		$sys_api = M("sys_api") -> where($map) -> find();
		$phone =$pre_deal['mobile'];
		$map1['product_id'] = $pre_deal['product_id'];
		$products = $this->CheckPacketMsg($map1);//检查流量包
		$size = $products['size'];
		$submiturl = C("URL");
		$range = $products["product_type"];
		$account = $sys_api['api_account'];
		$api_key = $sys_api['api_key'];
		$timeStamp = time();
		$pd = array('account' => $account, 'action' => 'Charge', 'phone' => $phone, 'range' => $range, 'size' => $size, 'timeStamp' => $timeStamp, );
		$pre_str = "{$api_key}account={$account}&action=Charge&phone={$phone}&range={$range}&size={$size}&timeStamp={$timeStamp}{$api_key}";
		$pd['sign'] = md5($pre_str);
		$rt = https_request($submiturl, $pd);
		$ret = json_decode($rt, true);
		$orderID = $ret['orderID'];
		if(!$orderID)
		{
			$msg = $ret['respCode'].",".$ret['respMsg'];
			$order['remark'] = $msg;
		}
		$order['order_code'] = $orderID;
		M('pay_order') -> where(array('pay_order_code' =>$pay_order_code)) -> save($order);
	}
}
?>