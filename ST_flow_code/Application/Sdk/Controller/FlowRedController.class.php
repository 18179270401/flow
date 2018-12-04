<?php
namespace Sdk\Controller;
use Think\Controller;
class FlowRedController extends Controller {

     public function _initialize(){
        Vendor("WxPayR.Api");
        Vendor("WxPayR.JsApiPay");
        Vendor("WxPayR.WxPayConfig");
        Vendor('WxPayR.Notify');
        Vendor('WxPayR.Native_notify');
        Vendor('WxPayR.WxPayData');
        Vendor('WxPayR.Exception');
    }
    private function lastindex()
    {
		$role = "/Application/Sdk/View/RedFlow/";
		$this -> assign("role", $role);
        $this->assign("typevalue","红包系统正在维护中！！");
        $this->display("/RedFlow/no_flow"); //错误提示   
        exit();
    }
	public function aindex()
	{

		//版本号更新css js更新
 		$this->assign('version_number', C('VERSION_NUMBER'));
		 
		$Key = $_SERVER["QUERY_STRING"];
		$tmp = stripos($Key, "&");
		if($tmp != false)
		{
			$Key = substr($Key,0,$tmp);
		}
		
    	$strArray = $this->localdecode($Key);
		$InfoArray = explode(",",$strArray);
			
		$user_type = $InfoArray[0];
		$user_id = $InfoArray[1];
		$share_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		//分享权限获取
		$this->get_shareurl($share_link,$user_type,$user_id);
        $this->assign("user_type",$user_type);
        $this->assign("user_id",$user_id); //user_id用来记录代理商和企业id
        $role="/Application/Sdk/View/FlowRed/";
        $this->assign("role",$role);
        $this->display("dispatch_flow_red");
	}
	
    public function localdecode($data) {
        $data = base64_decode($data);
		for($i=0;$i<strlen($data);$i++){
			$ord = ord($data[$i]);
			$ord -= 20;
			$string = $string.chr($ord);
		}
        return $string;
    }
	
    //进入流量红包页面
    public function index(){

		//版本号更新css js更新
 		$this->assign('version_number', C('VERSION_NUMBER'));
		 

        $user_type=I("user_type");
        if(empty($user_type)){
            $this->error("信息有误！");
        }
        $user_id=I("user_id");
		
		$share_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		//分享权限获取
		$this->get_shareurl($share_link,$user_type,$user_id);
        $this->assign("user_type",$user_type);
        $this->assign("user_id",$user_id); //user_id用来记录代理商和企业id
        $role="/Application/Sdk/View/FlowRed/";
        $this->assign("role",$role);
        $this->display("dispatch_flow_red");
    }
    
    public function flowproduct(){
		//版本号更新css js更新
 		$this->assign('version_number', C('VERSION_NUMBER'));
		 
        $role="/Application/Sdk/View/FlowRed/";
        $this->assign("role",$role);
        $this->display("flowproduct");
    }

    public function flowred(){
		//版本号更新css js更新
 		$this->assign('version_number', C('VERSION_NUMBER'));
		 
        $user_type=I("user_type");
        $user_id=I("user_id");
		$openid = $this->GetOpenid($user_type, $user_id);
		
        $role = "/Application/Sdk/View/FlowRed/";
        cookie("red_openid",$openid);
        $this->assign("role",$role);
        $this->assign("openid",$openid);
        $this->assign("user_type",$user_type);
        $this->assign("user_id",$user_id);
        $this->display("FlowRed/Flowproduct");
    }
	public function flowreddetail(){
        
		//版本号更新css js更新
 		$this->assign('version_number', C('VERSION_NUMBER'));
		 
        $Type = trim(I("Type"));
  		$this->assign("Type",$Type);
        $role = "/Application/Sdk/View/FlowRed/";
        $this->assign("role",$role);
        //$this->assign("active",$active);
        $this->display("FlowRed/Flowproduct_detaillist");
    }
	
	public function wxpaymentflowred(){
        $openid=I("openid");
        if(empty($openid)){
            $openid=cookie("red_openid");
        }
        $user_type=trim(I("user_type"));
        $user_id=trim(I("user_id"));
		
		
		$openid = $this->GetOpenid($user_type, $user_id);
		
        $role = "/Application/Sdk/View/FlowRed/";   
        $this->assign("role",$role);
        $data['red_order_code']=apply_number2("", 6);              
        if(empty($user_type)){
            $this->error("操作错误！");
        }
        $packages=I("packages");
        $package_list = explode(",",$packages);
        $price='';
		
       $data['user_type']=$user_type;
        if($user_type==1){
            $data['proxy_id']=$user_id;
	        $data['enterprise_id'] = 0;
			$discountdata['user_type'] = 1;
			$discountdata['proxy_id'] = $user_id;
        }else{
            $data['enterprise_id']=$user_id;
	        $data['proxy_id'] = 0;
			$discountdata['user_type'] = 2;
			$discountdata['enterprise_id'] = $user_id;
        }      
		
        $dicountsData = $this->get_dicounts($user_type,$user_id);
        foreach($package_list as $p ){
            $where=array();
            $where['product_id']=$p;
            $cp=M("channel_product")->where($where)->find();
			if ((int)$cp['operator_id'] == 1)
			{
				$dicountData = $dicountsData['mobile_discount'];
			}
			else if ((int)$cp['operator_id'] == 2)
			{
				$dicountData = $dicountsData['unicom_discount'];
			}
			else if ((int)$cp['operator_id'] == 3)
			{
				$dicountData = $dicountsData['telecom_discount'];
			}
			if((float)$dicountData == 0 || empty($dicountData))
			{
				$dicountData = 10;
			}
			$orderprice=$cp['price'];
			//商品打折
			$orderprice = round($orderprice*$dicountData/10.0,2);
            	$price= $price + $orderprice;
        }
		$price = number_format($price, 2, '.', '');
		
		
		
		
		
        if(empty($packages)){
        		echo "<script>alert('请选择流量包！');</script>";
			exit();
            //$this->error("请选择流量包！");
        }
        if($dicountsData){
            $data['discount'] = $dicountsData['mobile_discount'].",".$dicountsData['unicom_discount'].",".$dicountsData['telecom_discount'];
        }else{
            $data['discount']="10,10,10";
        }
		
		//payment_type  int  1表示运营方收款    2表示企业收款  3代理商收款
		if($user_type==1){
          $map['proxy_id']=$user_id;
          $map['user_type']=1;
        }else{
          $map['enterprise_id']=$user_id;
          $map['user_type']=2;
        }
		$result=M("user_set")->where($map)->find();
        $data['payment_type'] = $result['payment_type'];
		/////////////
		
		
		
        $data['wx_openid']=$openid;                    
        $data['packages']=trim(I("packages"));
        $data['pay_price']=$price;
        $data['discount_price']=trim(I("discount_price"));
        $data['order_date']=date("Y-m-d H:i:s",time());
        $data['pay_status']=1;
        $data['pay_type']=2;//网页端的微信
        //备注信息
		$data['remark'] = trim(I("remark"));
		$share_link = gethostwithhttp()."/index.php/Sdk/RedFlow/index/red_order_code/".$data['red_order_code']."/user_type/".$user_type."/user_id/".$user_id;
	
		
        $data['share_link'] = $share_link;
        if(M("red_order")->add($data)){
            $this->assign("user_type",$user_type);
            $this->assign("user_id",$user_id);
            $this->assign("red_order_code",$data['red_order_code']);
            $this->assign("pay_price",$data['pay_price']);
            $this->assign("order_date",$data['order_date']);
            $this->assign("openid",$openid);
            $this->display("flow_order");
        }else{
            $this->display("下单数据错误！");
        }   
	}

    public function share_flow_red(){
        $red_order_id=trim(I("red_order_id"));
        $data['red_order_id']=$red_order_id;
        $order=M("red_order")->where($data)->find();
        if($order){
			//分享权限获取
            if($order['user_type']==1){
                $user_id=$order['proxy_id'];
            }else{
                $user_id=$order['enterprise_id'];
            }
			$this->get_shareurl($order["share_link"],$order['user_type'],$user_id);
	        $role = "/Application/Sdk/View/FlowRed/";
	        $this->assign("role",$role);
            $this->assign("share_link",$order['share_link']);
            $this->display("share_flow_red");
        }
        else {
            $this->error("参数错误！");
        }
    }

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

    //微信付款
    public function red_wxpay(){
      	$red_order_code=trim(I("red_order_code"));
      	$data['red_order_code']=$red_order_code;
        if(empty($data['red_order_code'])){
            $this->error("支付信息有误！");
        }
        $orders=M("red_order")->where($data)->find();
		
        //①、获取用户openid
        $user_type=$orders['user_type'];
		
		$discountdata = array();
        if($orders['user_type']==1){
            $user_id=$orders['proxy_id'];
			$discountdata['user_type'] = 1;
			$discountdata['proxy_id'] = $user_id;
        }else{
            $user_id=$orders['enterprise_id'];
			$discountdata['user_type'] = 2;
			$discountdata['enterprise_id'] = $user_id;
        }
		
		
		
        $package_list=explode(',',$orders['packages']);
        $price='';

        $dicountsData = $this->get_dicounts($user_type,$user_id);
        foreach($package_list as $p ){
            $where=array();
            $where['product_id']=$p;
            $cp=M("channel_product")->where($where)->find();
			if ((int)$cp['operator_id'] == 1)
			{
				$dicountData = $dicountsData['mobile_discount'];
			}
			else if ((int)$cp['operator_id'] == 2)
			{
				$dicountData = $dicountsData['unicom_discount'];
			}
			else if ((int)$cp['operator_id'] == 3)
			{
				$dicountData = $dicountsData['telecom_discount'];
			}
			if((float)$dicountData == 0 || empty($dicountData))
			{
				$dicountData = 10;
			}
			$orderprice=$cp['price'];
			//商品打折
			$orderprice = round($orderprice*$dicountData/10.0,2);
            	$price= $price + $orderprice;
        }
		
		
		
		
		
        if(!$orders){
            $this->error("支付信息有误！");
        }
        $money= $price*100;//支付金额
        $red_order_code=$orders['red_order_code']; //订单号
        date_default_timezone_set('PRC');
        $openId=I("openid");
		//获取配置信息。提供支付
	    $config = $this->getconfig($user_type,$user_id);
 		$tools = new \JsApiPay($config); 
        if(empty($openId)){
			$openid = $this->GetOpenid($user_type, $user_id);
        }
		
		
		
		
		
        //②、统一下单
        $input = new \WxPayUnifiedOrder;
        $input->SetBody("微信支付");
        $input->SetAttach("微信支付");
        $input->SetOut_trade_no($red_order_code);
       	$input->SetTotal_fee($money);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("test");
        $url=gethostwithhttp()."/index.php/Sdk/FlowRed/notify";
        $input->SetNotify_url($url);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = \WxPayApi::unifiedOrder($input,6,$config);
        $jsApiParameters = $tools->GetJsApiParameters($order);
        $this->assign("jsApiParameters",$jsApiParameters);
        $this->assign("red_order_id",$orders['red_order_id']);
        $this->display("wx");
    }
	
    //微信支付回调
    public function notify(){
//  		write_debug_log(array(__METHOD__.':'.__LINE__, '支付宝异步通知数据=参数==', func_get_args()));

		
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
          $res=json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);  
//		 $fp = fopen("access_token.json","a");
//		fwrite($fp, 'res:'.json_encode($res));
//		fclose($fp);
		
          $pre= M('red_order')->where(array('red_order_code'=>$res['out_trade_no']))->find();
          $user_type=$pre["user_type"];
          if($user_type==1){
             $user_id=$pre['proxy_id'];
          }else{
            $user_id=$pre['enterprise_id'];
          }
        $config=$this->getconfig($user_type,$user_id);
        $notify = new \PayNotifyCallBack($config);
        $result = $notify->Handle(false);      
        S('resultwx',$result);     
        $pre_deal = M('red_order')->where(array('red_order_code'=>$result['out_trade_no'],'pay_status'=>2))->find();
        if($pre_deal) {
            $notify = new \WxPayNotify();
            $notify->SetReturn_code('SUCCESS');
            $notify->SetReturn_msg('OK');
            $xml = $notify->ToXml();
            \WxpayApi::replyNotify($xml);
            S('success', 1);
        }
        else{
            if ($result['result_code'] == 'SUCCESS') {
                  //下单
                $data['pay_status']=2;
                $data['pay_date']=date("Y-m-d H:i:s" ,time());
                $data['number']=$result['transaction_id'];
                M('red_order')->where(array('red_order_code'=>$result['out_trade_no']))->save($data);                  
            } else {
                S('success', 0);
            }
        }
    }

    //获取红包信息
    public function all_order(){
        $user_type=I("user_type");
        $user_id=I("user_id");
		$openid = $this->GetOpenid($user_type, $user_id);
		
        $data['wx_openid']=$openid;
        $data['pay_status']=2;
        $orders=M("red_order")->where($data)->order("pay_date desc")->select();

    	 	//echo "<script>alert('dfsf');</script>";
        $data=array();
        foreach ($orders as $o) {
            $da=array();
            $packages=explode(',',$o['packages']);
            if($o['out_packages']==null){
                $out_num=0;  //领取的数量
            }else{
                 $out_packages=explode(',',$o['out_packages']);
                $out_num=count($out_packages);  //领取的数量
            }        
            $all_num=count($packages);  //购买的数量  
            $da['red_order_id']= $o['red_order_id'];   
            $da['red_order_code']=$o['red_order_code'];
            $da['order_date']=$o['order_date'];
            $da["all_num"]=$all_num;
            $da['out_num']=$out_num;
            $da['pay_price']=$o['pay_price'];
            array_push($data,$da);
        }
        $length=count($data);
        $role = "/Application/Sdk/View/FlowRed/";
        $this->assign("role",$role);
        $this->assign("pay_price",$da['pay_price']);
        $this->assign("length",$length);
        $this->assign("order",$data);
        $this->display("flow_red_record");
    }

     public function red_code(){
        $status="error";
        $msg="系统错误";
        $red_order_id=I("red_order_id");
        if(empty($red_order_id)){
            $this->display("参数错误!");
        }
        $data['red_order_id']=$red_order_id;
        $status="success";
        $order=M("red_order")->where($data)->find();
        $packages=explode(',',$order['packages']);
        if($order['out_packages']==null){
            $out_packages=array();
            $out_num=0;
        }else{
            $out_packages=explode(',',$order['out_packages']);
            $out_num=count($out_packages);  //领取的数量
        }       
        $all_num=count($packages);  //购买的数量        
        $result=array();
		$hideShareBtn = true;
        foreach ($packages as $p) {
            $or=array();
            $where['product_id']=$p;
            if(in_array($p,$out_packages)){
                foreach($out_packages as $k1=>$v1){
                    if($v1==$p){
                        $out_packages[$k1]=0;
                        break;
                    }
                }        
                $product=M("channel_product")->where($where)->find();
                $where['red_order_id']=$red_order_id;
                $num = M("red_record")->where($where)->count();
                if ($num == 0) {
                    if($product['operator_id']==1){
                        $or['product_name']="中国移动".$product["product_name"]."流量红包";
                    }elseif($product['operator_id']==2){
                        $or['product_name']="中国联通".$product["product_name"]."流量红包";
                    }else{
                        $or['product_name']="中国电信".$product["product_name"]."流量红包";
                    }
                    $or['info']="未有人领取已退款";
                    $or['receive_date']=$order['order_date'];
                }else{
                    $r_id=0;
                    $ids = M("red_record")->where($where)->field("red_record_id")->select();
                    foreach ($ids as $id) {
                        $status = 1;
                        foreach ($result as $r) {
                            if ($id["red_record_id"] == $r['red_record_id']) {
                                $status = 2;
                                break;
                            }
                        }
                        if ($status != 2) {
                            $r_id = $id['red_record_id'];
                            break;
                        }
                    }
                    if ($r_id == 0) {
                        if($product['operator_id']==1){
                            $or['product_name']="中国移动".$product["product_name"]."流量红包";
                        }elseif($product['operator_id']==2){
                            $or['product_name']="中国联通".$product["product_name"]."流量红包";
                        }else{
                            $or['product_name']="中国电信".$product["product_name"]."流量红包";
                        }
                        $or['info']="未有人领取已退款";
                        $or['receive_date']=$order['order_date'];
                    }else{
                        $rec=M("red_record")->where($where)->find();
                        if($product['operator_id']==1){
                            $or['product_name']="中国移动".$product["product_name"]."流量红包";
                        }elseif($product['operator_id']==2){
                            $or['product_name']="中国联通".$product["product_name"]."流量红包";
                        }else{
                            $or['product_name']="中国电信".$product["product_name"]."流量红包";
                        }
                        $or['info']=$rec['mobile']."领取";
                        $or['receive_date']=$rec['receive_date'];
                        $or['red_record_id']=$rec['red_record_id'];
                    }
                }
            }else{
            	$hideShareBtn = false;
                $product=M("channel_product")->where($where)->find();
                $where['red_order_id']=$red_order_id;
                if($product['operator_id']==1){
                    $or['product_name']="中国移动".$product["product_name"]."流量红包";
                }elseif($product['operator_id']==2){
                    $or['product_name']="中国联通".$product["product_name"]."流量红包";
                }else{
                    $or['product_name']="中国电信".$product["product_name"]."流量红包";
                }   
                 $or['info']="未有人领取";
                 $or['receive_date']=$order['order_date'];
            }
            array_push($result, $or);            
        }
		$user_type = $order['user_type'];
		if($user_type == 2)
		{
			$user_id = $order['enterprise_id'];
		}
		else
		{
			$user_id = $order['proxy_id'];
		}
		//分享权限获取
		$this->get_shareurl($order['share_link'],$user_type,$user_id);
        $role = "/Application/Sdk/View/FlowRed/";
        $this->assign("role",$role);    
        $this->assign("url",$order['share_link']);
		$this->assign("hideShareBtn", $hideShareBtn);
        $this->assign("result",$result);
        $this->assign("user_type",$user_type);
        $this->assign("user_id",$user_id);
        $this->display("flow_red_detail");
    }


//获取分享权限
	 public function get_shareurl($Link,$user_type,$user_id){
        $config=$this->getconfig($user_type,$user_id);
		$APPSECRET = $config['APPSECRET'];
		$APPID = $config['APPID'];
		
		//获取ticketcode
		$submiturl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$APPID."&secret=".$APPSECRET;
		$rt = https_request($submiturl);
		$obj = json_decode($rt,true); 
		$accesstoken = $obj['access_token'];
		
		$submiturl = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=".$accesstoken;
			
		//$submiturl = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$accesstoken."&openid=" .$Openid. "&lang=zh_CN";
		//$retrnrt = https_request($submiturl);
    		$retrnrt = $this->httpGet($submiturl);
		$retrnrtobj = json_decode($retrnrt,true);
		$jsapi_ticket = $retrnrtobj['ticket'];
			
			
		$nonceStr = $this->createNonceStr();
		$timestamp = time();
		$localurl='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		
		 // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    		$string = "jsapi_ticket=".$jsapi_ticket."&noncestr=".$nonceStr."&timestamp=".$timestamp."&url=".$localurl;		
    		$signature = sha1($string);	 
			
        	$this->assign("APPID",$APPID);//APPID
        	$this->assign("nonceStr",$nonceStr);	//随即串
        	$this->assign("timestamp",$timestamp);//时间戳
        	$this->assign("signature",$signature);//字符串
      
        	$FlowProductTitle = "流量红包";
        	$this->assign("FlowProductTitle",$FlowProductTitle);//字符串
        	$FlowProductdesc = "大家来抢流量红包活动";
        	$this->assign("FlowProductdesc",$FlowProductdesc);//字符串
        	
        	$this->assign("Link",$Link);//字符串
        	//http://sdk.liuliang.net.cn/Application/Sdk/View/FlowRed/images/Share_CheckRed.png
        	$localimgUrl = 'http://'.$_SERVER['HTTP_HOST']."/Application/Sdk/View/FlowRed/images/Share_FlowRed.png";
        	$this->assign("localimgUrl",$localimgUrl);//字符串
        	//分享
        	
        	
        //var FlowProductTitle = "大转盘－流量活动";
		//var FlowProductdesc = "参加每天大转盘，赢海量奖品";
		//var Link = "http://sdk.liuliang.net.cn/index.php/Sdk/RedFlow/index/red_order_id/1/wx_openid/123456/red_order_code/1462688995500234800";
		//var localimgUrl = "http://crm.eoc.cn/resources/crm/upload/public/2016/02/07/9516d2fa-7c3a-4f9c-9ba6-1ad53ed76952.jpg";
	
	
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
     public function  getconfig($user_type,$user_id){
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
	      $config=array();
	      $config['APPID']=$result['wx_appid'];
	      $config['APPSECRET']=$result['wx_appsecret'];
	      $config['MCHID']=$result['wx_mchid'];
	      $config['KEY']=$result['wx_key'];
	      return $config;
    }
	 
    public function GetOpenid($user_type,$user_id){
     	$keyopenid = $user_type.$user_id."cookopenid";
		
		$openid = cookie($keyopenid);
        if(empty($openid)){
	        $config = $this->getconfig($user_type,$user_id);
	        $tools = new \JsApiPay($config); 
	        $openid = $tools->GetOpenid();
			cookie($keyopenid,$openid);
        }
		return $openid;
	}
	 
	public function limitmoney()
	{
        	  $role = "/Application/Sdk/View/FlowRed/";
	  	  $this->assign("role",$role);
	   	  $this->display("limitmoney"); 	
	}
}