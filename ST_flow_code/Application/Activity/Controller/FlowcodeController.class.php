<?php
/*
 * 流量码活动控制器
 *
 */
namespace Activity\Controller;
use Think\Controller;
class FlowcodeController extends Controller {

    public function _initialize(){
        Vendor("WxPayR.Api");
        Vendor("WxPayR.JsApiPay");
        Vendor("WxPayR.WxPayConfig");
        Vendor('WxPayR.WxPayData');
        Vendor('WxPayR.Exception');
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

    public function index(){
		$rsaKey = $_SERVER["QUERY_STRING"];
		$tmp = stripos($rsaKey, "&");
		if($tmp != false)
		{
			$rsaKey = substr($rsaKey,0,$tmp);
		}

		$strArray = $this->localdecode($rsaKey);
		$InfoArray = explode(",",$strArray);
		$user_type = $InfoArray[0];
		$user_id = $InfoArray[1];

		// $user_type=trim(I("user_type"));
        // $user_id=trim(I("user_id"));
		//版本号更新
 		$this->assign('version_number', C('VERSION_NUMBER'));

    
		
        if($user_type==1){
            $proxy_id=$user_id;
            $map['proxy_id']=$proxy_id;
        }else{
            $enterprise_id=$user_id;
            $map['enterprise_id']=$enterprise_id;
        }
        $flowcodeinfo = M("flowcode_set")->where($map)->find();//读出活动的信息
        if(empty($flowcodeinfo)){
            //已作废
       	 	$this->flowerror("end");
            exit();
        }
        
		//图片资源
	   	$background_img = 'http://'.$_SERVER['HTTP_HOST'].$flowcodeinfo['background_img'];
		$this->assign("background_img",$background_img);

        //http://localhost/index.php/Activity/Flowcode/index/aid/3/user_type/2/user_id/139/user_activity_id/134
        //说明信息
	    $sua['activity_rule']=str_replace("\n", "<br/>",$flowcodeinfo['activity_rule']);
	    $this->assign("activity_rule",$flowcodeinfo['activity_rule']);


        $newtime=date("Y-m-d H:i:s" ,time());

        if($newtime>$flowcodeinfo['end_time']&& !empty($flowcodeinfo['end_time'])){
       	 	$this->flowerror("end");
            exit();
        }elseif($newtime<$flowcodeinfo['start_time']){
        		//活动开始时间
	        $time=str_replace("-", "/",$flowcodeinfo['start_time']);
			$this->assign("start_date",$time);
       	 	$this->flowerror("unbegin");
            exit();
        }

        //获取微信公众号信息
     	if($user_type==1){
            $we['proxy_id']=$user_id;
        }else{
            $we['enterprise_id']=$user_id;
        }
        $sinfo = M("SceneInfo")->where($we)->find();//读出活动的信息
        //$wxinfo = $this->getwxuserinfo("",$user_id,$user_type,$sinfo);
        //获取openid
        $this->getopenid($user_type,$user_id,$sinfo);
		//获取分享权限
		$this->get_shareuser($sinfo);
		// //生成分享信息
		$this->set_shareinfo($user_type,$user_id,$flowcodeinfo);
		//组织开始时间和结束时间
		$activeTime = "活动时间:".$sua['start_time']." 至 ".$sua['end_time'];
        $this->assign("activeTime",$activeTime); 
		
        $role="/Application/Activity/View/Flowcode/";
        $this->assign("role",$role);

        $this->assign("user_type",$user_type);
        $this->assign("user_id",$user_id);
        $this->display("Flowcode/index");
    }

	public function showActivityRule() {
		$user_type=trim(I("user_type"));
        $user_id=trim(I("user_id"));

        if($user_type==1){
            $proxy_id=$user_id;
            $map['proxy_id']=$proxy_id;
        }else{
            $enterprise_id=$user_id;
            $map['enterprise_id']=$enterprise_id;
        }
        $flowcodeinfo = M("flowcode_set")->where($map)->find();//读出活动的信息
        if(empty($user_type)){
       	 	$this->flowerror("end");
            exit();
        }

        $activity_rule = str_replace("\n", "<br/>",$flowcodeinfo['activity_rule']);
		
		$role = "/Application/Activity/View/Flowcode/";
		$this -> assign("role", $role);
		$this->assign("activity_rule",$activity_rule);
		$this -> display("Flowcode/ActivityRule");
	}
	
	private function flowerror($errortype)
	{
		//版本号更新
 		$this->assign('version_number', C('VERSION_NUMBER'));
		if($errortype == "unbegin")
		{
			//$this->error("活动未开始！");
		    $role="/Application/Activity/View/Trailer/Flowunbegin/";
			$this->assign("role",$role);
		    $this->display("Trailer/Flowunbegin/index");
		} 
		else if($errortype == "zero") {
			//没有中奖
			$role="/Application/Activity/View/Flowcode/FlowZero/";
			$this->assign("role",$role);
		    $this->display("Trailer/FlowZero/index");
		}
		else if($errortype == "active") {
			//没有中奖
			$role="/Application/Activity/View/Flowcode/";
			$this->assign("role",$role);
			$this->assign("bgimage","active.png");
		    $this->display("Flowcode/fail");
		}
		else if($errortype == "cancelled") {
			//已失效
			$role="/Application/Activity/View/Flowcode/";
			$this->assign("role",$role);
			$this->assign("bgimage","cancelled.png");
		    $this->display("Flowcode/fail");
		}
		else if($errortype == "used") {
			//已使用
			$role="/Application/Activity/View/Flowcode/";
			$this->assign("role",$role);
			$this->assign("bgimage","used.png");
		    $this->display("Flowcode/fail");
		}
		else if($errortype == "chinamobile") {
			//中国移动
			$role="/Application/Activity/View/Flowcode/";
			$this->assign("role",$role);
			$this->assign("bgimage","chinamobile.png");
		    $this->display("Flowcode/fail");
		}
		else if($errortype == "unicom") {
			//中国联通
			$role="/Application/Activity/View/Flowcode/";
			$this->assign("role",$role);
			$this->assign("bgimage","unicom.png");
		    $this->display("Flowcode/fail");
		}
		else if($errortype == "telecom") {
			//中国电信
			$role="/Application/Activity/View/Flowcode/";
			$this->assign("role",$role);
			$this->assign("bgimage","telecom.png");
		    $this->display("Flowcode/fail");
		}
		else if($errortype == "gdgovonly")
		{
			//只限广东省省用户使用
			$role="/Application/Activity/View/Flowcode/";
			$this->assign("role",$role);
			$this->assign("bgimage","gdgovonly.png");
		    $this->display("Flowcode/fail");
		}
		else
		{
            //$this->error("活动已经结束！");
            $role="/Application/Activity/View/Trailer/FlowEnded/";
			$this->assign("role",$role);
            $this->display("Trailer/FlowEnded/index");
		}
	}
	
    public function active()
	{
		//版本号更新css js更新
 		$this->assign('version_number', C('VERSION_NUMBER'));

        $flowcode_code = trim(I("flowcode_code"));  
        $phone = trim(I("phoneNumber"));  
        $user_type = trim(I('user_type'));
        $user_id = trim(I('user_id'));
        $openid = trim(I('openid'));


		if($user_type == 1){
            $proxy_id = $user_id;
            $map['proxy_id'] = $proxy_id;
        }else{
            $enterprise_id = $user_id;
            $map['enterprise_id'] = $enterprise_id;
        }
        $map['flowcode_code']=$flowcode_code;
        $flowcode = M("flowcode")->where($map)->find(); //读出活动的而信息
        //查询系统api
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
        $flowcodeinfo = M("flowcode_set")->where($record)->find();//读出活动的信息
        //获取logo图片
//		$logo_img = $flowcodeinfo['logo_img'];
	   	$logo_img = 'http://'.$_SERVER['HTTP_HOST'].$flowcodeinfo['logo_img'];
		$this->assign("logo_img",$logo_img);

        //判断当前卷的状态状态（1.未激活2.已激活3.已使用4.已作废）
        $codestate = $flowcode["status"];
        switch($codestate)
        {
            case 1:
            {
                //未激活ro/index");
       	 		$this->flowerror("active");
                exit();
            }
            break;
            case 2:
            {
                //已激活 可用
            }
            break;
            case 3:
            {
                //已使用
       	 		$this->flowerror("used");
                exit();
            }
            break;
            case 4:
            {
                //已作废
       	 		$this->flowerror("cancelled");
                exit();
            }
            break;
			default:
			{
                //不存在
       	 		$this->flowerror("active");
                exit();
			}
			break;
        }

        //判定是否有号码已经用
        $code_phone = $flowcode["code_phone"];
        if(!empty($code_phone))
        {
            //如果不为空 则表示该兑换码已经兑换
            //已作废
       	 	$this->flowerror("cancelled");
            exit();
        }
        $code_phone = $phone;

        //如果为空 则判定其时间
        //判定是否超过截止时间则结束
        $newtime=date("Y-m-d H:i:s" ,time());
		if(!empty($flowcode["end_time"]))
		{	
			if($newtime > $flowcode['end_time']){
				$this->flowerror("cancelled");
				exit();
			}
		}

        //区域
        $type = $flowcode["type"];//属性（1.全国，2.广东省） 
        //产品名称
        $product_name = $flowcode["product_name"];
        //产品大小
        $size   = $flowcode["size"];
		
        //查询手机归属地
		$phoneinfo = CheckMobile($code_phone);
		//检测是否为全国包,如果不是 就检测区域
		$type = $flowcode["type"];
		if($type != 1)
		{
			//广东省
			if($phoneinfo["province_id"] != 24)
			{
				$this->flowerror("gdgovonly");
				exit();
			}
			//判断流量码使用的对应客户
			$operator_id = $flowcode["operator_id"];
			//如果运营商不同则处理好
			if($operator_id != $phoneinfo["operator_id"])
			{
				$this->operatorType($operator_id);
			}
		}
		
		
        $wxinfo = $this->getwxuserinfo($openid,$user_id,$user_type,"");
		$save['wx_photo'] = $wxinfo['headimgurl'];
		$save['wx_name'] = $wxinfo['nickname'];

		$save["operator_id"] = $phoneinfo["operator_id"];
        $save['modify_user_id'] = D("SysUser")->self_id();
        $save['modify_date'] = $newtime;
        $save["phone"] = $code_phone;
        $save["status"] = 3;//已使用
        M("flowcode")->where($map)->save($save); //读出活动的而信息

        //下单
		$respinfo = $this->Apiflowsubmit($size,$code_phone,$sys_api,$type);

        //exit();
        $respsave["order_time"] = $newtime;//下单时间
        $respsave["order_code"] = $respinfo["orderID"];//下单id
        M("flowcode")->where($map)->save($respsave); //读出活动的而信息
        //跳转至活动结束页面
                //查询系统api
		$listselect['user_type'] = $user_type;
		if ($user_type == 1) {
			$listselect['proxy_id'] = $user_id;
			$listselect['enterprise_id'] = 0;
		} else {
			$listselect['enterprise_id'] = $user_id;
			$listselect['proxy_id'] = 0;
		}
		//微信名称
		$this->assign("wx_name",$wxinfo['wx_name']);
		//微信头像
		$this->assign("wx_photo", $wxinfo['headimgurl']);
		$save['wx_name'] = $wxinfo['nickname'];
        //已使用
        $listselect["status"] = 3;
        //获取列表
        $this->rewarded_users($listselect);
        //包名称
		$this->assign("product_name",$product_name);
        //电话号码
		$this->assign("phone",$phone);
        //手机归属地省市区 例如：江西省南昌市
        $phoneaddress = $phoneinfo["province_name"].$phoneinfo["city_name"];
		$this->assign("phoneaddress",$phoneaddress);
        //运营商名称：例如：中国电信
        $operator_name = $phoneinfo["operator_name"];
		$this->assign("operator_name",$operator_name);
        $role="/Application/Activity/View/Flowcode/";
		$this->assign("role",$role);
        
        $this->display("Flowcode/foot");
    }

	private function operatorType($type)
	{
		switch($type)
		{
			//中国移动
			case 1:
			{
				$this->flowerror("chinamobile");
				exit();
			}
			break;
			//中国联通
			case 2:
			{
				$this->flowerror("unicom");
				exit();
			}
			break;
			//中国电信
			case 3:
			{
				$this->flowerror("telecom");
				exit();
			}
			break;
		}
	}
	//对外api接口 h5获取流量所有包
	public function CheckMobile() {
		
		$phone = trim(I("phone"));
        //查询手机归属地
		$phoneinfo = CheckMobile($phone);
        //手机归属地省市区 例如：江西省南昌市
        $phoneaddress = $phoneinfo["province_name"].$phoneinfo["city_name"];
		//组装返回数组
		$packet = array('phoneaddress' => $phoneaddress);
		//返回数据
		$this -> ajaxReturn($packet);
	}

 	public function getopenid($user_type,$user_id){
             //获取微信公众号信息
     	if($user_type==1){
            $we['proxy_id']=$user_id;
        }else{
            $we['enterprise_id']=$user_id;
        }
        $sinfo = M("SceneInfo")->where($we)->find();//读出活动的信息


		$pd = "";
		$APPID = $sinfo['active_appid'];
		$APPSECRET = $sinfo['active_appsecret'];
		$openidkey = "samton".$user_type."_".$user_id."openid";
        $openid;// = cookie($openidkey);
		if(empty($openid))
		{
		    $config=array();
		    $config['APPID'] = $APPID;
		    $config['APPSECRET']=$APPSECRET;
	        $tools = new \JsApiPay($config);	
	        $openid=$tools->GetOpenid();
            //如果用户未关注公众号。则进行授权请求
          
        	cookie($openidkey,$openid);
		}
	 	$this->assign("openid",$openid);
    }

    private function getwxuserinfo($openid,$user_id,$user_type,$sinfo)
    {
        if(empty($sinfo))
        {
             //获取微信公众号信息
            if($user_type==1){
                $we['proxy_id']=$user_id;
            }else{
                $we['enterprise_id']=$user_id;
            }
            $sinfo = M("SceneInfo")->where($we)->find();//读出活动的信息
        }

		$APPID = $sinfo['active_appid'];
		$APPSECRET = $sinfo['active_appsecret'];
	
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
	        $newstr = substr($retrnrtobj['headimgurl'],0,strlen($retrnrtobj['headimgurl'])-1); 
			
			$headimgurl = $newstr."64";
			$nickname = $retrnrtobj['nickname'];
			
		    return array("headimgurl"=>$headimgurl,"nickname"=>$nickname);
		}
        else
        {
			//当用户未关注时。让用户跳转授权页面进行授权
			 $code = $_GET["code"];
            if(empty($code))
            {
                    $redirect_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                    $submiturl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$APPID."&redirect_uri=".$redirect_url."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
				
                    echo "<script language='javascript' type='text/javascript'> window.location='{$submiturl}';</script>";  	
					exit();
            }
                //获取信息之后
            $submiturl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$APPID."&secret=".$APPSECRET."&code=".$code."&grant_type=authorization_code";
            $rt = https_request($submiturl);
            $obj = json_decode($rt,true); 
            $accesstoken = $obj['access_token'];
            $openid = $obj['openid'];

            //通过openid获取用户信息
            $submiturl = "https://api.weixin.qq.com/sns/userinfo?access_token=".$accesstoken."&openid=" .$openid."&lang=zh_CN";
            $retrnrt = $this->httpGet($submiturl);
            $retrnrtobj = json_decode($retrnrt,true);
	        $newstr = substr($retrnrtobj['headimgurl'],0,strlen($retrnrtobj['headimgurl'])-1); 
			$headimgurl = $newstr."64";
			$nickname = $retrnrtobj['nickname'];
			
		    return array("headimgurl"=>$headimgurl,"nickname"=>$nickname);
        }
        return array();
    }

    //下单
	private function Apiflowsubmit($size, $phone,$sys_api,$type)
	{
		$submiturl = C("API_SUBMIT");
		$phone = $phone;
		if($type == 1)
		{
			//全国包
			$range = 0;
		}
		else
		{
			$range = 1;
		}
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

    //获取获奖列表
	private function rewarded_users($listselect) {

        $oder_list =M("flowcode")->where($listselect) -> limit("10") -> order('flowcode_id desc') -> select();

		if (!$oder_list) {
			$msg = '没有领取记录';
            exit();
		}
        
		$dt = array();
		foreach ($oder_list as $vo) {
			$da = array();
			$da['wx_photo'] = $vo['wx_photo'];
			$da['wx_name'] = $vo['wx_name'];
			$da['code_phone'] = $vo['phone'];
			$da['product_name'] = $vo['product_name'];
			$da['order_time'] = $vo['order_time'];
			array_push($dt, $da);
		}
        $this->assign("list",json_encode($dt));//时间戳
	}
    //调取分享权限
	public function get_shareuser($result){
		$APPSECRET = $result['active_appsecret'];
		$APPID = $result['active_appid'];
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
	 public function set_shareinfo($user_type,$user_id,$finfo){
	 	$sharlink = $finfo["share_url"];
		if(empty($sharlink))
		{
            $sharlink = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		}
		
		
		//echo "<script>alert('$active');</script>"; 
        $this->assign("Link",$sharlink);//字符串

	 	//http://sdk.liuliang.net.cn/index.php/Sdk/Index/index/mod/TigerRoundModule/func/index/aid/6/user_type/2/user_id/53
        $FlowProductTitle = "流量码上换";
        	
        $FlowProductdesc = "购买产品获得流量码兑换海量流量！";
			
	   	$localimgUrl = 'http://'.$_SERVER['HTTP_HOST']."/Flowcode/imgs/shareicon.png";
		//从数据库中读取信息	
		if(!empty($finfo["share_title"]))
		{   
	   		$FlowProductTitle = $finfo["share_title"];
		}
    	if(!empty($finfo["share_content"]))
		{
	   		$FlowProductdesc = $finfo["share_content"];
		}
    	if(!empty($finfo["share_img"]))
		{
	   		$localimgUrl = 'http://'.$_SERVER['HTTP_HOST'].$finfo["share_img"];
		}
        $this->assign("FlowProductTitle",$FlowProductTitle);//标题
        $this->assign("FlowProductdesc",$FlowProductdesc);//字符串
        	//http://sdk.liuliang.net.cn/Application/Sdk/View/FlowRed/images/Share_CheckRed.png
        $this->assign("localimgUrl",$localimgUrl);//字符串
    }

}