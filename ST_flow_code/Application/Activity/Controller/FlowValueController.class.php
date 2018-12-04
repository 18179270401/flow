<?php
/*
 * 流量卷活动控制器
 *
 */
namespace Activity\Controller;
use Think\Controller;
class FlowValueController extends Controller {

	public function juanindex()
	{
		//解密
		$rsaKey = $_SERVER["QUERY_STRING"];
		$tmp = stripos($rsaKey, "&");
		if($tmp != false)
		{
			$rsaKey = substr($rsaKey,0,$tmp);
		}
		
		$user_activity_id = $this->localdecode($rsaKey);
        $map['user_activity_id']=$user_activity_id;
        $sua=M("scene_user_activity")->where($map)->find(); //读出活动的信息
        $activity_address = $sua["activity_address"];
		
		if(empty($activity_address))
		{
			$this->flowerror("end");
            exit();
		}
		//测试环节
		$activity_address = str_replace("Sdk/Index/index/","Activity/FlowValue/index/",$activity_address);	
		$activity_address = str_replace("Activity/Index/index/","Activity/FlowValue/index/",$activity_address);	

		if(strpos($activity_address,"user_activity_id") == false)
		{
			//没有找到这个字符串则将信息加到后面去
			$activity_address = $activity_address."/user_activity_id/".$user_activity_id;
		}
		//服务号
        echo "<script language='javascript' type='text/javascript'> window.location='{$activity_address}';</script>";  
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
		//版本号更新
 		$this->assign('version_number', C('VERSION_NUMBER'));

		$mod=trim(I("mod"));
        $func=trim(I("func"));

        $user_type=trim(I("user_type"));
        $user_id=trim(I("user_id"));
      
        $activity_id=trim(I("aid"));
		$user_activity_id = trim(I("user_activity_id"));

		
        if($user_type==1){
            $proxy_id=$user_id;
            $map['proxy_id']=$proxy_id;
        }else{
            $enterprise_id=$user_id;
            $map['enterprise_id']=$enterprise_id;
        }
        $sinfo = M("SceneInfo")->where($map)->find();//读出活动的信息
   
		
        if(empty($user_type)){
       	 	$this->flowerror("end");
            exit();
        }
		
		//页面路径处理
    	$sa=M("scene_activity")->where(array("activity_id"=>$activity_id,"activity_status"=>1))->find();
//		$sa=M("scene_activity")->where(array("activity_id"=>$activity_id))->find();
     	$ssa=explode(',',$sa['activity_file_name']);
		$activetype = $ssa[1];
		
		
        $map['user_activity_id']=$user_activity_id;
        $map['activity_id']=$activity_id;
        $sua=M("scene_user_activity")->where($map)->find(); //读出活动的而信息
        		
        //每个活动对应的图片,如果找不到就去找原来的图片
		$logo_img = $sua['logo_img'];
		if(empty($logo_img))
		{
			$logo_img = $sinfo['logo_img'];
		}
		$propagandat_img = $sua['propagandat_img'];
		if(empty($propagandat_img))
		{
			$propagandat_img = $sinfo['propagandat_img'];
		}
		$background_img = $sua['background_img'];
		if(empty($background_img))
		{
			$background_img = $sinfo['background_img'];
		}


		//lbs定位模块
		//（1.开启，2关闭）
		$position = $sua["lbs_status"];
		if (empty($position)) {
			//如果没有值。则默认关闭
			$position = 2;
		}
        $this->assign("position",$position); 



        $activity_status = $sua['activity_status'];

        $newtime=date("Y-m-d H:i:s" ,time());
        if($newtime>$sua['end_date'] || ($activity_status == 2)){
       	 	$this->flowerror("end");
            exit();
        }elseif($newtime<$sua['start_date']){
        		//活动开始时间
	        $time=str_replace("-", "/",$sua['start_date']);
			$this->assign("start_date",$time);
			
	        $sua['activity_rule']=str_replace("\n", "<br/>",$sua['activity_rule']);
	        $this->assign("activity_rule",$sua['activity_rule']);
       	 	$this->flowerror("unbegin");
            exit();
        }
    
		//获取分享权限
		$this->get_shareuser($sinfo);
		//生成分享信息
		$this->set_shareinfo($user_type,$user_id,$mod,$func,$activity_id,$sua,$user_activity_id);
		//组织开始时间和结束时间
		$activeTime = "活动时间:".$sua['start_date']." 至 ".$sua['end_date'];
        $this->assign("activeTime",$activeTime); 
		
        $role="/Application/Activity/View/FlowVolume/Home/";
        $this->assign("role",$role);
        $this->assign("mod",$mod);
        $this->assign("func",$func); 
        $this->assign("user_activity_id",$user_activity_id);
        $this->assign("user_type",$user_type);
        $this->assign("user_id",$user_id);
        $this->assign("logo_img",$logo_img);
        $this->assign("propagandat_img",$propagandat_img);
        $this->assign("background_img",$background_img);
        $this->assign("active",$activetype);
        $this->assign("activity_id",$activity_id);
        $sua['activity_rule']=str_replace("\n", "<br/>",$sua['activity_rule']);
        $this->assign("activity_rule",$sua['activity_rule']);
        $this->display("FlowVolume/Home/index");
    }

	public function showActivityRule() {
		$user_type=trim(I("user_type"));
        $user_id=trim(I("user_id"));
        $activity_id=trim(I("activity_id"));
        $user_activity_id=trim(I("user_activity_id"));
        if($user_type==1){
            $proxy_id=$user_id;
            $map['proxy_id']=$proxy_id;
        }else{
            $enterprise_id=$user_id;
            $map['enterprise_id']=$enterprise_id;
        }

        if(empty($user_type)){
       	 	$this->flowerror("end");
            exit();
        }

        $map['activity_id']=$activity_id;
        $map['user_activity_id']=$user_activity_id;
		
        $sua=M("scene_user_activity")->where($map)->find(); //读出活动的而信息
        $sua['activity_rule']=str_replace("\n", "<br/>",$sua['activity_rule']);
        $activity_rule = $sua['activity_rule'];
		
		$role = "/Application/Activity/View/FlowVolume/ActivityRule/";
		$this -> assign("role", $role);
		$this->assign("activity_rule",$activity_rule);
		$this -> display("FlowVolume/ActivityRule/index");
	}
	
	private function flowerror($errortype)
	{
		//版本号更新
 		$this->assign('version_number', C('VERSION_NUMBER'));
		if($errortype == "number")
		{
			//次数已用完
            $role="/Application/Activity/View/Trailer/Frequencylimit/";
            $home_url='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->assign("home_url",$home_url);
            $this->assign("role",$role);
            $this->display("Trailer/Frequencylimit/index");
		}
		else if($errortype == "unfollow")
		{
			//$this->error("活动已经结束！");
		    $role="/Application/Activity/View/Trailer/Flowunfollow/";
			$this->assign("role",$role);
		    $this->display("Trailer/Flowunfollow/index");
		}
		else if($errortype == "unbegin")
		{
			//$this->error("活动未开始！");
		    $role="/Application/Activity/View/Trailer/Flowunbegin/";
			$this->assign("role",$role);
		    $this->display("Trailer/Flowunbegin/index");
		} 
		else if($errortype == "zero") {
			//没有中奖
			$role="/Application/Activity/View/Trailer/FlowZero/";
			$this->assign("role",$role);
		    $this->display("Trailer/FlowZero/index");
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

        $activeTime=trim(I("activeTime"));  
        $phone=trim(I("phoneNumber"));  
        $activity_id=trim(I("activity_id"));
        $user_type=trim(I('user_type'));
        $user_id=trim(I("user_id"));
        $active=trim(I("active"));
        $user_activity_id=trim(I("user_activity_id"));

        $mod=trim(I("mod"));
        $func=trim(I('func'));
		$logo_img=trim(I('logo_img'));

        $headimgurl=I("headimgurl");
		if(empty($headimgurl))
		{
			$headimgurl = "juanhead";
		}
        $nickname=I("nickname");
		if(empty($nickname))
		{
			$nickname = "juanname";
		}
		
        $map['user_activity_id']=$user_activity_id;
        $map['activity_id']=$activity_id;
		if($user_type==1){
            $proxy_id=$user_id;
            $map['proxy_id']=$proxy_id;
        }else{
            $enterprise_id=$user_id;
            $map['enterprise_id']=$enterprise_id;
        }
        $sua=M("scene_user_activity")->where($map)->find(); //读出活动的而信息



		$whereflowTicketReceive['mobile'] = $phone;
        $whereflowTicketReceive['user_activity_id']=$user_activity_id;
		//判定该号码是否已经参与过活动。如果参与过。则显示为当前号码参与的活动m
		$ticketinfo = M("ticket_receive") -> where($whereflowTicketReceive) -> find();
		if(!empty($ticketinfo))
		{
			$product_id = $ticketinfo["product_id"];
			if(!empty($product_id))
			{
				$flowSize = D('ChannelProduct')->get_size_by_pid($product_id,$user_id);
			}
        	$this->assign("aid",$user_activity_id);

			if($flowSize<=0) {
				$this->flowerror("zero");
				exit();
			}
			if($flowSize >= 1024)
			{
				$flowSize = intval($flowSize/1024);
				$flowSize = $flowSize."G";
			}
			else
			{
				$flowSize = $flowSize."M";
			}
			//查询链接是否可以用 http
			$user_activity_guide_link = $sua["user_activity_guide_link"];
			if(strpos($user_activity_guide_link,"ttp://") == false)
			{
				$user_activity_guide_link =  'http://'.$user_activity_guide_link;
			}

			//每个活动对应的图片,如果找不到就去找原来的图片
			$logo_img = $sua['logo_img'];
			
			//活动有效期时间 
			$effective_duration = $ticketinfo["effective_duration"];
			//领取时间
			$receive_time = $ticketinfo["receive_time"];
			$receive_soletime = strtotime($receive_time);	//输出 1256112010
			$end_time = $receive_soletime + $effective_duration*3600; 
			//计算有效期毫秒数
			$this->assign("end_time",$end_time* 1000);
			if($end_time < time())
			{
				//过期
				$this->assign("flowticket_status", "2");
			}
			else
			{
				//流量包状态flowticket_status值 流量券状态（默认状态 已兑换、已过期、已失效）0  1  2 3
				$this->assign("flowticket_status", $ticketinfo['flowticket_status']);
			}

      	  	$this->assign("logo_img",$logo_img);
			$this->assign("user_activity_guide_link",$user_activity_guide_link);
			$this->assign("flowSize",$flowSize);
			
			$this->assign("user_type",$user_type);
			$this->assign("user_id",$user_id);
			//获取分享权限
			if($user_type==1){
				$we['proxy_id']=$user_id;
			}else{
				$we['enterprise_id']=$user_id;
			}
			$sinfo=M("SceneInfo")->where($we)->find();//读出活动的信息
			$this->get_shareuser($sinfo);
			
			$role = "/Application/Activity/View/FlowVolume/Trailer/";
			$this->assign("role",$role);
			//生成分享信息
			$this->set_shareinfo($user_type,$user_id,$active,"index",$activity_id,$sua,$user_activity_id);
			$this -> display("FlowVolume/Trailer/index");
			exit();
		}
		if(empty($mod) || empty($func) || empty($active)){
				$this->flowerror("number");
				exit();
		}
		if(empty($phone)){
				$this->error("请输入号码");
		}

		$url=gethostwithhttp()."/index.php/Activity/Api/get_packet_size?phone=".$phone."&user_type=".$user_type."&user_id=".$user_id."&activity_id=".$activity_id."&user_activity_id=".$user_activity_id;
		$rt = https_request($url);
		$role="/Application/Activity/View/Active/".$active."/";
		$this->assign("role",$role);
		$this->assign("rt",$rt);
		$this->assign("active",$active);
		$this->assign("headimgurl",$headimgurl);
		$this->assign("nickname",$nickname);
		$this->assign("user_type",$user_type);
		$this->assign("user_id",$user_id);
		$this->assign("activity_id",$activity_id);
		$this->assign("user_activity_id",$user_activity_id);
		$this->assign("phone",$phone);
		$this->assign("openid",$openid);
		$this->assign("activeTime",$activeTime);
		$this->assign("mod",$mod);
		$this->assign("func",$func);
		//直领流量模式
		$this->assign("type",1);
		$this->display("Active/".$active."/index");
    }

    public function foot(){
		//版本号更新
 		$this->assign('version_number', C('VERSION_NUMBER'));

        $flowSize=I("flowSize");
        $activity_id=I("activity_id");
		$user_activity_id = I("user_activity_id");
        $openid=I("openid");
        $orderid=I("orderid");
        $active=I("active");
        $mobile=I("phone");
        $user_type=I("user_type");
        $user_id=I("user_id");
        $headimgurl=I("headimgurl");
        $nickname=I("nickname");
        if(empty($activity_id)){
       	 	$this->flowerror("end");
            exit();
        }
        if(empty($user_type)){
       	 	$this->flowerror("end");
            exit();
        }
        if($user_type==1){
            $map['proxy_id']=$user_id;
        }else{
            $map['enterprise_id']=$user_id;
        }
        $map["user_activity_id"]=$user_activity_id;
        $map['activity_id']=$activity_id;
        $sua=M("scene_user_activity")->where($map)->find();//获取场景信息 
        //每个活动对应的图片,如果找不到就去找原来的图片
		$logo_img = $sua['logo_img'];
		if(empty($logo_img))
		{
			$logo_img = $sinfo['logo_img'];
		}

		if($flowSize<=0) {
			$this->flowerror("zero");
            exit();
		}
		else if($flowSize >= 1024)
		{
   			$flowSize = intval($flowSize/1024);
			$flowSize = $flowSize."G";
		}
		else
		{
			$flowSize = $flowSize."M";
		}
        $this->assign("flowSize",$flowSize);
        $sua['activity_rule']=str_replace("\n", "<br/>",$sua['activity_rule']);
		$user_activity_guide_link = $sua["user_activity_guide_link"];
		
		
		if(strpos($user_activity_guide_link,"ttp://") == false)
		{
			$user_activity_guide_link =  'http://'.$user_activity_guide_link;
		}

		//活动有效期长
		$ticket_effective_duration = $sua["ticket_effective_duration"];
		$time = time();
		$end_time = $time + $ticket_effective_duration*3600; 
		//计算有效期毫秒数
		$this->assign("end_time",$end_time* 1000);
		//流量卷状态
		$this->assign("flowticket_status",0);


        $this->assign("user_activity_guide_link",$user_activity_guide_link);
        $this->assign("aid",$user_activity_id);
        $this->assign("user_type",$user_type);
        $this->assign("user_id",$user_id);

        $this->assign("activity_rule",$sua['activity_rule']);
        //$this->assign("msg",$msg);
        $this->assign("active",$active);
		
		$role = "/Application/Activity/View/FlowVolume/Trailer/";
        $this->assign("role",$role);
        $this->assign("logo_img",$logo_img);

		//获取分享权限
     	if($user_type==1){
            $we['proxy_id']=$user_id;
        }else{
            $we['enterprise_id']=$user_id;
        }
        $sinfo=M("SceneInfo")->where($we)->find();//读出活动的信息
		$this->get_shareuser($sinfo);
		
		//生成分享信息
		$this->set_shareinfo($user_type,$user_id,$active,"index",$activity_id,$sua,$user_activity_id);
        
		$this -> display("FlowVolume/Trailer/index");
    }



    public function active_result(){
		//版本号更新
 		$this->assign('version_number', C('VERSION_NUMBER'));
        $user_type = I("user_type");
        $user_id = I("user_id");
        $activity_id=trim(I("aid"));
        $mod=trim(I("mod"));
        $func=trim(I('func'));
        $user_activity_id=trim(I("user_activity_id"));
		
        if($user_type==1){
            $proxy_id = $user_id;
            $map['proxy_id']=$proxy_id;
        }else{
            $enterprise_id=$user_id;
            $map['enterprise_id']=$enterprise_id;
        }
        $sinfo=M("SceneInfo")->where($map)->find();//读出活动的信息
        
		//获取分享权限
		$this->get_shareuser($sinfo);
		//生成分享信息	
        $map['user_activity_id']=$user_activity_id;
        $sua=M("scene_user_activity")->where($map)->find();//获取场景信息 


		$this->set_shareinfo($user_type,$user_id,$mod,$func,$activity_id,$sua,$user_activity_id);
		
		
        $role="/Application/Activity/View/Trailer/Frequencylimit/";
        $this->assign("role",$role);
        $this->assign("sharelink",1);
        $this->display("Trailer/Frequencylimit/index");
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
	 public function set_shareinfo($user_type, $user_id,$active,$func,$aid,$sinfo,$user_activity_id){
	 	$sharlink = $sinfo["share_url"];
		if(empty($sharlink))
		{
			$sharlink='http://'.$_SERVER['HTTP_HOST']."/index.php/Activity/FlowValue/index/mod/".$active."/func/".$func."/aid/".$aid."/user_type/".$user_type."/user_id/".$user_id."/user_activity_id/".$user_activity_id;
		}
		
		
		//echo "<script>alert('$active');</script>"; 
        	$this->assign("Link",$sharlink);//字符串
	 	//http://sdk.liuliang.net.cn/index.php/Sdk/Index/index/mod/TigerRoundModule/func/index/aid/6/user_type/2/user_id/53
        	$FlowProductTitle = "流量红包";
	 	if($active == "RoundAboutModule")
	 	{
	 	 	$localimgUrl = 'http://'.$_SERVER['HTTP_HOST']."/Application/Activity/View/Home/HomePage/images/Share_RoundAbout.png";
	 		//流量幸运大转盘
	 		$FlowProductTitle = "转一转得流量";
	 	}
		else if($active == "FlowShakeModule")
		{
	 	 	$localimgUrl = 'http://'.$_SERVER['HTTP_HOST']."/Application/Activity/View/Home/HomePage/images/Share_Shke.png";
			//流量摇一摇
	 		$FlowProductTitle = "摇一摇得流量";
		}
		else if($active == "ScratchModule")
		{
	 	 	$localimgUrl = 'http://'.$_SERVER['HTTP_HOST']."/Application/Activity/View/Home/HomePage/images/Share_ScratchModule.png";
			//流量刮刮乐
	 		$FlowProductTitle = "大家一起刮流量";
		}
		else if($active == "SmashingGoldenEggs")
		{
			//砸金蛋
	 		$FlowProductTitle = "大家一起砸流量";
	 	 	$localimgUrl = 'http://'.$_SERVER['HTTP_HOST']."/Application/Activity/View/Home/HomePage/images/Share_Egg.png";
		}
		else if($active == "TigerRoundModule")
		{
	 	 	$localimgUrl = 'http://'.$_SERVER['HTTP_HOST']."/Application/Activity/View/Home/HomePage/images/Share_TigerRound.png";
			//幸运大抽奖
	 		$FlowProductTitle = "幸运大抽奖";
		}
		else if($active == "Slotmachine")
		{
	 	 	$localimgUrl = 'http://'.$_SERVER['HTTP_HOST']."/Application/Activity/View/Home/HomePage/images/Share_Slotmachine.png";
			//流量老虎机
	 		$FlowProductTitle = "全民摇摇乐";
		}
        	
        	$FlowProductdesc = "海量流量大家来抢，更多活动大家参与！";
			
		//从数据库中读取信息	
		if(!empty($sinfo["share_title"]))
		{
	   		$FlowProductTitle = $sinfo["share_title"];
		}
    	 	if(!empty($sinfo["share_content"]))
		{
	   		$FlowProductdesc = $sinfo["share_content"];
		}
    	 	if(!empty($sinfo["share_img"]))
		{
	   		$localimgUrl = 'http://'.$_SERVER['HTTP_HOST'].$sinfo["share_img"];
		}
        	$this->assign("FlowProductTitle",$FlowProductTitle);//标题
        	$this->assign("FlowProductdesc",$FlowProductdesc);//字符串
        	//http://sdk.liuliang.net.cn/Application/Sdk/View/FlowRed/images/Share_CheckRed.png
        	$this->assign("localimgUrl",$localimgUrl);//字符串
    }

}