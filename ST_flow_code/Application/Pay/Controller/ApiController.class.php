<?php
namespace Pay\Controller;
use Think\Controller;

class ApiController extends Controller {

    function __construct() {
        parent::__construct();
		$this->hostIp = gethostwithhttp();
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
        Vendor('AlipayApp.NotifyPc');
        Vendor('Alipay.Md5function');
    }
	//根据手机获取流量包
	public function demo() {
		
		
		$map['enterprise_id']=139;
		$transaction_id = "180791049481464834971743012679";
		$total_fee = 100;
		$refund_fee = 100;
		
		
		$result=M("user_set")->where($map)->find();
		$config['APPID']		=   $result['app_appid'];
		$config['MCHID'] 	=   $result['app_mchid'];
		$config['KEY']		=	$result['app_key'];
		$config['SSLCERT_PATH']=$result['app_pem_file_one'];
		$config['SSLKEY_PATH']= $result['app_pem_file_two'];
		
		$input = new \WxPayRefund;
		$input->SetTransaction_id($transaction_id);//订单号
		$input->SetTotal_fee($total_fee);//总金额
		$input->SetRefund_fee($refund_fee);//退款金额
		$input->SetOut_refund_no($config['MCHID'] . date("YmdHis"));//退款订单号
		$input->SetOp_user_id($config['MCHID']);//商户id
		$input->SetAppid($config['APPID']);//公众账号ID
		$input->SetMch_id($config['MCHID']);//商户号
		$input->SetSign($config['KEY']);//签名
		$return = \WxPayApi::LExrefund($input,$config);
		$xml = $input->ToXml();
	}
    /**
     * 读取当前手机所能使用的产品
     */
     public function checkSign($contentArray)
	 {
	 	$sign = I("sign");      //签名
        $st_app_id = I('st_app_id');
        $user_id = I('user_id');
		$contentArray['st_app_id'] = $st_app_id;
		$contentArray['user_id'] = $user_id;

		$text = md5(md5($this->field_order($contentArray)));
		if($sign != $text)
		{
		 	$this->ReturnJson(false, "失败：签名错误！");
		}
	 }
    /**
     * field_order()
     * 将数据以字母大小排序在用'&'符号连起来
     * @param $content
     */
    private function field_order($content) {
        $appInfo = M('user_set')->where(array('third_app_key'=>I('st_app_id')))->find();
//		var_dump($appInfo['third_app_code']);
        if(!empty($content) && is_array($content)) {
            ksort($content);
            foreach($content as $key=>$val) {
                $list[] = $key."&".$val;
            }
            $list[] = "key_code&".$appInfo['third_app_code'];
        }
        return empty($list) ? $content : implode("&",$list);
    }
	
	 /**
     * 获取当前折扣
     */
	private function get_dicounts($result)
	{
	 	$st_app_id = I('st_app_id');
		$appInfo = M('user_set')->where(array('third_app_key'=>I('st_app_id')))->find();
        $user_type = $appInfo["user_type"];

        
        if ($user_type == 1) {
			$data['proxy_id'] = $appInfo['proxy_id'];
		} else {
			$data['enterprise_id'] = $appInfo['enterprise_id'];
		}
		//
		//1为微信 2为app
		$data['discount_type'] = 2;
        		//商品打折
        //通过省份
		$data['province_id'] = $result["province_id"];
        //通过运营商
		$data['operator_id'] = $result["operator_id"];
		$dicountsData = M('person_discount')->where($data)->find();
		if(!$dicountsData)
		{
			$data['province_id'] = 1;
			$dicountsData = M('person_discount')->where($data)->find();
		}
		
        $dicountData = $dicountsData["charge_discount"];
		if((float)$dicountData == 0 || empty($dicountData))
		{
			$dicountData = 10;
		}
		
		return $dicountData;
	}

	//微信支付
	//对外接口查询手机号对应的产品包
    public function CheckMobile() {
        $mobile = I('mobile');  //电话号码
        $sign = I("sign");      //签名
        //检查签名
        $pre_sign = array();
        $pre_sign['mobile'] = $mobile;
		$this->checkSign($pre_sign);
        //检查手机号码
        $tag = isMobile2($mobile);
        if (!$tag) {
            $this->ReturnJson(false, "失败：手机号码错误！");
        }

        $result = CheckMobile(I('mobile'));    //调用聚合数据接口
        if($result['operator_id']==0){
            $this -> ReturnJson(false, "失败：手机号码错误！");
        }
        //聚合接口没有返回数据
        if ('error' == $result['status']) {
            $this->ReturnJson(false, $result['msg']);
        }
//		var_dump($mobile);
		$discountNumber = $this->get_dicounts($result);



        //取出流量包数组
        $map = array(
            'operator_id'   => $result['operator_id'],
            'status'        => 1,
            'province_id'   => array('IN',array(1, $result['province_id']))
        );
        $msg = D('ChannelProduct')->get_product_list($map);
        $packetQ = array();    //全国包
        $packetS = array();    //省内包
        foreach ($msg as $key => $row) {
            $size = ($row['size'] >= 1024) ? ($row['size']/1024)."G" : ($row['size'])."M";
            //$discount = D('ChannelProduct')->get_product_discount(array('product_name'=>$row['product_name'],'operator_id'=>$row['operator_id'],'province_id'=>$row['province_id']));
           
            //商品打折
            $discount_money = round($row['price']*$discountNumber/10.0,2);
            $discount_money = number_format($discount_money, 2, '.', '');
		if($discount_money <= 0.01)
			{
				$discount_money = 0.01;
								$discount_money = number_format($discount_money, 2, '.', '');
				
			}
            $mid = array(
                'id'            => $row['product_id'],
                'size'          => $size,
                'price'         => $discount_money,
                'price_market'  => $row['price'],
            );
            if (intval($row['province_id']) == 1) {
                $packetQ[] = $mid;
            }else{
                $packetS[] = $mid;
            }
        }
        //组装返回数组
        $packet = array(
            'num' => $mobile,
            'belong' => $result['province_name'].$result['operator_name'],
            'Type' => array(
                array(
                    'type' => '全国',
                    'packet' => $packetQ,
                ),
                array(
                    'type' => '省内',
                    'packet' => $packetS,
                ),
            ),
        );
        //返回数据
        $this->ReturnJson('true','',$packet);
    }

    /**
	 * //微信支付
	 *
     * 添加预支付订单
     */
    public function PostOrder() {
        $mobile     = I('mobile');          //电话号码
        $packet_id  = I('packet_id');    //所选流量ID号
        $pay_id     = I('pay_id', '', 'int');   //支付类型
        $user_id    = I('user_id'); //标注用户 other_user_id
        $sign       = I("sign");                  //签名
        $st_app_id = I('st_app_id');
		$appInfo = M('user_set')->where(array('third_app_key'=>I('st_app_id')))->find();
		
		
        //write_debug_log(array(__METHOD__.':'.__LINE__, '支付参数为post==', I('post.'), '参数get==', I('get.')));
        //检查签名
        //echo $user_id;
        $pre_sign['mobile'] = $mobile;
        $pre_sign['packet_id'] = $packet_id;
        $pre_sign['pay_id'] = $pay_id;
        //检查签名
		$this->checkSign($pre_sign);
		if($pay_id != 1&&$pay_id!=3)
		{
            $this->ReturnJson(false, "失败：支付类型错误!");
		}
        //检查手机号码
        $tag = isMobile2($mobile);
        if (!$tag) {
            $this->ReturnJson(false, "失败：手机号码错误！");
        }
        //检查流量包
        $packet_id = array('packet_id'=>$packet_id,'num'=>1);
        $packet_msg = $this->CheckPacketMsg($packet_id);//检查流量包
        $packet_msg['mobile'] = $mobile;
        //检查支付方式
        $pay_id = $this->checkPayMethod($pay_id);//检查支付方式
        
      
        $result = CheckMobile($mobile);    //调用聚合数据接口
		$discountNumber = $this->get_dicounts($result);


		
		 //商品打折
        $discount_money = round($packet_msg['price']*$discountNumber/10.0,2);
        $discount_money = number_format($discount_money, 2, '.', '');
				if($discount_money <= 0.01)
			{
				$discount_money = 0.01;
								$discount_money = number_format($discount_money, 2, '.', '');
				
			}
        //建立订单
        $total_fee = $discount_money;

        $pay_order_code = $this->createOrder($packet_msg, $pay_id, $user_id, $total_fee, $appInfo, $mobile,$discount_money,$result);
		
        //write_debug_log(array(__METHOD__.':'.__LINE__, "pay_order_code=={$pay_order_code}"));
  		
        if($pay_id == 1) { //支付宝
        			$dataString = "";
   			$out_trade_no = $pay_order_code;
            $url = gethostwithhttp()."/index.php/Pay/Api/Notifyurl";
            $da['notify_url'] = $url;
            //支付宝支付订单信息
            $para['partner']=$appInfo['alipay_key'];
            $para['seller_id']=$appInfo['alipay_partner'];
            $para['out_trade_no']=$pay_order_code;
            $para['subject']="流量包";
            $para['body']="流量包";
            $para['total_fee']= $total_fee;
            //$para['total_fee']=0.01; //单位 元
            $para['notify_url']=$da['notify_url'];
            $para['service']="mobile.securitypay.pay";
            $para['payment_type']="1";
            $para['_input_charset']="utf-8";
            $para['it_b_pay']="30m";
            $para['show_url']="m.alipay.com";
            $dataS="";
            while (list ($key, $val) = each ($para)) {
                $dataS .= $key.'="'.$val.'"&';
				if ($key == 'out_trade_no'||$key == 'notify_url') {
					$dataString .= $key . '="' . '%@' . $key . '"&';
				} else {
					$dataString .= $key . '="' . $val . '"&';
				}
            }
            //去掉最后一个&字符
            $dataS = substr($dataS,0,count($dataS)-2);
            $dataString = substr($dataString,0,count($dataString)-2);
            
//          $priKey = file_get_contents("./Public/key/rsa_private_key.pem");
			$priKey = file_get_contents("." . $appInfo['alipay_pem_file_two']);
			
            $res=openssl_get_privatekey($priKey);
            openssl_sign($dataS, $sign, $res);
            openssl_free_key($res);
            //base64编码
            $sign = urlencode(base64_encode($sign));
			$dataS=$dataS.'&sign="'.$sign.'"&sign_type="RSA"';
            $dataString=$dataString.'&sign="'.$sign.'"&sign_type="RSA"';
            $data['data'] = $dataS;
			$data['rsdata'] = $dataString;
			$data['notify_url'] = $this->GetRSAData($url);
			$data['out_trade_no'] = $this->GetRSAData($out_trade_no);
			
        } else { //微信支付
        		$url = gethostwithhttp()."/index.php/Pay/Api/GetWechatCallback";
            
            $input = new \WxPayUnifiedOrder;
            $input->SetBody("流量充值");
            $input->SetAttach("流量充值");
            $input->SetOut_trade_no($pay_order_code);
            $input->SetTotal_fee($total_fee*100);
            //$input->SetTotal_fee(1); //单位 分
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetNotify_url($url);
			$input->SetProduct_id($pay_order_code);
            $input->SetTrade_type("APP");
			
			
		
			$config['APPID']=$appInfo['app_appid'];
			$config['MCHID']  =  $appInfo['app_mchid'];
			$config['KEY']=$appInfo['app_key'];
			$config['SSLCERT_PATH']=$appInfo['app_pem_file_one'];
			$config['SSLKEY_PATH']=$appInfo['app_pem_file_two'];
			
            //write_debug_log(array(__METHOD__.':'.__LINE__, 'input===', $input));
            $UnifiedOrderResult = \WxPayApi::unifiedOrder($input,6,$config);
            //write_debug_log(array(__METHOD__.':'.__LINE__, 'UnifiedOrderResult===', $UnifiedOrderResult));
	
            $prepayid=$UnifiedOrderResult["prepay_id"];
            $timeStamp = time();
            $noncestr=$UnifiedOrderResult['nonce_str'];
            $data = array();
            $data['appid']=$appInfo['app_appid'];//"wx95fe83325174ca87";
            $data['partnerid']=$appInfo['app_mchid'];
            $data['prepayid']=$prepayid;
            $data['package']="Sign=WXPay";
            $data['noncestr']=$noncestr;
            $data['timestamp']=$timeStamp;
        }
        //write_debug_log(array(__METHOD__.':'.__LINE__, "返回的订单数据data==", $data));
        $this->ReturnJson('true','',$data);
    }

  
    //回调确认是否支付成功
    public function  NotifyConfirm() {
        $order_id = I("order_id");
        if(!empty($order_id)){
            $map['pay_order_code'] = $order_id;
            $payorder_info = M("pay_order")->where($map)->find();
            if(2 == $payorder_info['pay_status']) {
                $this->ReturnJson('true',"", "成功：订单支付成功！");
            } else if(1 == $payorder_info['pay_status']) {
                $this->ReturnJson(false, "失败：订单未支付！");
            } else {
                $this->ReturnJson(false, "失败：订单数据不存在！");
            }
        }else{
            $this->ReturnJson(false, "失败：订单号不存在！");
        }
    }

    //查询流量红包订单
    public function  LlBack() {
    		$st_appId=I('st_app_id');
        $user_id=I("user_id");
		$sign = I('sign');
       	$pre_sign = array();
		$this->checkSign($pre_sign);
		
		 $appInfo = M('user_set')->where(array('third_app_key'=>I('st_app_id')))->find();
		
        if(empty($user_id)) {
            $this->ReturnJson(false, "失败：用户id不能为空！");
        }
	
		
        $cond = array(
            'other_user_id' => $user_id,
            'pay_status'=>2
        );
		
		$user_type = $appInfo['user_type'];
		if ($user_type == 1) {
			$cond['proxy_id'] = $appInfo['proxy_id'];
		} else {
			$cond['enterprise_id'] = $appInfo['enterprise_id'];
		}
		
        $data = M('pay_order po')
                ->join("left join ".C('DB_PREFIX')."channel_product cp on po.product_id = cp.product_id")
                ->where($cond)->field("po.other_user_id as user_id,po.discount_price as user_fee,po.mobile,po.order_date as time,po.pay_status,cp.size as packet_size")
                ->order('po.order_date desc')->limit('0,20')->select();
        if(!empty($data) && is_array($data)) {
            foreach($data as $k => &$v) {
                $v['time'] = strtotime($v['time']); //时间戳
                if($v['pay_status'] == 2) {
                    $v['status'] = 1; //已支付成功
                } else {
                    $v['status'] = 0; //未支付
                }
				
            }
        }
        $this->ReturnJson('true','',$data);
    }

    /**
     * CheckPacketMag
     * 检查传进来的流量包数据 $packet_msg 只有流量包id 和  数量
     * @return array   /失败就直接json放回并exit
     */
    private function CheckPacketMsg($packet_msg) {
        if (empty($packet_msg) || !is_array($packet_msg)) {
            $this->ReturnJson(false, '未传入流量包信息！');
        }

        $packet_msg = D('ChannelProduct')->channelproductinfo($packet_msg['packet_id']);
        if (empty($packet_msg)) {
            $this->ReturnJson(false, '流量包信息有误！');
        } else {
            return $packet_msg;
        }
    }

    /**
     * 检查支付方式
     */
    private function checkPayMethod($pay_id) {
        return empty($pay_id) ? 1 : $pay_id;
    }


    /**
     * 建立订单 (t_flow_pay_order)
     */
    private function createOrder($packet_msg, $pay_id, $user_id, $total_fee, $api_info, $mobile,$discount_price,$result) {
        //$discount = D('ChannelProduct')->get_product_discount(array('product_name'=>$packet_msg['product_name'],'operator_id'=>$packet_msg['operator_id'],'province_id'=>$packet_msg['province_id']));

		/////////////
		//代理商提供给企业的价格
        if($api_info['user_type']==1){
          $map['proxy_id'] = $api_info['proxy_id'];
	      $map['enterprise_id'] = 0;
          $map['user_type']=1;
        }else{
          $map['enterprise_id'] = $api_info['enterprise_id'];
	      $map['proxy_id'] = 0;
          $map['user_type']=2;
        }
		/////////////
        //通过省份
		$map['province_id'] = $result["province_id"];
        //通过运营商
		$map['operator_id'] = $result["operator_id"];
		$discount=M("discount")->where($map)->find();
        $discount_number = $discount["discount_number"];
        if(empty($discount_number))
        {
            //查询全国
		    $map['province_id'] = 1;
            $map['operator_id'] = $result["operator_id"];
            $discount=M("discount")->where($map)->find();
            $discount_number = $discount["discount_number"];
            if(empty($discount_number))
            {
                $discount_number = 1;
            }
        }
        $deduct_price = $discount_number*$packet_msg['price'];
        //将代理商价格录入购买记录
        $ins = array(
            'pay_order_code'  => apply_number2($mobile, 6),
            'user_type'     => $api_info['user_type'],
            'proxy_id'      => $api_info['proxy_id'],
            'enterprise_id' => $api_info['enterprise_id'],
            'order_code'    => '',
            'product_id'    => $packet_msg['product_id'],
            'number'        => '',//交易号
            'pay_type'      => $pay_id, //支付类型(1：支付宝、2：微信,3APP微信)
            'mobile'        => $mobile,
            'price'         => $packet_msg['price'],
            'discount_price'=> $discount_price,
            'pay_status'    => 1,//支付状态（1：未支付、2：已支付）
            'order_date'    => date('Y-m-d H:i:s'),
            'pay_date'      => '',
            'other_user_id' => $user_id,
            'deduct_price'  => $deduct_price,
        );
        $rt = M('pay_order')->add($ins);
        empty($rt) && write_error_log(array(__METHOD__.':'.__LINE__, '插入订单失败,sql==> '.M()->getLastSql(), $ins));
        return $ins['pay_order_code'];
    }

    /**
     * _createInFrom()
     * 功能：生成订单
     * @front 前置条件：流量包数据正确
     * @param array() 流量包数组
     * @return 成功->$msg  失败->false
     * @param $order_num 成功就引用返回订单编号
     * @version 1.0
     * @time 5/21
     */
    private function _createInFrom($packet_msg, $api_info) {
		$phone = $packet_msg['mobile'];
		$size  = $packet_msg['size'];
		
        $api_key = $api_info['api_key'];
		$api_account = $api_info['api_account'];
		$range = (1 == $packet_msg['province_id']) ? 0 : 1;
		$timeStamp = time();
        $post_data = array(
            'account'			=> $api_account,
            'action'			=> 'Charge',
            'phone'				=> $phone,
            'range'				=> $range,
            'size'				=> $size,
            'timeStamp'			=> $timeStamp,
        );
		
        $pre_str = "{$api_key}account={$api_account}&action=Charge&phone={$phone}&range={$range}&size={$size}&timeStamp={$timeStamp}{$api_key}";
        $post_data['sign'] = md5($pre_str);
        $rt = https_request(C('API_SUBMIT'), $post_data);
		 
        $arr_rt = json_decode($rt, true);
		return $arr_rt;
    }

    /**
     * ReturnJson()
     * 封装了的json返回函数
     * @param true /false
     * @param $msg
     * @param $code
     * @param $deta
     */
    protected function ReturnJson($status, $msg = '', $data = '') {
        $status = ($status == true) ? 1 : 0;
        $array = array(
            'status'    => $status,
            'msg'       => $msg,
            'data'      => $data,
        );
        echo json_encode($array);exit;
    }
	
	protected function GetRSAData($data)
	{
		$encrypted = "";
		$pu_key = file_get_contents("./Public/RSAKEY/rsa_public_key.pem");
        $res=openssl_get_publickey($pu_key);
		openssl_public_encrypt($data,$encrypted,$pu_key);//公钥加密  
//		var_dump($encrypted);
		$encrypted = base64_encode($encrypted);
		return  $encrypted;
	}

    private function  app_wx_config($user_type,$user_id){
        if($user_type==1){
            $map['proxy_id']=$user_id;
        }else{
            $map['enterprise_id']=$user_id;
        }
        $result=M("user_set")->where($map)->find();
        //默认还是企业端收款
        $config=array();
        $config['APPID']=$result['app_appid'];
        $config['APPSECRET']=$result['app_appsecret'];
        $config['MCHID']=$result['app_mchid'];
        $config['KEY']=$result['app_key'];
        return $config;
    }

    public function GetWechatCallback() {
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $res=json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        $pre= M('pay_order')->where(array('pay_order_code'=>$res['out_trade_no']))->find();
        $user_type=$pre["user_type"];
        if($user_type==1){
            $user_id=$pre['proxy_id'];
        }else{
            $user_id=$pre['enterprise_id'];
        }
        $config=$this->app_wx_config($user_type,$user_id);
        $notify = new \PayNotifyCallBack($config);
        $result = $notify->Handle(false);
        $pre_deal = M('pay_order')->where(array('pay_order_code'=>$result['out_trade_no']))->find();
        if(2 == $pre_deal['pay_status']) { //2 已支付
            $notify = new \WxPayNotify();
            $notify->SetReturn_code('SUCCESS');
            $notify->SetReturn_msg('OK');
            $xml = $notify->ToXml();
            \WxpayApi::replyNotify($xml);
        } else if(1 == $pre_deal['pay_status']) {
        	 	//1 未支付
            $array_data=$result;
            if ($array_data['result_code'] == 'SUCCESS') {
                $packet_msg = M('channel_product')->where("product_id={$pre_deal['product_id']}")->find();
                $packet_msg['mobile'] = $pre_deal['mobile'];
                $order = array(
//                  'order_code'    => $order_id,
                    'number'        => $array_data['transaction_id'],
                    'pay_status'    => 2,
                    'pay_date'      => date('Y-m-d H:i:s'),
                );
                M('pay_order')->where(array('pay_order_code'=>$result['out_trade_no']))->save($order);
                $cond = array(
                    'user_type'     => $pre_deal['user_type'],
                    'proxy_id'      => intval($pre_deal['proxy_id']),
                    'enterprise_id' => intval($pre_deal['enterprise_id']),
                );
                $api_info = M('sys_api')->where($cond)->find();
                $arr_rt = $this->_createInFrom($packet_msg, $api_info);
                
                $errcode = $arr_rt['respCode'];
                $orderID = $arr_rt["orderID"];
                if(!$orderID)
                {
                    $msg = $ret['respCode'].",".$ret['respMsg'];
                    $order['remark'] = $msg;
                }
                $order['order_code'] = $orderID;
	            M('pay_order') -> where(array('pay_order_code' =>$result['out_trade_no'])) -> save($order);


				if('0000' != $errcode) {
					$this->wx_refund($pre_deal);
				}
	            } else {
                S('success', 0);
            }
        }
    }

    /**
     * 退款
     */
    public function NotifyurlRefund() {
        $alipay_config = array(
            'partner'           => '2088221350804434',   //这里是你在成功申请支付宝接口后获取到的PID；
            'key'               => 's0g4zmhng1ur0kb9eh2smgkk2wvn9v1n',//这里是你在成功申请支付宝接口后获取到的Key
            'sign_type'         => strtoupper('RSA'),
            'private_key_path'  => './Public/key/rsa_private_key.pem',
            'public_key_path'   => './Public/key/rsa_public_key.pem',
            'input_charset'     => strtolower('utf-8'),
            'cacert'            => getcwd() . '\\cacert.pem',
            'transport'         => 'http'
        );
        //计算得出通知验证结果
        /*$alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
		$fp = fopen("access_token.json", "w");
		fwrite($fp, json_encode($verify_result));
		fclose($fp);*/
        $verify_result=1;
        if ($verify_result) {//验证成功
            //检查是否之前已经接受过改批次的通知
            /*$batch_no = $_POST['batch_no'];
            $db_check = D('Refund');
            $re = $db_check->where(array('in_order_id' => $batch_no, 'flag' => 'T','payment_method'=>1))->find();
            if (!empty($re)) {
                echo "success";
                exit();
            }

            $db_check->where(array('in_order_id' => $batch_no,'payment_method' => 1))->save(array('refresh_time' => time(), 'flag' => 'T'));*/
            echo "success";        //请不要修改或删除
        } else {
            //验证失败
            echo "fail";//请不要修改或删除
        }
    }

    function wx(){
    		$out_trade_no =I('order');
			var_dump($out_trade_no);
//      $order="180791049481464834971743012679";
		$map['enterprise_id']=139;
		
		$total_fee = $data["price"] * 100;
		$refund_fee = $data["price"] * 100;
		
		
		$result=M("user_set")->where($map)->find();
		$config['APPID']		=   $result['app_appid'];
		$config['MCHID'] 	=   $result['app_mchid'];
		$config['KEY']		=	$result['app_key'];
		$config['SSLCERT_PATH']=$result['app_pem_file_one'];
		$config['SSLKEY_PATH']= $result['app_pem_file_two'];
		$APPID = $result['app_appid'];
		$MCHID = $result['app_mchid'];
		$KEY   = $result['app_key'];
		
		$input = new \WxPayRefund;

        $input->SetOut_trade_no($out_trade_no);
		//$input->SetTransaction_id($transaction_id);//订单号

		$input->SetTotal_fee($total_fee);//总金额
		$input->SetRefund_fee($refund_fee);//退款金额
		$input->SetOut_refund_no($config['MCHID'] . date("YmdHis"));//退款订单号
		$input->SetOp_user_id($config['MCHID']);//商户id
		$input->SetAppid($config['APPID']);//公众账号ID
		$input->SetMch_id($config['MCHID']);//商户号
		//$input->SetSign($config['KEY']);//签名
		$xml = $input->ToXml();
		$return = \WxPayApi::LExrefund($input,$config);
		var_dump($return);
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

    private function app_alipy_config($user_type,$user_id){
        if($user_type==1){
            $map['proxy_id']=$user_id;
        }else{
            $map['enterprise_id']=$user_id;
        }
        $result=M("user_set")->where($map)->find();
        $alipay_config['partner'] = $result['alipay_partner']; //这里是你在成功申请支付宝接口后获取到的PID；
        $alipay_config['key']=$result['alipay_key'];//这里是你在成功申请支付宝接口后获取到的Key
        $alipay_config['sign_type'] = strtoupper('RSA');
        $alipay_config['ali_public_key_path'] = $result['alipay_pem_file'];
        $alipay_config['public_key_path']= $result['alipay_pem_file_two'];
        $alipay_config['input_charset'] = strtolower('utf-8');
        $alipay_config['cacert'] = './Public/key/cacert.pem';
        $alipay_config['transport'] = "http";
        return $alipay_config;
    }

    /**
     * 支付宝服务器异步通知页面方法
     */
    public function Notifyurl() {
        //计算得出通知验证结果
        $pre= M('pay_order')->where(array('pay_order_code'=>$_POST['out_trade_no']))->find();
        $user_type=$pre["user_type"];
        if($user_type==1){
            $user_id=$pre['proxy_id'];
        }else{
            $user_id=$pre['enterprise_id'];
        }
        $alipay_config=$this->app_alipy_config($user_type,$user_id);
        $alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        if ($verify_result) {
            //write_debug_log(array(__METHOD__.':'.__LINE__, '支付宝异步通知数据==', $_POST));
            //验证成功
            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
            $out_trade_no = $_POST['out_trade_no'];      //商户订单号
            $trade_no = $_POST['trade_no'];          //支付宝交易号
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

    private function payOver($order_id, $trade_no) {
    		
        //write_debug_log(array(__METHOD__.':'.__LINE__, '支付宝异步通知数据=参数==', func_get_args()));
        //成功支付
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
        //write_debug_log(array(__METHOD__.':'.__LINE__, '支付宝异步通知数据=订单数据==', $middle));
        switch (intval($middle['pay_status'])) {
            case 1: //未支付
 				$packet_msg = M('channel_product')->where("product_id={$middle['product_id']}")->find();
                $packet_msg['mobile'] = $middle['mobile'];
				if($middle['user_type'] == 1)
				{
					$map['proxy_id'] = $middle['proxy_id'];
				}
				else
				{
					$map['enterprise_id'] = intval($middle['enterprise_id']);
				}
                $api_info = M('sys_api')->where($map)->find();
				
                $order = array(
                   // 'order_code'    => $order_code,
                    'number'        => $trade_no,
                    'pay_status'    => 2,
                    'pay_date'      => date('Y-m-d H:i:s'),
                );
                M('pay_order')->where("pay_order_code='{$order_id}'")->save($order);
				
				
                $arr_rt = $this->_createInFrom($packet_msg, $api_info);
                $errcode = $arr_rt['respCode'];

                $orderID = $arr_rt["orderID"];
                if(!$orderID)
                {
                    $msg = $ret['respCode'].",".$ret['respMsg'];
                    $order['remark'] = $msg;
                }
                $order['order_code'] = $orderID;
                M('pay_order')->where("pay_order_code='{$order_id}'")->save($order);
//		       	$fp = fopen("access_token.json","a");
//				fwrite($fp, $order_code);
//				fclose($fp);
				
                //write_debug_log(array(__METHOD__.':'.__LINE__, 'sql==>'.M()->getLastSql(),'支付宝异步通知数据==未支付，更新数据为==>', $upd));
                break;
            case 2: //已支付
                //write_debug_log(array(__METHOD__.':'.__LINE__, '支付宝异步通知数据==已支付'));
                break;
        }
    }
	//微信退款
	private function wx_refund($data)
	{
		$user_type = $data['user_type'];
		if ($user_type == 1) {
			$user_id = $data['proxy_id'];
			$map['proxy_id']=$user_id;
		} else {
			$user_id = $data['enterprise_id'];
			$map['enterprise_id']=$user_id;
		}
//		       	{"pay_order_id":"386",
//	"pay_order_code":"180791049481464680863554234569",
//	"user_type":"2","proxy_id":"0",
//	"enterprise_id":"139","order_code":"",
//	"product_id":"303","number":"",
//	"pay_type":"2","mobile":"18079104948","price":"1.00",
//	"discount_price":"1.00","pay_status":"1",
//	"order_date":"2016-05-31 15:47:43","pay_date":"0000-00-00 00:00:00",
//	"other_user_id":"EC8F0011-9AE8-46EE-830A-34D23F87B15D","remark":null}
//"1000"
		
		
		
		
		
		
		
//		if (empty($config['SSLCERT_PATH']) || empty($config['SSLKEY_PATH'])) {
//			$order['refund_status'] = 1;//未退款
//			M('pay_order')->where(array('pay_order_code' => $data['pay_order_code']))->save($order);
//			exit();
//		}
		
		
		
		$transaction_id = $data["pay_order_code"];
        $out_trade_no=$data['pay_order_code'];
		$total_fee = $data["price"] * 100;
		$refund_fee = $data["price"] * 100;
		
		
		$result=M("user_set")->where($map)->find();
		$config['APPID']		=   $result['app_appid'];
		$config['MCHID'] 	=   $result['app_mchid'];
		$config['KEY']		=	$result['app_key'];
		$config['SSLCERT_PATH']=$result['app_pem_file_one'];
		$config['SSLKEY_PATH']= $result['app_pem_file_two'];
		$APPID = $result['app_appid'];
		$MCHID = $result['app_mchid'];
		$KEY   = $result['app_key'];
		
		$input = new \WxPayRefund;

        $input->SetOut_trade_no($out_trade_no);
		//$input->SetTransaction_id($transaction_id);//订单号

		$input->SetTotal_fee($total_fee);//总金额
		$input->SetRefund_fee($refund_fee);//退款金额
		$input->SetOut_refund_no($config['MCHID'] . date("YmdHis"));//退款订单号
		$input->SetOp_user_id($config['MCHID']);//商户id
		$input->SetAppid($config['APPID']);//公众账号ID
		$input->SetMch_id($config['MCHID']);//商户号
		//$input->SetSign($config['KEY']);//签名
		$xml = $input->ToXml();
       /* $fp = fopen("access_token.json","a");
        fwrite($fp, "我在这");
        fwrite($fp, json_encode($xml));*/
		$return = \WxPayApi::LExrefund($input,$config);
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

    public function Refund(){
        $pre= M('pay_order')->where(array('batch_no'=>$_POST['batch_no']))->find();
        if(empty($pre)){
            exit();
        }
        $user_type=$pre["user_type"];
        if($user_type==1){
            $user_id=$pre['proxy_id'];
        }else{
            $user_id=$pre['enterprise_id'];
        }
        $alipay_config=$this->app_alipy_config($user_type,$user_id);
        $alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        if ($verify_result) {//验证成功

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
        } else {
            //验证失败
            echo "fail";//请不要修改或删除
        }
    }
    public function pc_refund(){
        $pre= M('pay_order')->where(array('batch_no'=>$_POST['batch_no']))->find();
        if(empty($pre)){
            exit();
        }
        $user_type=$pre["user_type"];
        if($user_type==1){
            $user_id=$pre['proxy_id'];
        }else{
            $user_id=$pre['enterprise_id'];
        }
        $alipay_config=$this->app_alipy_config($user_type,$user_id);
        $alipayNotify = new \PcAlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        if ($verify_result) {//验证成功

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
        } else {
            //验证失败
            echo "fail";//请不要修改或删除
        }
    }

    //流量红包回调
    public function Refund_alipay(){
        $pre= M('red_order')->where(array('batch_no'=>$_POST['batch_no']))->find();
        if(empty($pre)){
            exit();
        }
        $user_type=$pre["user_type"];
        if($user_type==1){
            $user_id=$pre['proxy_id'];
        }else{
            $user_id=$pre['enterprise_id'];
        }
        $alipay_config=$this->app_alipy_config($user_type,$user_id);
        $alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        if ($verify_result) {//验证成功
            $order['refund_status'] = 2;//退款成功
            $data=D('SceneInfo')->get_pay_red_record_detail($pre['red_order_id'],2);
            $where['red_order_code']=$data['red_order_code'];
            if ($data['products'] != ",") {
                $reds = M('red_order')->where($where)->field("out_packages")->find();
                $pack1 = $reds['out_packages'] . $data['products'];
                if (empty($reds['out_packages'])) {
                    $pack1 = substr($pack1, 1, strlen($pack1) - 2);
                } else {
                    $pack1 = substr($pack1, 0, strlen($pack1) - 1);
                }
                $pack['out_packages'] = $pack1;
                M('red_order')->where($where)->save($pack);
            }
            if ($data['records'] != ",") {
                $records = explode(',', $data['records']);
                foreach ($records as $id) {
                    $where['red_record_id'] = $id;
                    $map['refund_status'] = 2;
                    M('red_record')->where($where)->save($map);
                }
            }
            echo "success";
            exit();
        } else {
            //验证失败
            echo "fail";//请不要修改或删除
        }
    }
}