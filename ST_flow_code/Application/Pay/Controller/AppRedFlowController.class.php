<?php
namespace Pay\Controller;
use Think\Controller;
class AppRedFlowController extends Controller {

	public function _initialize() {
		Vendor("WxPayR.Api");
		Vendor("WxPayR.JsApiPay");
		Vendor("WxPayR.WxPayConfig");
		Vendor('WxPayR.Notify');
		Vendor('WxPayR.Native_notify');
		Vendor('WxPayR.WxPayData');
		Vendor('WxPayR.Exception');
	}

	public function localencode($data) {
		for($i=0;$i<strlen($data);$i++){
			$ord = ord($data[$i]);
			$ord += 20;
			$string = $string.chr($ord);
		}
        $string = base64_encode($string);
        return $string;
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

	public function index() {
		$rsaKey = $_SERVER["QUERY_STRING"];
		$tmp = stripos($rsaKey, "&");
	
		if($tmp != false)
		{
			$rsaKey = substr($rsaKey,0,$tmp);
		}

		// var_dump($rsaKey);
		// exit();

		$strArray = $this->localdecode($rsaKey);
		$InfoArray = explode(",",$strArray);


		$user_type = $InfoArray[0];
		$user_id = $InfoArray[1];
		$red_order_code = $InfoArray[2];


		// $red_order_code = I('red_order_code');
		// $user_type = I('user_type');
		// $user_id = I('user_id');

		$data['red_order_code'] = $red_order_code;

		$red_order = M("red_order") -> where($data) -> find();
		//将备注放入页面中
		$remark = $red_order['remark'];
		if (!$remark) {
			$remark = "现在流行发流量红包！";
		}
		$this -> assign("remark", $remark);

		//获取微信信息
		$this -> get_Shareinfo($user_type, $user_id,$red_order_code);

		$role = "/Application/Pay/View/AppRedFlow/";

		$this -> assign("role", $role);
		$this -> assign("red_order_code", $red_order_code);
		$this -> display("index");
	}

	//领取红包页面
	public function recode_red() {
		$phone = I("phone");
		$red_order_code = I("red_order_code");

		$role = "/Application/Pay/View/AppRedFlow/";
		$this -> assign("role", $role);

		if (empty($phone)) {
			$this -> error("号码有误！");
		}
		if (empty($red_order_code)) {
			$this -> error("信息错误！");
		}
		//获取流量红包信息
		$order = M("red_order") -> where(array("red_order_code" => $red_order_code, "pay_status" => 2)) -> find();
		if (!$order) {
			$this -> error("请先支付");
		}
        $user_type = $order['user_type'];
        if($user_type==1){
       	 	$user_id = $order['proxy_id'];
        }else{
          	$user_id = $order['enterprise_id'];
        }
        if(strtotime($order['pay_date'])+(86400*3)<time()){
            $localurl =gethostwithhttp()."/index.php/Sdk/FlowRed/index/user_type/".$user_type."/user_id/".$user_id;
            $this->assign("url",$localurl);
            $this->assign("typevalue","红包已过期！");
            $this->display("no_flow"); //错误提示    不可领
            exit();
        }
		



        //流量红包链接
        $data = $this->localencode($user_type.",".$user_id);
        $url = gethostwithhttp()."/index.php/Sdk/FlowRed/aindex?".$data;
        $this->assign("url",$url);

		//设定其分享内容
		$this->get_Shareinfo($user_type, $user_id,$red_order_code);
 //获取领取记录
        $where["mobile"]=$phone;
        $where['wx_openid']=$phone;
        // $where1["_logic"]="or";
        // $where[]=$where1;
        $where["red_order_id"] = $order["red_order_id"];
		$recordinfo = M("red_record")->where($where)->find();
        if(!empty($recordinfo)){
            $this->assign("typevalue","对不起，你已经领过红包了");
            $this->display("no_flow"); //错误提示    不可领
            exit();
        }else{
            //可领
            $packages = explode(",",$order['packages']);        //原有包
            $out_packages = explode(",",$order['out_packages']);//已领包
            if($packages)
            {
                foreach($packages as $v)
                {
                    if(!in_array($v,$out_packages)){
                        $packages_k[] = $v; //可用的流量产品ID
                    }else{
                        foreach($out_packages as $k1=>$v1){
                            if($v1==$v){
                               $out_packages[$k1]=0;
                                break;
                            }
                        }
                    }
                }
                if($packages_k){
                    $list = '';
                    $result = CheckMobile($phone);    //调用聚合数据接口
                    $whe['operator_id'] = $result['operator_id'];
                    $product_id="";
                    foreach($packages_k as $v1){
                        $whe['product_id'] = $v1;
                        //读取可用的产品写入 $list;
                        if($list = M("channel_product")->where($whe)->find()){
                            $product_id=$v1;
                            break;
                        }
                    }
                    if($product_id=="" || $product_id==null){
                        //$user_type=$record["user_type"];
		                	//$user_id=$record['user_id'];
            			$this->assign("typevalue","手慢了！红包已抢完");
                        $this->display("no_flow");
                        exit();
                    }
                    //添加领取记录

					  //红包占用更新
                    $result=M("red_order")->where(array("red_order_code"=>$red_order_code))->find();
                    if($result['out_packages']==null || $result['out_packages']==""){
                        $result['out_packages']=$product_id;
                    }else{
                        $result['out_packages']=$result['out_packages'].",".$product_id;
                    }
                    M("red_order")->where(array("red_order_code"=>$red_order_code))->save($result);
                   
                    //纪录录入红包领取记录
                    $record['user_type']=$user_type;
                    if($user_type==1){
                    	$record['enterprise_id'] = 0;
                        $record['proxy_id']=$user_id;
                    }else{
                        $record['enterprise_id']=$user_id;
                    	$record['proxy_id'] = 0;
                    }
                    $record['red_order_id']=$order['red_order_id'];
                    $record['product_id']=$product_id;
                    $record['mobile']=$phone;
                    $record['product_name']=$list['product_name'];
                    $record['wx_openid']=$phone;
                    $record['wx_photo'] = "appnophoto";
                    $record['wx_name'] = "appreceive";
                    $record['receive_date']=date("Y-m-d H:i:s" ,time());
                    M("red_record")->add($record);


                    //调用下单接口 返回空为失败   返回ID号为真
                    $orderID=$this->place_an_order($phone,$list['size'],$user_type,$user_id);
                    if($orderID==-1){
            			$this->assign("typevalue","手慢了！红包没有抢到!");
                        $this->display("no_flow");
                        exit();
                    }

			 		$orderinfo['order_id'] = $orderID;
		            M("red_record")->where($where)->save($orderinfo);
                    
					
                    $this->assign("operator_id",$list['operator_id']);
                    $this->assign("product_name",$list['product_name']);

                    $this->display("over_flow");
                    exit();
                }
                else
                {
                    //$user_type=$record["user_type"];
                    //$user_id=$record['user_id'];
           			$this->assign("typevalue","手慢了！红包已抢完");
                    $this->display("no_flow");
                    exit();
                }
            }else{
                //$user_type=$record["user_type"];
                //$user_id=$record['user_id'];
                //未读到已购买的流量包
           		$this->assign("typevalue","红包已过期！");
                $this->display("no_flow");
                exit();   
            }
        }
	}

	public function get_Shareinfo($user_type, $user_id,$red_order_code) {
		if ($user_type == 1) {
			$map['proxy_id'] = $user_id;
		} else {
			$map['enterprise_id'] = $user_id;
		}
		$result = M("user_set") -> where($map) -> find();
		$APPID = $result['wx_appid'];
		$APPSECRET = $result['wx_appsecret'];

		//获取ticketcode
		$submiturl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$APPID."&secret=".$APPSECRET;
		$rt = https_request($submiturl);
		$obj = json_decode($rt, true);
		$accesstoken = $obj['access_token'];

		$submiturl = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=".$accesstoken;
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

		$FlowProductTitle = "流量红包";
		$this -> assign("FlowProductTitle", $FlowProductTitle);
		//字符串

		$FlowProductdesc = "大家抢流量红包活动";
		$this -> assign("FlowProductdesc", $FlowProductdesc);
		//字符串

		//分享内容
		$data = $this->localencode($user_type.",".$user_id.",".$red_order_code);
        $Link = gethostwithhttp()."/index.php/Pay/AppRedFlow/index?".$data;


		//$Link = gethostwithhttp() . "/index.php/Pay/AppRedFlow/index/user_type/" . $user_type . "/user_id/" . $user_id;
	
		$this -> assign("Link", $Link);
		//字符串
		//http://sdk.liuliang.net.cn/Application/Sdk/View/FlowRed/images/Share_CheckRed.png
		$localimgUrl = 'http://' . $_SERVER['HTTP_HOST'] . "/Application/Pay/View/AppRedFlow/images/Share_FlowRed.png";

		$this -> assign("localimgUrl", $localimgUrl);
		//字符串
		//分享

		//var FlowProductTitle = "大转盘－流量活动";
		//var FlowProductdesc = "参加每天大转盘，赢海量奖品";
		//var Link = "http://sdk.liuliang.net.cn/index.php/Sdk/RedFlow/index/red_order_id/1/wx_openid/123456/red_order_code/1462688995500234800";
		//var localimgUrl = "http://crm.eoc.cn/resources/crm/upload/public/2016/02/07/9516d2fa-7c3a-4f9c-9ba6-1ad53ed76952.jpg";

	}

	private function place_an_order($phone, $size, $user_type, $user_id) {
		if ($user_type == 1) {
			$proxy_id = $user_id;
			$map['proxy_id'] = $proxy_id;
		} else {
			$enterprise_id = $user_id;
			$map['enterprise_id'] = $enterprise_id;
		}
		$sys_api = M("sys_api") -> where($map) -> find();
		$submiturl = C("API_SUBMIT");
		$phone = $phone;
		$range = 0;
		$size = $size;
		//单位 M
		/*$account    = 'LKKKUZMO';
		 $api_key    = 'KN0lqsyYbWsB65LCZSo13f2zfZhCPdyW';*/
		$account = $sys_api['api_account'];
		$api_key = $sys_api['api_key'];
		$timeStamp = time();
		$pd = array('account' => $account, 'action' => 'Charge', 'phone' => $phone, 'range' => $range, 'size' => $size, 'timeStamp' => $timeStamp, );
		$pre_str = "{$api_key}account={$account}&action=Charge&phone={$phone}&range={$range}&size={$size}&timeStamp={$timeStamp}{$api_key}";
		$pd['sign'] = md5($pre_str);
		$rt = https_request($submiturl, $pd);
		$ret = json_decode($rt, true);
		$fp = fopen("access_token.json", "w");
		fwrite($fp, "APP BUY REQCODE =".$ret['respCode']."size=".$size."account =".$account."api_key=".$api_key."user_type=".$user_type."user_id=".$user_id);
		fclose($fp);

		if ($ret['respCode'] == "0000") {
			return $ret['orderID'];
		} else {
			//return $ret['respMsg'];
			return -1;
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

}
