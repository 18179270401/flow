<?php
namespace Sdk\Controller;
use Think\Controller;
class WxFlowPaymentController extends Controller {

	public function _initialize() {
		//微信支付
		Vendor("WxPayR.Api");
		Vendor("WxPayR.JsApiPay");
		Vendor("WxPayR.WxPayConfig");
		Vendor('WxPayR.Notify');
		Vendor('WxPayR.Native_notify');
		Vendor('WxPayR.WxPayData');
		Vendor('WxPayR.Exception');
		Vendor('WxPayR.NativePay');
		//支付宝支付
		Vendor("WapPay.AlipayDataDataserviceBillDownloadurlQueryContentBuilder");
		Vendor("WapPay.AlipayTradeCloseContentBuilder");
		Vendor("WapPay.AlipayTradeFastpayRefundQueryContentBuilder");
		Vendor("WapPay.AlipayTradeQueryContentBuilder");
		Vendor("WapPay.AlipayTradeRefundContentBuilder");
		Vendor("WapPay.AlipayTradeService");
		Vendor("WapPay.AlipayTradeWapPayContentBuilder");
		Vendor("WapPay.ContentBuilder");
	}

	public function aindex() {
		$rsaKey = $_SERVER["QUERY_STRING"];
		$tmp = stripos($rsaKey, "&");
		if ($tmp != false) {
			$rsaKey = substr($rsaKey, 0, $tmp);
		}

		$strArray = localdecode($rsaKey);
		$InfoArray = explode(",", $strArray);

		$user_type = $InfoArray[0];
		$user_id = $InfoArray[1];
		$recharge_sources = $InfoArray[2];
		$this -> wx_index($user_type, $user_id, $recharge_sources);

		//用户点击链接监控 by lv ($type, $phone, $product_name,$user_type,$user_id)
		$this->inputuserdata(1,0,"",$user_type,$user_id);
	}

	//入口（另外一个）
	public function index() {
		$user_type = I("user_type");
		$user_id = I("user_id");
		$recharge_sources = I("recharge_sources");
		$this -> wx_index($user_type, $user_id, $recharge_sources);
	}

	//逻辑处理
	public function wx_index($user_type, $user_id, $recharge_sources) {
		//版本号更新css js更新
 		$this->assign('version_number', C('VERSION_NUMBER'));
		 
		//将信息存入cooke
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

		$pub_notice = $dataOut['pub_notice'];
		$pub_notice=str_replace("\n", "<br/>",$pub_notice);
        $this -> assign("pub_notice", $pub_notice);

		//选择通用模版类型
		$template_type = $dataOut["template_type"];
		$account_id = $dataOut["account_id"];




		//读取轮播图路径
		$this->get_rolooppic($account_id);

		date_default_timezone_set('PRC');
		if ($this -> is_weixin()) {
			$config = $this -> getconfig($user_type, $user_id);
			$openidkey = $user_type . $user_id . "openid";
			$openId = cookie($openidkey);
			if (empty($openId)) {
				$tools = new \JsApiPay($config);
				$openId = $tools -> GetOpenid();
				cookie($openidkey, $openId);
			}

			//判断用户是是否需要关注才能充值
			$search["user_set_id"] = $dataOut['account_id'];
			//查看折扣类型设置
       		$usescece =M('user_sceneset')
            ->where($search)
            ->find();
			$follow = $usescece["follow_type"];
			if($follow == 2)
			{
				$this->getlocaluserinfo($config,$openId);
			}
			//验证是否需要关注

			$this -> assign("openid", $openId);
            
            $role = "/Application/Sdk/View/WxFlowPayment/";
            $this -> assign("user_type", $user_type);
            $this -> assign("user_id", $user_id);
            $this -> assign("role", $role);
            $this -> assign("consumer_phone", $consumer_phone);
            
            $this -> get_shareuser($config);
            $this -> set_shareinfo($user_type, $user_id,$recharge_sources);
			// //原通用版
            // //$this -> display("index");

			// //新通用版
            // // $this -> display("gdindex");

			// //易道版
            // $this -> display("gdindex");
		}
        else
        {
            $this -> assign("consumer_phone", $consumer_phone);
			//修正为非微信打开时处理
            $role = "/Application/Sdk/View/WxFlowPayment/";
            $this -> assign("user_type", $user_type);
            $this -> assign("user_id", $user_id);
            $this -> assign("role", $role);
            // $this -> display("special");

			
			//
            // $role = "/Application/Sdk/View/WxFlowPayment/";
            // $imgPath = $role."images/flowunwx.png";
            // $this -> assign("role", $role);
            // $this -> assign("imgpath", $imgPath);
            // $this -> display("limitmoney");
            // $this -> display("index");
        }
		//创建用户。创建表。给表加数据10W 查询

		switch($template_type)
		{
				case 1:
				{
					//通用版(橙色)
					$this -> display("WxFlowPayment/gdn/index");
				}
				break;
				case 2:
				{
					//$this -> display("gdindex");
					//公告版
					$this -> display("gdindex");
				}
				break;
				case 3:
				{
					//原通用版(红色)
					$this -> display("index");
				}
				break;
				case 4:
				{
					//春节版
					$this -> display("special");
				}
				break;
				case 5:
				{
					//易到定制版
					$this -> display("WxFlowPayment/yd/ydindex");
				}
				break;
				default:
				{
					//$this -> display("WxFlowPayment/yd/ydindex");
					//无公告版
					$this -> display("WxFlowPayment/gdn/index");
				}
				break;
		}
	}

	public function getlocaluserinfo($result,$openid){
		$pd = "";

		$APPID = $result['APPID'];
		$APPSECRET = $result['APPSECRET'];
		
		$submiturl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$APPID."&secret=".$APPSECRET;
		$rt = https_request($submiturl);
		$obj = json_decode($rt,true); 
		$accesstoken = $obj['access_token'];
		
		
		$submiturl = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$accesstoken."&openid=" .$openid."&lang=zh_CN";
	
		$retrnrt = $this->httpGet($submiturl);
		$retrnrtobj = json_decode($retrnrt,true);
		$subscribe = $retrnrtobj['subscribe'];
		if($subscribe == 1)
		{
	        // $newstr = substr($retrnrtobj['headimgurl'],0,strlen($retrnrtobj['headimgurl'])-1); 
			
			// $headimgurl = $newstr."64";
			// $nickname = $retrnrtobj['nickname'];
			
	       	// $this->assign("headimgurl",$headimgurl);
	        // $this->assign("nickname",$nickname);
				
	        // cookie('headimgurl',$headimgurl);
	        // cookie('nickname',$nickname);
		}
		else
		{
			$submiturl = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$accesstoken;
			$pd = '{"action_name":"QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 123}}}';
      
			$rt = https_request($submiturl,$pd);
			$obj = json_decode($rt,true);
		
			$qrurl = $obj['url'];
			$src = 'http://pan.baidu.com/share/qrcode?w=150&h=150&url='.$qrurl;
			
			//公众号二维码
	 	    $this->assign("qrurl",$src);
			//让其必须关注
			//$this->error("活动已经结束！");
		    $this->display("WxFlowPayment/Flowunfollow/index");
            exit();
			
			//$this->get_userinfo($result);
		}
    }

	//读取轮播图
	private function get_rolooppic($user_set_id)
	{
         	$usescece = M('user_sceneset')->where("user_set_id={$user_set_id}")->find();
		    $jsonuser_headpics = $usescece["user_headpics"];
            //json解析
            $user_headpics = json_decode($jsonuser_headpics);
			$piclist = array();
            for($count = 0;$count<3;$count++)
            {
                $picitem = $user_headpics[$count];
                $picitem = object_array($picitem);
                $goroundimgpic = $picitem["goroundimgpic"];
                $goroundimg = $picitem["goroundimg"];
				if($goroundimg == 2 && !empty($goroundimgpic))//如果当前为显示，并且有图，
				{
					$piclist[] = $goroundimgpic;
				}
            }
			if(empty($piclist))
			{
				//如果都为空。则默认显示为充值图片
				$piclist[] = 'http://'.$_SERVER['HTTP_HOST']."/Application/Sdk/View/WxFlowPayment/gdn/images/banner.png";
			}
            $this->assign("piclist",json_encode($piclist));
	}

	//充值说明
	public function showTip() {
		$user_type = I('user_type');
		$user_id = I('user_id');
		if ($user_type == 1) {
			$data['user_type'] = 1;
			$data['proxy_id'] = $user_id;
		} else {
			$data['user_type'] = 2;
			$data['enterprise_id'] = $user_id;
		}
		
		$dataOut = M('user_set') -> where($data) -> find();
		$explanation = $dataOut['explanation'];
		$consumer_phone = $dataOut['consumer_phone'];
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
		$explanation=str_replace("\n", "<br/>",$explanation);
		$role = "/Application/Sdk/View/WxFlowPayment/";
		$this -> assign("role", $role);
		$this -> assign("explanation", $explanation);
		$this -> assign("consumer_phone", $consumer_phone);
		$this -> display("WxFlowPayment/index_tip");
	}

	//是否为微信
	function is_weixin() {
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
			return true;
		}
		return false;
	}

	//获取产品包
	public function GetFlowProtuct() {
		$phoneNumber = I("phone");
		$user_type = I("user_type");
		$user_id = I("user_id");
		$product = I("product");


		if($phoneNumber != "18507085074")
		//用户点击链接监控 by lv ($type, $phone, $product_name,$user_type,$user_id)
			$this->inputuserdata(2,$phoneNumber,"",$user_type,$user_id);


		$submiturl = gethostwithhttp() . "/index.php/Sdk/Api/check_mobile";
		$pd = array('phone' => $phoneNumber, 'user_type' => $user_type, 'user_id' => $user_id,"p_ty"=>"$user_id","product"=>$product);
		$rt = https_request($submiturl, $pd);
		echo $rt;
	}

	//＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊
	//微信扫码支付
	public function scancode()
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
		//用户点击链接监控 by lv ($type, $phone, $product_name,$user_type,$user_id)
		$product_name = $product["product_name"];
		$this->inputuserdata(4,$phone,$product_name,$user_type,$user_id);


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
		// $discount_number = $discount["discount_number"];
		// if (empty($discount_number)) {
		// 	//查询全国
		// 	$map['province_id'] = 1;
		// 	$map['operator_id'] = $result["operator_id"];
		// 	$discount = M("discount") -> where($map) -> find();
		// 	$discount_number = $discount["discount_number"];
		// 	if (empty($discount_number)) {
		// 		$discount_number = 1;
		// 	}
		// }
		//$data['deduct_price'] = $discount_number * $packet_msg['price'];

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

        $role = "/Application/Sdk/View/WxFlowPayment/";
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
			$keycode = localencode($user_type.",".$user_id);
		}
		else
		{
			$keycode = localencode($user_type.",".$user_id.",".$recharge_sources);
		}
        $this -> assign("keycode", $keycode);

		$this->display("qrcode");
	}

	//生成二维码统一下单
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
		$input -> SetTotal_fee($money*100);//
		$input -> SetTime_start(date("YmdHis"));
		$input -> SetTime_expire(date("YmdHis", time() + 600));
		$input -> SetGoods_tag("test");
		$url = gethostwithhttp() . "/index.php/Sdk/WxFlowPayment/notify";
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

		$url = gethostwithhttp() . "/index.php/Sdk/WxFlowPayment/aindex?".$rsaKey;
        $this -> assign("url", $url);
        $role = "/Application/Sdk/View/WxFlowPayment/";
        $this -> assign("role", $role);
		$this->display("success");
	}
	//＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊

	//获取本地ip
	public function Getclient_ip()
	{
		$cip = "unknow";
		if($_SERVER['REMOTE_ADDR'])
		{
			$cip = $_SERVER['REMOTE_ADDR'];
		}
		else if(getenv('REMOTE_ADDR'))
		{
			$cip = $getenv['REMOTE_ADDR'];
		}
		return $cip;
	}

	//微信外跳转微信支付
	public function appwxpay()
	{
		//微信app支付
			$phone = I("phone");
		$pid = I("pid");
		$user_type = I("user_type");
		$user_id = I("user_id");
		if (empty($pid)) {
			die();
		}
		$pack['packet_id'] = $pid;
		$product = $this -> CheckPacketMsg($pack);
		//检查流量包
		if (!$product) {
			die();
		}

		//获取来源key
		$recharge_sourceskey = $user_type . $user_id;
		$recharge_sources = cookie($recharge_sourceskey);

		$pay_order_code = apply_number2($phone, 6);
	
		//并将价格录入数据库 手机号，企业类型，企业id，产品id，下单号,备注
		$discount_money = $this->adddiscount_money($phone,$user_type,$user_id,$product,$pay_order_code,$recharge_sources);

		date_default_timezone_set('PRC');
		//①、获取用户openid
		$config = $this -> getconfig($user_type, $user_id);
		$tools = new \JsApiPay($config);
		//②、统一下单
		$input = new \WxPayUnifiedOrder;
		$input -> SetBody("微信支付");
		$input -> SetAttach("微信支付");

		$cip =  $this -> Getclient_ip();
		//存入本机ip地址
		$input -> SetSpbill_create_ip($cip);
		$input -> SetOut_trade_no($pay_order_code);
		//$input->SetTotal_fee($money);
		$input -> SetTotal_fee($discount_money * 100);
		$input -> SetTime_start(date("YmdHis"));
		$input -> SetTime_expire(date("YmdHis", time() + 600));
		$input -> SetGoods_tag("test");
		$url = gethostwithhttp() . "/index.php/Sdk/WxFlowPayment/notify";
		$input -> SetNotify_url($url);
		$input -> SetTrade_type("MWEB");
		$order = \WxPayApi::unifiedOrder($input, 6, $config);
		var_dump($order);
		//http://localhost/index.php/Sdk/WxFlowPayment/aindex?RkBNRA==
		$jsApiParameters = $tools -> GetJsApiParameters($order);
		$this -> assign("jsApiParameters", $jsApiParameters);
		$role = "/Application/Sdk/View/WxFlowPayment/";
		$this -> assign("role", $role);



		if(empty($recharge_sources))
		{
			$keycode = localencode($user_type.",".$user_id);
		}
		else
		{
			$keycode = localencode($user_type.",".$user_id.",".$recharge_sources);
		}
		$url = gethostwithhttp() . "/index.php/Sdk/WxFlowPayment/aindex?" . $keycode;
		$this -> assign("url", $url);
		$this -> display("wx");
	}
	
	//微信内点击支付
	public function wxpay() {

		$phone = I("phone");
		$pid = I("pid");
		$user_type = I("user_type");
		$user_id = I("user_id");
		if (empty($pid)) {
			die();
		}
		$pack['packet_id'] = $pid;
		$product = $this -> CheckPacketMsg($pack);
		//检查流量包
		if (!$product) {
			die();
		}

		//获取来源key
		$recharge_sourceskey = $user_type . $user_id;
		$recharge_sources = cookie($recharge_sourceskey);

		$pay_order_code = apply_number2($phone, 6);
	
		$openId = I("openid");
		//并将价格录入数据库 手机号，企业类型，企业id，产品id，下单号,备注
		$discount_money = $this->adddiscount_money($phone,$user_type,$user_id,$product,$pay_order_code,$recharge_sources,$openId);

		date_default_timezone_set('PRC');
		//①、获取用户openid
		$config = $this -> getconfig($user_type, $user_id);
		$tools = new \JsApiPay($config);
		//$openId=$tools->GetOpenid();
		//②、统一下单
		$input = new \WxPayUnifiedOrder;
		$input -> SetBody("微信支付");
		$input -> SetAttach("微信支付");
		$input -> SetOut_trade_no($pay_order_code);
		//$input->SetTotal_fee($money);
		$input -> SetTotal_fee($discount_money * 100);
		$input -> SetTime_start(date("YmdHis"));
		$input -> SetTime_expire(date("YmdHis", time() + 600));
		$input -> SetGoods_tag("test");
		$url = gethostwithhttp() . "/index.php/Sdk/WxFlowPayment/notify";
		$input -> SetNotify_url($url);
		$input -> SetTrade_type("JSAPI");
		$input -> SetOpenid($openId);
		$order = \WxPayApi::unifiedOrder($input, 6, $config);
		$jsApiParameters = $tools -> GetJsApiParameters($order);
		$this -> assign("jsApiParameters", $jsApiParameters);
		$role = "/Application/Sdk/View/WxFlowPayment/";
		$this -> assign("role", $role);



		if(empty($recharge_sources))
		{
			$keycode = localencode($user_type.",".$user_id);
		}
		else
		{
			$keycode = localencode($user_type.",".$user_id.",".$recharge_sources);
		}
		$url = gethostwithhttp() . "/index.php/Sdk/WxFlowPayment/aindex?" . $keycode;
		$this -> assign("url", $url);
		$this -> display("wx");
	}

	//微信支付回调
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
					$this->payOrder($result['out_trade_no'], $result['transaction_id'],2,$user_type,$user_id);
			} else {
				S('success', 0);
			}
			/* $notify = new \PayNotifyCallBack();
			 $notify->Handle(false);*/

		}
	}

	//调取分享权限
	public function get_shareuser($config) {
		$APPSECRET = $config['APPSECRET'];
		$APPID = $config['APPID'];
		//获取ticketcode
		$submiturl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $APPID . "&secret=" . $APPSECRET;
		$rt = https_request($submiturl);
		$obj = json_decode($rt, true);
		$accesstoken = $obj['access_token'];

		$submiturl = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=" . $accesstoken;

		//$submiturl = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$accesstoken."&openid=" .$Openid. "&lang=zh_CN";
		//$retrnrt = https_request($submiturl);
		$retrnrt = $this -> httpGet($submiturl);
		$retrnrtobj = json_decode($retrnrt, true);
		$jsapi_ticket = $retrnrtobj['ticket'];

		$nonceStr = $this -> createNonceStr();
		$timestamp = time();
		$localurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		// 这里参数的顺序要按照 key 值 ASCII 码升序排序
		$string = "jsapi_ticket=" . $jsapi_ticket . "&noncestr=" . $nonceStr . "&timestamp=" . $timestamp . "&url=" . $localurl;
		$signature = sha1($string);

		$this -> assign("APPID", $APPID);
		//APPID
		$this -> assign("nonceStr", $nonceStr);
		//随即串
		$this -> assign("timestamp", $timestamp);
		//时间戳
		$this -> assign("signature", $signature);
		//字符串

	}

	//计算出折扣价格
	//并将价格录入数据库 手机号，企业类型，企业id，产品，下单号,备注
	private function adddiscount_money($phone,$user_type,$user_id,$product,$pay_order_code,$recharge_sources,$openid = null)
	{
		//点击包属于省内还是全国 1为全国 2为省内
		$productprovince_id = $product["province_id"];

		$data = array();
		//订单号码
		$data['pay_order_code'] = $pay_order_code;
		if ($user_type == 1) {
			$data['user_type'] = 1;
			$data['proxy_id'] = $user_id;
			$discountdata["proxy_id"] = $user_id;
		} else {
			$data['user_type'] = 2;
			$data['enterprise_id'] = $user_id;
			$discountdata["enterprise_id"] = $user_id;
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



		//商品打折
		$result = CheckMobile($phone);
		//1为微信 2为app
		$discountdata['discount_type'] = 1;
		// //通过省份
		// $discountdata['province_id'] = $result["province_id"];
		//通过运营商
		$discountdata['operator_id'] = $result["operator_id"];
		//如果选择产品包为全国产品包
		if($productprovince_id == 1 && $user_province_type == 1)
		{
			//全国折扣
			$discountdata['province_id'] = 1;
			$dicountsData = M('person_discount') -> where($discountdata) -> find();
			$dicountData = $dicountsData["charge_discount"];
			if ((float)$dicountData == 0 || empty($dicountData)) {
				$dicountData = 10;
			}
		}
		else
		{
			$discountdata['province_id'] = $result["province_id"];
			//省内产品包
			$dicountsData = M('person_discount') -> where($discountdata) -> find();
			$dicountData = $dicountsData["charge_discount"];
			if ((float)$dicountData == 0 || empty($dicountData)) {
					//如果选择为分省折扣
					if($user_province_type == 2)
					{
						//当用户没有设置当前城市折扣时取用全国折扣
						$discountdata['province_id'] = 1;
						$dicountsData = M('person_discount') -> where($discountdata) -> find();
						$dicountData = $dicountsData["charge_discount"];
					}
					if ((float)$dicountData == 0 || empty($dicountData)) {
						$dicountData = 10;
					}
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
		// //通过省份
		// $map['province_id'] = $result["province_id"];
		// //通过运营商
		// $map['operator_id'] = $result["operator_id"];
		// /////////////
		// //通过省份
		// $map['province_id'] = $result["province_id"];
		// //通过运营商
		// $map['operator_id'] = $result["operator_id"];
		// $discount = M("discount") -> where($map) -> find();
		// $discount_number = $discount["discount_number"];
		// if (empty($discount_number)) {
		// 	//查询全国
		// 	$map['province_id'] = 1;
		// 	$map['operator_id'] = $result["operator_id"];
		// 	$discount = M("discount") -> where($map) -> find();
		// 	$discount_number = $discount["discount_number"];
		// 	if (empty($discount_number)) {
		// 		$discount_number = 1;
		// 	}
		// }

		//新增点击支付。记录
		$this->inputuserdata(4,$phone,$product["product_name"],$user_type,$user_id);
		//$data['deduct_price'] = $discount_number * $packet_msg['price'];

		//将代理商价格录入购买记录
		$data['product_id'] = $product["product_id"];
		$data['mobile'] = $phone;
		$data['pay_type'] = 2;
		$data['price'] = $product['price'];
		$data['discount_price'] = $discount_money;
		$data['pay_status'] = 1;
		$data['order_date'] = date("Y-m-d H:i:s", time());
		$data['recharge_sources'] = $recharge_sources;
		if(!empty($openid))
		{
			$data['we_openid'] = $openid;
		}
		if (! M("pay_order") -> add($data)) {
			die();
		}
		return $discount_money;
	}

	private function createNonceStr($length = 16) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}

	private function httpGet($url) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 500);
		// 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
		// 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
		curl_setopt($curl, CURLOPT_URL, $url);

		$res = curl_exec($curl);
		curl_close($curl);

		return $res;
	}

	//设定分享内容
	public function set_shareinfo($user_type, $user_id,$recharge_sources) {

		$FlowProductTitle = "流量充值";
		$this -> assign("FlowProductTitle", $FlowProductTitle);
		//字符串

		$FlowProductdesc = "大家都来参加流量充值活动,更多折扣活动即将开展";
		$this -> assign("FlowProductdesc", $FlowProductdesc);
		//字符串

		//分享内容

		if(empty($recharge_sources))
		{
			$keycode = localencode($user_type.",".$user_id);
		}
		else
		{
			$keycode = localencode($user_type.",".$user_id.",".$recharge_sources);
		}
		$Link = gethostwithhttp() . "/index.php/Sdk/WxFlowPayment/aindex?" . $keycode;

		//$Link = 'http://'.$_SERVER['HTTP_HOST']."/index.php/Sdk/WxFlowPayment/index/user_type/".$user_type."/user_id/".$user_id;
		$this -> assign("Link", $Link);
		//字符串
		//http://sdk.liuliang.net.cn/Application/Sdk/View/FlowRed/images/Share_CheckRed.png
		$localimgUrl = 'http://' . $_SERVER['HTTP_HOST'] . "/Application/Sdk/View/FlowRed/images/icon_sendflow_background.png";

		$this -> assign("localimgUrl", $localimgUrl);
		//字符串
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

	//获取配置信息
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

	//流量不够提示
	public function limitmoney() {
		$role = "/Application/Sdk/View/WxFlowPayment/";
		$this -> assign("role", $role);
        $imgPath = $role."images/flowisempty.png";
        $this -> assign("imgpath", $imgPath);
		$this -> display("limitmoney");
	}

	//数据记录操作习惯
	private function inputuserdata($type, $phone, $product_name,$user_type,$user_id)
	{
		//1进入链接， 2输入号码。 3选择包型 。4点击支付。5完成支付
		$data['type'] = $type;
		$data['phone'] = $phone;
		$data['product_name'] = $product_name;

		if ($user_type == 1) {
			$data['user_type'] = 1;
			$data['proxy_id'] = $user_id;
			$data["enterprise_id"] = 0;
		} else {
			$data['user_type'] = 2;
			$data['proxy_id'] = 0;
			$data["enterprise_id"] = $user_id;
		}
		$data['create_date'] = date("Y-m-d H:i:s", time());
		M("sence_counts") -> add($data);
	}

	//支付宝支付
	function alipay()
	{
			$phone = I("phone");
			$pid = I("pid");
			$user_type = I("user_type");
			$user_id = I("user_id");

			if (empty($pid)) {
				die();
			}
			$pack['packet_id'] = $pid;
			$product = $this -> CheckPacketMsg($pack);
			//检查流量包
			if (!$product) {
				die();
			}
			//获取来源key
			$recharge_sourceskey = $user_type . $user_id;
			$recharge_sources = cookie($recharge_sourceskey);

			if(empty($recharge_sources))
			{
				$keycode = localencode($user_type.",".$user_id);
			}
			else
			{
				$keycode = localencode($user_type.",".$user_id.",".$recharge_sources);
			}
			//同步跳转
			$return_url = gethostwithhttp() . "/index.php/Sdk/WxFlowPayment/aindex?".$keycode;
			//异步通知地址
			$notify_url = gethostwithhttp() . "/index.php/Sdk/WxFlowPayment/notify_url";

			
			//商户订单号，商户网站订单系统中唯一订单号，必填
			$pay_order_code = apply_number2($phone, 6);
			//并将价格录入数据库 手机号，企业类型，企业id，产品id，下单号,备注
			$discount_money = $this->adddiscount_money($phone,$user_type,$user_id,$product,$pay_order_code,$recharge_sources);
		
			//支付宝配置信息
			$config=$this->wap_get_alipay($user_type,$user_id, $return_url, $notify_url);


			$province_id = $product["province_id"];
			//订单名称，必填
			if($province_id == 1)
			{
				$subject = "全国流量".$product["product_name"];
			}
			else
			{
				$subject = "省内流量".$product["product_name"];
			}
			//超时时间
			$timeout_express="1m";
			//商品描述，可空
			$body = "flowpay";

			$payRequestBuilder = new \AlipayTradeWapPayContentBuilder();
			$payRequestBuilder->setBody($body);
			$payRequestBuilder->setSubject($subject);
			$payRequestBuilder->setOutTradeNo($pay_order_code);
			//付款金额，必填
			$payRequestBuilder->setTotalAmount($discount_money);
			$payRequestBuilder->setTimeExpress($timeout_express);
			$payResponse = new \AlipayTradeService($config);
			$result=$payResponse->wapPay($payRequestBuilder,$return_url,$notify_url);
	}

	//支付宝异步支付回调
	public function notify_url() {

		//商户订单号
		$out_trade_no = $_POST['out_trade_no'];
		//支付宝交易号
		$trade_no = $_POST['trade_no'];

		$pre= M('pay_order')->where(array('pay_order_code'=>$out_trade_no))->find();
		$user_type=$pre["user_type"];
		if($user_type==1){
			$user_id=$pre['proxy_id'];
		}else{
			$user_id=$pre['enterprise_id'];
		}

		//设置回调地址
		//获取来源key
		$recharge_sourceskey = $user_type . $user_id;
		$recharge_sources = cookie($recharge_sourceskey);

		if(empty($recharge_sources))
		{
			$keycode = localencode($user_type.",".$user_id);
		}
		else
		{
			$keycode = localencode($user_type.",".$user_id.",".$recharge_sources);
		}
			//同步跳转
		$return_url = gethostwithhttp() . "/index.php/Sdk/WxFlowPayment/aindex?".$keycode;
			//异步通知地址
		$notify_url = gethostwithhttp() . "/index.php/Sdk/WxFlowPayment/notify_url";
		//获取支付宝配置
		$config=$this->wap_get_alipay($user_type,$user_id, $return_url, $notify_url);

		$arr=$_POST;
		$alipaySevice = new \AlipayTradeService($config); 
		$alipaySevice->writeLog(var_export($_POST,true));
		$result = $alipaySevice->check($arr);

		/* 实际验证过程建议商户添加以下校验。
		1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
		2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
		3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
		4、验证app_id是否为该商户本身。
		*/
		if($result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代

			
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
			
			//获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
			
			//交易状态
			$trade_status = $_POST['trade_status'];
			if($_POST['trade_status'] == 'TRADE_FINISHED') {

				//注意：
				//退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
				$this->payOrder($out_trade_no, $trade_no,7,$user_type,$user_id);
			}
			else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
				//付款完成后，支付宝系统发送该交易状态通知
				$this->payOrder($out_trade_no, $trade_no,7,$user_type,$user_id);
			}
			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
				
			echo "success";		//请不要修改或删除
				
		}else {
			//验证失败
			echo "fail";	//请不要修改或删除

		}
	}

	//支付宝回调2
	public function refund_url()
	{
					$fp = fopen("access_token.json", "a");
					fwrite($fp, json_encode("111111111111111111111111"));
					fclose($fp);
		$pre= M('pay_order')->where(array('batch_no'=>$_POST['out_request_no']))->find();
        if(empty($pre)){
            exit();
        }
        $user_type=$pre["user_type"];
        if($user_type==1){
            $user_id=$pre['proxy_id'];
        }else{
            $user_id=$pre['enterprise_id'];
        }

		//获取支付宝配置
		$config=$this->wap_get_alipay($user_type,$user_id, "", "");
		$arr=$_POST;
		$alipaySevice = new \AlipayTradeService($config); 
		$alipaySevice->writeLog(var_export($_POST,true));
		$result = $alipaySevice->check($arr);

		if($result) {//验证成功

            //检查是否之前已经接受过改批次的通知
            /*$batch_no = $_POST['batch_no'];
            $db_check = D('Refund');*/
            $order['refund_status'] = 2;//退款成功
            $order['batch_no']= $_POST['batch_no'];
            $re=M('pay_order')->where($order)->find();
            if (!empty($re)) {
                echo "success";
                exit();
            }
            $data['refund_status']=2;
            M('pay_order')->where(array('batch_no' => $_POST['batch_no']))->save($data);
            echo "success";        //请不要修改或删除
		}
		else
		{
            //验证失败
            echo "fail";//请不要修改或删除
		}
	}
	
	//获取支付宝支付相关信息
	public function wap_get_alipay($user_type,$user_id, $return_url, $notify_url){
			if($user_type==1){
				$where['proxy_id']=$user_id;
			}else{
				$where['enterprise_id']=$user_id;
			}
			$result = M("user_set") -> where($where) -> find();

			//应用ID,您的APPID。
			$app_id = $result["pc_alipay_account"];//"2016122804684999";
			//商户私钥，您的原始格式RSA私钥
			$merchant_private_key = $result["pc_alipay_partner"];//"MIICWwIBAAKBgQDPEdy59UIFAQEA1eWREPtb7+nZqJkr014Q1wxK3B4/5y9uMQ0LGGa8y9XA24F7ngOereDw5VbOAe1qLezkEPJRt+rYNkwQSa7snzUjHwR0EnhAJB1RpNlDUiB9cRJ4bauFa6uXQJicz1jl2EOD3jbPBbCDahoZzpBqN+IUwGJcFQIDAQABAoGAENcxDmal8eY9AKZkv0GUT8vZRvxxSKEuG0yCEWyJgUT6FIokt2xKnrwtLDwt8bHONY+KpczhHGwHtQT7KSk/q8QkrPgYQxTTMiZL8jJ85Tip2TEyVFPyugd0QhRIwLjQ528NKWoaeOO7CI41xuW8OM46lzB/cU94+Oi9gHplw5kCQQD665+Wd6K6aLMp9eMVnggCXESVex2XtkIHnoKbApb51B8JUwU0CHDAC3mXlz03NQx7f4J759xztKYgvjssAbK7AkEA00L8dCjO6Y6z9XuNWB3pJ38foCO3EFolBEYoJoU3vDFM1ZbkHqC+KEggNZwuRswJUH0D2yVgeAwQ2y3IGfxHbwJABLaOTdY7cULsMpqSxGIuhlTTWPdyC9p5jQkWLPE0gsbQOm2byGlsLL9KbmWB2dqePGedvNQTGP1IrY7FL8NsZwJACkh6bVUHLUsq60oGSUG7dZa0fWD/qiYZIyofDjDx05E6wjLEC9GbL+7C0pk1j3CjC23qYCJjnbRIpcKGuO0UtQJAU+GYU8mcLCOdUzUf1sMvUF+fDOePkgCX8aRP9USB69cnyLcZA4lyGLd6cwORH+VWOQVDaRbN9IhMe0VcN2vxPQ==";
			//编码格式
			$charset = "UTF-8";
			//签名方式
			$sign_type = "RSA";
			//支付宝网关
			$gatewayUrl = "https://openapi.alipay.com/gateway.do";
			//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
			$alipay_public_key = $result["pc_alipay_key"];//"MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDDI6d306Q8fIfCOaTXyiUeJHkrIvYISRcc73s3vF1ZT7XN8RNPwJxo8pWaJMmvyTn9N4HQ632qJBVHf8sxHi/fEsraprwCtzvzQETrNRwVxLO5jVmRGi60j8Ue1efIlzPXV9je9mkjzOmdssymZkh2QhUrCmZYI/FCEa3/cNMW0QIDAQAB";

			//支付信息数组
			// $config = C("AliPay");
	
			$config["app_id"] = $app_id;
			$config["merchant_private_key"] = $merchant_private_key;
			$config["charset"] = $charset;
			$config["sign_type"] = $sign_type;
			$config["gatewayUrl"] = $gatewayUrl;
			$config["alipay_public_key"] = $alipay_public_key;
			$config["return_url"] = $return_url;
			$config["notify_url"] = $notify_url;
			return $config;
	}

	//向流量服务器下单
	private function payOrder($order_id, $trade_no,$pay_type,$user_type,$user_id){
			$order = M('pay_order') -> where(array('pay_order_code' => $order_id)) -> find();
			switch (intval($order['pay_status'])) {
				case 1: 
					//下单
					$mobile = $order['mobile'];
					$product_id = $order['product_id'];
					$data['pay_type'] = $pay_type;//4表示支付宝网页支付
					$data['pay_status'] = 2;
					$data['pay_date'] = date("Y-m-d H:i:s", time());
					$data['number'] = $trade_no;
					M('pay_order') -> where(array('pay_order_code' => $order_id)) -> save($data);

						
					$submiturl = gethostwithhttp() . "/index.php/Sdk/Api/wx_recharge";
					$pd = array('phone' => $mobile, 'product_id' => $product_id, "user_type" => $user_type, "user_id" => $user_id, "out_trade_no" => $order_id);
					$rt = https_request($submiturl, $pd);
					//****************************************************************************
					$map1['product_id'] = $product_id;
					$products = D('ChannelProduct')->channelproductinfo($map1);
					$product_name = $products["product_name"];
					if(empty($product_name))
					{
						$product_name = $products['size'];
					}
					//用户点击链接监控 by lv ($type, $phone, $product_name,$user_type,$user_id)
					$this->inputuserdata(5,$mobile,$product_name,$user_type,$user_id);
					//*****************************************************************************
						
					$fp = fopen("access_token.json", "a");
					fwrite($fp, json_encode($rt));
					fclose($fp);
					
					break;
				case 2: //已支付
					break;
			}
	}
	
	//支付宝退款
	public function refund()
	{
	}
}
?>