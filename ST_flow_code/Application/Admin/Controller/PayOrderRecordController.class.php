<?php 
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
use Think\Verify;

class PayOrderRecordController extends CommonController{
    public function _initialize() {
        Vendor("WxPayR.Api");
        Vendor("WxPayR.JsApiPay");
        Vendor("WxPayR.WxPayConfig");
        Vendor('WxPayR.WxPayData');
        Vendor('WxPayR.Exception');
        Vendor('WxPayR.AppApi');
        Vendor('AlipayApp.Notify');
        Vendor('AlipayApp.Corefunction');
        Vendor('AlipayApp.Rsafunction');
        Vendor('AlipayApp.Submit');
        Vendor('AlipayApp.SubmitPc');
        Vendor('Alipay.Md5function');
        
		Vendor("WapPay.SignData");
		Vendor("WapPay.AlipayTradeRefundRequest");
		Vendor("WapPay.AlipayTradeService");
		Vendor("WapPay.AlipayTradeRefundContentBuilder");
		Vendor("WapPay.AopClient");
		Vendor("WapPay.AlipayTradeFastpayRefundQueryRequest");
        
        /*Vendor('WxPayApp.WxAppPayConfig');
        Vendor('WxPayApp.Exception');
        Vendor('WxPayApp.Notify');
        Vendor('WxPayApp.NativePay');
        Vendor('WxPayApp.Native_notify');
        Vendor('WxPayApp.JsApiPay');*/
    }
	/*
	 *领取流量记录
	 */
	public function  index(){
        D("SysUser")->sessionwriteclose();
        $use_t=D("SysUser")->self_user_type();
        $user_type = D('SysUser')->self_user_type()-1;
        $user_id = D('SysUser')->self_id();
        $self_proxy_id = D('SysUser')->self_proxy_id();
        $self_enterprise_id = D('SysUser')->self_enterprise_id();
        $mobile = trim(I('mobile'));
        $user_name=trim(I('user_name'));
        $operator_id=trim(I('operator_id'));
        $product_name = trim(I('product_name'));
        $start_datetime = trim(I('start_datetime'));
        $end_datetime = trim(I('end_datetime')) ;
        $status = trim(I('status')); //状态 9全部；1正在送冲；2充值成功，3.充值失败
        $refund_status=trim(I('refund_status')); //状态 ：9全部.1已退款，2未退款
        $payment_type=trim(I("payment_type"));//收款方式 1.为运营端，2为企业，3为代理商
        $where = array();
        if($use_t!=3){
            if(!empty($user_name)) {
                $whe1['p.proxy_name'] = array("like","%".$user_name."%");
                $whe1['e.enterprise_name'] = array("like","%".$user_name."%");
                $whe1["_logic"] = "or";
                $where[] = $whe1;
            }
        }
        if(!empty($payment_type) && $payment_type!=9){
            if($payment_type==2){
                $where['po.payment_type'] =array(2,array("exp","is null"),"or");
            }else{
                $where['po.payment_type']=$payment_type;
            }
        }
        if($use_t==3) {
            $where['po.user_type'] = $user_type;
            if ($user_type == '1') {
                //代理商
                $where['po.proxy_id'] = $self_proxy_id;
            } else if ($user_type == '2') {
                //企业
                $where['po.enterprise_id'] = $self_enterprise_id;
            }
        }
        if($status!=9){
            if($status == 1){
                $where1['o.order_status'] =array("exp","is null");
                $where1['po.order_code']=array("neq","");
                $where1['_logic']="and";
                $where[]=$where1;
            }
            if($status == 2){
                $where1['o.order_status']=array("in","2,5");
                $where1['po.order_code']=array('neq',"");
                $where1['_logic']="and";
                $where[]=$where1;
            }
            if($status==3){
                $where1['o.order_status']=6;
                $where1['po.order_code']=array("eq","");
                $where1['_logic']="or";
                $where[]=$where1;
            }
        }
        if($refund_status!=9){
            if($refund_status == 1){
                $where['po.refund_status']=2;
            }
            if($refund_status == 2){
                $map2['o.order_status']=6;
                $map2['po.order_code']=array("eq","");
                $map2['_logic']="or";
                $map1[]=$map2;
                $map1['po.refund_status']=array(1,array('exp',"is null"),"or");
                $map1['_logic']="and";
                $where[]=$map1;
            }
        }
        if($operator_id!=9){
            if($operator_id == 1){
                $where['cp.operator_id'] =1;
            }
            if($operator_id== 2){
                $where['cp.operator_id'] = 2;
            }
            if($operator_id==3){
                $where['cp.operator_id'] = 3;
            }
        }
        if($mobile){
            $where['po.mobile'] = $mobile;
        }
        if($product_name){
            $where['cp.product_name']=array('like','%'.$product_name.'%');
        }
        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $where['po.pay_date'] = array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }elseif($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $where['po.pay_date'] = array('between',array(start_time($start_datetime),$end_datetime));
            }elseif($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $where['po.pay_date'] = array('between',array($start_datetime,end_time($end_datetime)));
            }
        }else{
            $start_datetime =date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
            //$end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $end_datetime= strtotime($start_datetime)-2592000;
            $e_time=start_time(date('Y-m-d',$end_datetime));
            $where['po.pay_date'] = array('between',array($e_time,$start_datetime));
        }
        $list=D('SceneInfo')->get_pay_order_list($where);
        $money=$this->all_pay($list['list']);
        $this->assign("money",$money);
        $this->assign("use",$use_t);
        $start_datetime =date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
        $end_datetime= strtotime($start_datetime)-2592000;
        $e_time=start_time(date('Y-m-d',$end_datetime));
        $this->assign('default_end',$start_datetime);
        $this->assign('default_start',$e_time);
        //加载模板
        $this->assign('list',$list['list']);  //数据列表
        $this->assign('page',$list['page']);  //分页  
        $this->display();        //模板
    }

    //统计金额
    private function all_pay($list){
        $prices=0;//统计微信的收款金额
        $deducts=0;//统计扣款金额
        foreach($list as $v){
            //除去充值失败的 统计平台扣款
            if(!empty($v['order_code']) && $v['order_status ']!=6){
                $deducts=$deducts+$v['deduct_price'];
            }
            //除去退款的 统计微信收款金额
            if($v['refund_status']!=2){
                $prices=$prices+$v['discount_price'];
            }
        }
        $money['prices']=$prices;
        $money['deducts']=$deducts;
        return $money;
    }

    public function show(){
        $id = trim(I('get.pay_order_id'));
        $info = D('SceneInfo')->get_pay_order_record_detail($id);
        $use_t=D('SysUser')->self_user_type();
        $this->assign("usr",$use_t);
        $this->assign('info',$info);
        $this->display();
     }

    //再次送充
    public function pay_repay(){
        $msg = "系统错误";
        $status = "error";
        $pay_order_id = I("pay_order_id");
        $order = M("pay_order")->where(array('pay_order_id'=>$pay_order_id))->find();
        

        
        if(empty($order)){
            $this->ajaxReturn(array("msg"=>$msg,"stauts"=>$status));
        }
        //下单
		$mobile = $order['mobile'];//18079104948
		$product_id = $order['product_id'];//313
        $pay_order_code = $order['pay_order_code'];//180791049481489658332814124789
        $user_id = $order['enterprise_id'];//16
        
        //{"status":1,"msg":"\u65e0\u6cd5\u627e\u5230\u76f8\u5e94\u7684\u4ea7\u54c1","data":""}
        //{"phone":"18079104948","product_id":"520","user_type":2,"user_id":"90","out_trade_no":"180791049481489644169942025679"}
        $submiturl = gethostwithhttp() . "/index.php/Sdk/Api/wx_recharge";
        //指名方式是再次送充
		$pd = array('phone' => $mobile, 'product_id' => $product_id, "user_type" => 2, "user_id" => $user_id, "out_trade_no" => $pay_order_code, "pay_type" => "pay_repay");
		$rt = https_request($submiturl, $pd);
        $info = json_decode($rt,true);
        $status = $info["status"];
        if($status == 1)
        {
            $status = "success";
            $title = "下单成功";
        }
        else
        {
            $status = "error";
            $title =  "下单失败";//$info["msg"];
        }
        //$title  = $info["msg"].$product_id;
        // $info = json_decode($rt);
        // $msg = $info['msg'];
        // $info = json_encode($msg);
        //表示微信app支付退款

        $this->ajaxReturn(array("msg"=>$title,"status"=>$status));
    }

    //unicode 解码
    function replace_unicode_escape_sequence($match) {
      return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
    }
     //退款
    public function pay_refund(){
        $msg="系统错误";
        $status="error";
        $pay_order_id=I("pay_order_id");
        $orders=M("pay_order")->where(array('pay_order_id'=>$pay_order_id))->find();
   
        if(empty($orders)){
            $this->ajaxReturn(array("msg"=>$msg,"stauts"=>$status));
        }
        $orders['discount_price']=round($orders['discount_price'],2);
        //表示微信网页
        if($orders['pay_type']==2 || $orders['pay_type']==4) {
            $type = $this->wx_refund($orders);
        }elseif($orders['pay_type']==3) {
            $type = $this->app_wx($orders);
        }elseif($orders['pay_type']==7) {
            //up by lv
            $html=$this->wap_Alipay($orders);
       // var_dump($html);
            // $this->assign("html",$html);
            // $this->display("refund");
            exit();
        }elseif($orders['pay_type']==5){
            $html=$this->pc_Alipay($orders);
            $this->assign("html",$html);
            $this->display("refund");
            exit();
        }else{
            $html=$this->app_Alipay($orders);
            $this->assign("html",$html);
            $this->display("refund");
            exit();
        }
        //$type 1.未上传pem,2.退款成功，3.退款失败
        if($type==1){
            $msg="退款失败,请先上传pem文件！";
            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】,退款失败';
            $this->sys_log('退款',$note);
            $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
        }elseif($type==2){
            $msg="退款成功！";
            $status="success";
            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】,退款成功';
            $this->sys_log('退款',$note);
            $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
        }else{
            $msg="退款失败！";
            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】,退款失败';
            $this->sys_log('退款',$note);
            $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
        }
        //表示微信app支付退款
        $msg="退款失败！";
        $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
    }
    //微信app 退款
    private function app_wx($orders){
        $transaction_id =$orders['number'];
        if ($orders['user_type'] == 1) {
            $map['proxy_id']= $orders['proxy_id'];
        } else {
            $map['enterprise_id'] = $orders['enterprise_id'];
        }
        $result=M("user_set")->where($map)->find();
        $config['APPID']		=   $result['app_appid'];
        $config['MCHID'] 	=   $result['app_mchid'];
        $config['KEY']		=	$result['app_key'];
        $config['SSLCERT_PATH']=$result['app_pem_file_one'];
        $config['SSLKEY_PATH']= $result['app_pem_file_two'];
        if(empty($config['SSLCERT_PATH']) || empty($config['SSLKEY_PATH'])){
            return 1;
        }
        $total_fee = $orders["discount_price"] * 100;
        $refund_fee = $orders["discount_price"] * 100;
        $input = new \WxPayRefund;
        //$input->SetOut_trade_no($out_trade_no);
        $input->SetTransaction_id($transaction_id);//订单号

        $input->SetTotal_fee($total_fee);//总金额
        $input->SetRefund_fee($refund_fee);//退款金额
        $input->SetOut_refund_no($config['MCHID'] . date("YmdHis"));//退款订单号
        $input->SetOp_user_id($config['MCHID']);//商户id
        $input->SetAppid($config['APPID']);//公众账号ID
        $input->SetMch_id($config['MCHID']);//商户号
        //$input->SetSign($config['KEY']);//签名
        $xml = $input->ToXml();
        $return = \WxPayAppApi::LExrefund($input,$config);
        if ($return["result_code"] == "SUCCESS" && $return["return_code"] == "SUCCESS") {
            $order['refund_status'] = 2;//退款成功
            $rt=M('pay_order')->where(array('pay_order_code' => $orders['pay_order_code']))->save($order);
            return 2;
        } else {
           return 3;
        }
    }
    private function app_Alipay($orders){
        $alipay_config =array();
        if ($orders['user_type'] == 1) {
            $map['proxy_id']= $orders['proxy_id'];
        } else {
            $map['enterprise_id'] = $orders['enterprise_id'];
        }
        $result=M("user_set")->where($map)->find();
        $alipay_config['partner']= $result['alipay_key'];   //这里是你在成功申请支付宝接口后获取到的PID；
        $alipay_config['key']=$result['paykey'];//这里是你在成功申请支付宝接口后获取到的Key
        $alipay_config['sign_type'] = strtoupper('RSA');
        $alipay_config['private_key_path'] = $result['alipay_pem_file_two'];
        $alipay_config['public_key_path'] = $result['alipay_pem_file'];
        $alipay_config['input_charset'] = strtolower('utf-8');
        $alipay_config['cacert'] = './Public/key/cacert.pem';
        $alipay_config['transport'] = 'http';
        $alipaySubmit = new \PcAlipaySubmit($alipay_config);
        $msg = $orders['number'] . '^' . $orders['discount_price'] . '^充值失败，退款！';
        $batch_no=date('Ymd') . time() . randomY(8);
        $parameter = array(
            "service" => "refund_fastpay_by_platform_pwd",
            "partner" => $alipay_config['partner'],
            "notify_url" => gethostwithhttp()."/index.php/Pay/Api/Refund",
            //"notify_url" => "http://test.liuliang.net.cn/index.php/Pay/Api/Refund",
            "seller_email" => $result['alipay_partner'],
            "refund_date" => date('Y-m-d H:i:s'),
            "batch_no" => $batch_no,
            "batch_num" => 1,
            "detail_data" => $msg,
            "_input_charset" => strtolower('utf-8')
        );
        $data['batch_no']=$batch_no;
        M("pay_order")->where(array("number"=>$orders['number']))->save($data);//存入退款订单号
        $html_text = $alipaySubmit->buildRequestForm($parameter, "post", "确认");
        return $html_text;
    }

    //网页端支付宝退款
    private function wap_Alipay($orders){

		//获取支付宝配置

        $config =array();
        if ($orders['user_type'] == 1) {
            $map['proxy_id']= $orders['proxy_id'];
        } else {
            $map['enterprise_id'] = $orders['enterprise_id'];
        }
        $result=M("user_set")->where($map)->find();

		//应用ID,您的APPID。
		$app_id = $result["pc_alipay_account"];
		//商户私钥，您的原始格式RSA私钥
		$merchant_private_key = $result["pc_alipay_partner"];
		//编码格式
		$charset = "UTF-8";
		//签名方式
		$sign_type = "RSA";
		//支付宝网关
		$gatewayUrl = "https://openapi.alipay.com/gateway.do";
		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		$alipay_public_key = $result["pc_alipay_key"];

		//支付信息数组
		// $config = C("AliPay");

		$config["app_id"] = $app_id;
		$config["merchant_private_key"] = $merchant_private_key;
		$config["charset"] = $charset;
		$config["sign_type"] = $sign_type;
		$config["gatewayUrl"] = $gatewayUrl;
		$config["alipay_public_key"] = $alipay_public_key;
	//	$config["return_url"] = $return_url;
		$return_url = gethostwithhttp() . "/index.php/Sdk/WxFlowPayment/refund_url";
		$notify_url = gethostwithhttp() . "/index.php/Sdk/WxFlowPayment/refund_url";
		$config["notify_url"] = $notify_url;
		$config["return_url"] = $return_url;


		//商户订单号和支付宝交易号不能同时为空。 trade_no、  out_trade_no如果同时存在优先取trade_no
		//商户订单号，和支付宝交易号二选一
		$out_trade_no = $orders['number'];//trim($_POST['WIDout_trade_no']);

		//支付宝交易号，和商户订单号二选一
		$trade_no = $orders['number'];//$orders['pay_order_code'];//trim($_POST['WIDtrade_no']);

		//退款金额，不能大于订单总金额
		$refund_amount = $orders['discount_price'];//trim($_POST['WIDrefund_amount']);

		//退款的原因说明
		$refund_reason= "流量充值失败";
        
		//标识一次退款请求，同一笔交易多次退款需要保证唯一，如需部分退款，则此参数必传。
        $batch_no=date('Ymd') . time() . randomY(8);

   
		$RequestBuilder = new \AlipayTradeRefundContentBuilder();
		$RequestBuilder->setTradeNo($trade_no);
		$RequestBuilder->setOutTradeNo($out_trade_no);
		$RequestBuilder->setRefundAmount($refund_amount);
		$RequestBuilder->setRefundReason($refund_reason);
		$RequestBuilder->setOutRequestNo($batch_no);

		$Response = new \AlipayTradeService($config);
		$result = $Response->Refund($RequestBuilder);

        // $responseNode = str_replace(".", "_", "alipay.trade.fastpay.refund.query") . "_response";
        // $resultCode = $result->$responseNode->code;
        $array = $this->object2array($result);
        if($array["code"] == 10000)
        {
            $data['refund_status'] = 2;//退款成功
            $data['batch_no']= $batch_no;
            M('pay_order')->where(array('number' => $out_trade_no))->save($data);

            $msg="退款成功！";
            $status="success";
            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】,退款成功';
            $this->sys_log('退款',$note);
            $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));

            
        }
        else
        {
            $msg="退款失败！";
            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】,退款失败';
            $this->sys_log('退款',$note);
            $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
        }
        //return $result;
    }

   private function object2array($object) {
        if (is_object($object)) {
            foreach ($object as $key => $value) {
            $array[$key] = $value;
            }
        }
        else {
            $array = $object;
        }
        return $array;
    }

    //pc端支付宝退款
    private function pc_Alipay($orders){
        $alipay_config =array();
        if ($orders['user_type'] == 1) {
            $map['proxy_id']= $orders['proxy_id'];
        } else {
            $map['enterprise_id'] = $orders['enterprise_id'];
        }
        $result=M("user_set")->where($map)->find();
        $alipay_config['partner']= $result['pc_alipay_partner'];   //这里是你在成功申请支付宝接口后获取到的PID；
        $alipay_config['key']=$result['pc_alipay_key'];//这里是你在成功申请支付宝接口后获取到的Key
        $alipay_config['sign_type'] = strtoupper('MD5');
        $alipay_config['input_charset'] = strtolower('utf-8');
        $alipay_config['cacert'] = './Public/key/cacert.pem';
        $alipay_config['transport'] = 'http';
        $alipaySubmit = new \PcAlipaySubmit($alipay_config);
        $msg = $orders['number'] . '^' . $orders['discount_price'] . '^充值失败，退款！';
        $batch_no=date('Ymd') . time() . randomY(8);
        $parameter = array(
            "service" => "refund_fastpay_by_platform_pwd",
            "partner" => $alipay_config['partner'],
            "notify_url" => gethostwithhttp()."/index.php/Pay/Api/pc_refund",
            //"notify_url" => "http://test.liuliang.net.cn/index.php/Pay/Api/Refund",
            "seller_email" => $result['pc_alipay_account'],
            "refund_date" => date('Y-m-d H:i:s'),
            "batch_no" => $batch_no,
            "batch_num" => 1,
            "detail_data" => $msg,
            "_input_charset" => strtolower('utf-8')
        );
        $data['batch_no']=$batch_no;
        M("pay_order")->where(array("number"=>$orders['number']))->save($data);//存入退款订单号
        $html_text = $alipaySubmit->buildRequestForm($parameter, "post", "确认");
        return $html_text;
    }

    //h5页面退款的功能
    private function wx_refund($data)
    {
        $transaction_id = $data["number"];
        $total_fee = $data["discount_price"] * 100;
        $refund_fee = $data["discount_price"] * 100;
        if ($data['user_type'] == 1) {
            $user_id = $data['proxy_id'];
        } else {
            $user_id = $data['enterprise_id'];
        }
        $config = $this->wx_getconfig($data['user_type'], $user_id);
        if (empty($config['SSLCERT_PATH']) || empty($config['SSLKEY_PATH'])) {
            return 1;
        }
        $input = new \WxPayRefund;
        $input->SetTransaction_id($transaction_id);
        $input->SetTotal_fee($total_fee);
        $input->SetRefund_fee($refund_fee);
        $input->SetOut_refund_no($config['MCHID'] . date("YmdHis"));
        $input->SetOp_user_id($config['MCHID']);
        $return = \WxPayApi::refund($input, $config);
        if ($return["result_code"] == "SUCCESS" && $return["return_code"] == "SUCCESS") {
            $order['refund_status'] = 2;//退款成功
            $rt=M('pay_order')->where(array('pay_order_code' => $data['pay_order_code']))->save($order);
            if($rt){
                return 2;
            }else{
                return 3;
            }
        } else {
            return 3;
        }
    }
    //h5页面 获取信息config
    private function  wx_getconfig($user_type,$user_id){
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

    /**
    *导出EXCEL
    **/
    public function export_excel(){
        $use_t=D("SysUser")->self_user_type();
        $user_type = D('SysUser')->self_user_type()-1;
        $user_id = D('SysUser')->self_id();
        $self_proxy_id = D('SysUser')->self_proxy_id();
        $self_enterprise_id = D('SysUser')->self_enterprise_id();
        $mobile = trim(I('mobile'));
        $user_name=trim(I('user_name'));
        $operator_id=trim(I('operator_id'));
        $product_name = trim(I('product_name'));
        $start_datetime = trim(I('start_datetime'));
        $end_datetime = trim(I('end_datetime')) ;
        $status = trim(I('status')); //状态 9全部；1正在送冲；2充值成功，3.充值失败
        $refund_status=trim(I('refund_status')); //状态 ：9全部.1已退款，2未退款
        $where = array();
        if($use_t!=3){
            if(!empty($user_name)) {
                $whe1['p.proxy_name'] = array("like","%".$user_name."%");
                $whe1['e.enterprise_name'] = array("like","%".$user_name."%");
                $whe1["_logic"] = "or";
                $where[] = $whe1;
            }
        }
        if($use_t==3) {
            $where['po.user_type'] = $user_type;
            if ($user_type == '1') {
                //代理商
                $where['po.proxy_id'] = $self_proxy_id;
            } else if ($user_type == '2') {
                //企业
                $where['po.enterprise_id'] = $self_enterprise_id;
            }
        }
        if($status!=9){
            if($status == 1){
                $where1['o.order_status'] =array("exp","is null");
                $where1['po.order_code']=array("neq","");
                $where1['_logic']="and";
                $where[]=$where1;
            }
            if($status == 2){
                $where1['o.order_status']=array("in","2,5");
                $where1['po.order_code']=array('neq',"");
                $where1['_logic']="and";
                $where[]=$where1;
            }
            if($status==3){
                $where1['o.order_status']=6;
                $where1['po.order_code']=array("eq","");
                $where1['_logic']="or";
                $where[]=$where1;
            }
        }
        if($refund_status!=9){
            if($refund_status == 1){
                $where['po.refund_status']=2;
            }
            if($refund_status == 2){
                $map2['o.order_status']=6;
                $map2['po.order_code']=array("eq","");
                $map2['_logic']="or";
                $map1[]=$map2;
                $map1['po.refund_status']=array(1,array('exp',"is null"),"or");
                $map1['_logic']="and";
                $where[]=$map1;
            }
        }
        if($operator_id!=9){
            if($operator_id == 1){
                $where['cp.operator_id'] =1;
            }
            if($operator_id== 2){
                $where['cp.operator_id'] = 2;
            }
            if($operator_id==3){
                $where['cp.operator_id'] = 3;
            }
        }
        if($mobile){
            $where['po.mobile'] = $mobile;
        }
        if($product_name){
            $where['cp.product_name']=array('like','%'.$product_name.'%');
        }
        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $where['po.pay_date'] = array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }elseif($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $where['po.pay_date'] = array('between',array(start_time($start_datetime),$end_datetime));
            }elseif($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $where['po.pay_date'] = array('between',array($start_datetime,end_time($end_datetime)));
            }
        }else{
            $start_datetime =date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
            //$end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $end_datetime= strtotime($start_datetime)-2592000;
            $e_time=start_time(date('Y-m-d',$end_datetime));
            $where['po.pay_date'] = array('between',array($e_time,$start_datetime));
        }
        if($use_t==1){
            $proxys=D('Proxy')->proxy_child_ids();
            if($proxys){
                $where1['p.proxy_id']= array('in',$proxys);
            }else{
                $where1['p.proxy_id']=-1;
            }
            $enterprises=D("Enterprise")->enterprise_child_ids();
            if($enterprises){
                $where1['e.enterprise_id']=array('in',$enterprises);
            }else{
                $where1['e.enterprise_id']=-1;
            }
            $where1['_logic']="or";
            $where[]=$where1;
        }
        if($use_t==2) {
            $proxys=D('Proxy')->proxy_child_ids();
            $self_proxy_id=D('SysUser')->self_proxy_id();
            if($proxys){
                $stat['p.proxy_id'] = array('in',$proxys);
                $stat['p.top_proxy_id'] = array('eq',$self_proxy_id);
                $stat['_logic'] = 'and';
                $map['_complex'] = $stat;
                $map['p.proxy_id'] = array('eq',$self_proxy_id);
                $map['_logic'] = 'or';
                $where1[]=$map;
            }else{
                $where1['p.proxy_id']=$self_proxy_id;
            }
            $enterprises=D("Enterprise")->enterprise_ids();//获取该用户可操作的企业号();
            if($enterprises){
                $where1['e.enterprise_id']=array("in",$enterprises);
            }else{
                $where1['e.enterprise_id']=-1;
            }
            $where1['_logic']= "or";
            $where[]=$where1;
        }
        $where['pay_status']=2;//表示购买成功
        $model=M('pay_order po');
        
        $list =$model
            ->join('left join t_flow_channel_product as cp on cp.product_id = po.product_id')
            ->join('left join t_flow_proxy as p on p.proxy_id = po.proxy_id and po.user_type=1')
            ->join('left join t_flow_enterprise as e on e.enterprise_id= po.enterprise_id and po.user_type=2')
            ->join('left join t_flow_order as o on o.order_code= po.order_code')
            ->field('po.pay_order_id,po.pay_order_code,po.mobile,po.order_code,po.deduct_price,po.pay_date,po.price,po.discount_price,cp.operator_id,cp.product_name,po.user_type,p.proxy_name,e.enterprise_name,o.order_status,po.refund_status,po.recharge_sources')
            ->where($where)
            ->order('po.pay_date desc')
            ->limit(3000)
            ->select();

        $datas = array();
        $headArr=array();
        if($use_t !=3){
            $headArr =array_merge($headArr,array("用户类型","用户名称"));
        }
        $headArr= array_merge($headArr,array("手机号","运营商","流量包","购买金额(元)","收款方式","购买时间","充值状态","退款状态","支付渠道"));
        $operators = array("1"=>"中国移动","2"=>"中国联通","3"=>"中国电信");
        foreach ($list as $v) {
            $data=array();
            if($use_t !=3){
                $data['user_type'] = $v['user_type'] == 1?"代理商":"企业";
                $data['user_name'] = $v['user_type'] == 1?$v['proxy_name']:$v['enterprise_name'];
            }
            $data['mobile'] = $v['mobile'];
            $data['operator_id'] = $operators[$v['operator_id']];
            $data['product_name'] = $v['product_name'];
            $data['discount_price'] = $v['discount_price'];
            //$data['deduct_price']=$v['deduct_price'];
            if($v['payment_type']==1){
                $data['payment_type' ] ="运营方收款";
            }elseif($v['payment_type']==3){
                $data['payment_type' ]="代理商收款";
            }else{
                $data['payment_type' ]="企业收款";
            }
            $data['pay_date'] = $v['pay_date'];
            $data['order_status'] = "正在送充";
            if(empty($v['order_code']) || $v['order_status'] == 6){
                $data['order_status'] = "充值失败";
            }elseif($v['order_status'] == 2 || $v['order_status'] == 5){
                $data['order_status'] = "充值成功";
            }
            $data['refund_status'] = '--';
            if(empty($v['order_code']) || $v['order_status'] == 6){
                if($v['refund_status'] == 2){
                    $data['refund_status'] = '已退款';
                }else{
                    $data['refund_status'] = '未退款';
                }
            }
            $data['recharge_sources']=$v['recharge_sources'];
            array_push($datas,$data);
        }
            
        $title='流量购买记录';

        ExportEexcel($title,$headArr,$datas);
    }
}
?>