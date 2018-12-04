<?php
/*
 * 微信服务号流量活动控制器
 *
 */
namespace Activity\Controller;
use Think\Controller;
class IndexController extends Controller {

    public function _initialize(){
        Vendor("WxPayR.Api");
        Vendor("WxPayR.JsApiPay");
        Vendor("WxPayR.WxPayConfig");
        Vendor('WxPayR.WxPayData');
        Vendor('WxPayR.Exception');
    }
	
	public function aindex()
	{
		$rsaKey = $_SERVER["QUERY_STRING"];
		$tmp = stripos($rsaKey, "&");
		if($tmp != false)
		{
			$rsaKey = substr($rsaKey,0,$tmp);
		}
		
		
		
		$user_activity_id = $this->localdecode($rsaKey);
        $map['user_activity_id']=$user_activity_id;
        $sua=M("scene_user_activity")->where($map)->find(); //读出活动的而信息
//		localhost/index.php/Activity/Index/aindex?RQ==
        $activity_address = $sua["activity_address"];
		
		
		if(empty($activity_address))
		{
			$this->flowerror("end");
            exit();
		}
		
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
		//版本号更新css js更新
 		$this->assign('version_number', C('VERSION_NUMBER'));

        $user_type=trim(I("user_type"));
        $user_id=trim(I("user_id"));
        $mod=trim(I("mod"));
        $func=trim(I("func"));
        $activity_id=trim(I("aid"));
        $openid=trim(I("openid"));
		$user_activity_id = trim(I("user_activity_id"));
		
		
		
        if(empty($user_type)){
       	 	$this->flowerror("end");
            exit();
        }
		$map['user_type'] = $user_type;
        if($user_type==1){
            $proxy_id=$user_id;
            $map['proxy_id']=$proxy_id;
        }else{
            $enterprise_id=$user_id;
            $map['enterprise_id']=$enterprise_id;
        }
        //$result=M("user_set")->where($map)->find();//读出
        
        $sinfo=M("SceneInfo")->where($map)->find();//读出活动的信息s
		
//      if(!$result){
//     	 	$this->flowerror("end");
//          exit();
//      }
		
		
        $this->getlocaluserinfo($sinfo,$user_type,$user_id,$openid);
		//方便订阅号传递消息
		if(empty($openid))
		{
			$openidkey = "samton".$user_type."_".$user_id."openid";
			
			$openid 	= cookie($openidkey);
			if(empty($openid)){
				$config=$this->getconfig($user_type,$user_id);
				$tools = new \JsApiPay($config);
				$openid=$tools->GetOpenid();
			}
		}
		else
		{
			$openidkey = "samton".$user_type."_".$user_id."openid";
			
			cookie($openidkey,$openid);
		}

		$this->assign("openid",$openid);
		
		//获取openid 和headurl 和nickname
		
		//页面路径处理
//  	$sa=M("scene_activity")->where(array("activity_id"=>$activity_id,"activity_status"=>1))->find();
		$sa=M("scene_activity")->where(array("activity_id"=>$activity_id))->find();
     	$ssa=explode(',',$sa['activity_file_name']);
		$activetype = $ssa[1];
		
		//通过活动类型和活动id
        $map['activity_id']=$activity_id;
        $map['user_activity_id']=$user_activity_id;
		
        $sua=M("scene_user_activity")->where($map)->find(); //读出活动的而信息

		//获取分享权限
		$this->get_shareuser($sinfo);

		//生成分享信息
		$this->set_shareinfo($user_type,$user_id,$mod,$func,$activity_id,$sua,$user_activity_id);
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
        //$sua['frequency'] 1为每天，2为每周，3为每月，4为开始-结束
        if($sua['frequency']==1){
            $start_date=date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
            $end_date=date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1);
        }elseif($sua['frequency']==2){
            $start_date=date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d')-date('w')+1,date('Y')));
            $end_date=date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d')-date('w')+7,date('Y')));
        }elseif($sua['frequency']==3){
            $start_date=date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
            $end_date=date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
        }else{
            $start_date=$sua['start_date']; 
            $end_date=$sua['end_date'];
        }
		
		//组织开始时间和结束时间
		$activeTime = "活动时间:".$sua['start_date']." 至 ".$sua['end_date'];
        $this->assign("activeTime",$activeTime); 
		
        $where['receive_date']=array('between',array(start_time($start_date),end_time($end_date)));
        $where['openid']=$openid;
        $where['user_activity_id']=$user_activity_id;
        $sr=M("scene_record")->where($where)->count();
        //数量不足的时候报错
        if($sr>=$sua['number']){
       	 	$this->flowerror("number");
            exit();
        }
        $role="/Application/Activity/View/Home/HomePage/";
        $this->assign("role",$role);
        $this->assign("mod",$mod);
        $this->assign("func",$func); 
        //$this->assign("openid",$openid);
        $this->assign("user_type",$user_type);
        $this->assign("user_id",$user_id);
        $this->assign("logo_img",$logo_img);
        $this->assign("propagandat_img",$propagandat_img);
        $this->assign("background_img",$background_img);
        $this->assign("active",$activetype);
		//活动类型
        $this->assign("activity_id",$activity_id);
		//活动id
        $this->assign("user_activity_id",$user_activity_id);
        $sua['activity_rule']=str_replace("\n", "<br/>",$sua['activity_rule']);
        $this->assign("activity_rule",$sua['activity_rule']);
        $this->display("Home/HomePage/index");
    }
	
	private function flowerror($errortype)
	{
		//版本号更新css js更新
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
		else
		{
            //$this->error("活动已经结束！");
            $role="/Application/Activity/View/Trailer/FlowEnded/";
			$this->assign("role",$role);
            $this->display("Trailer/FlowEnded/index");
		}
	}
	
    public function active(){
		//版本号更新css js更新
 		$this->assign('version_number', C('VERSION_NUMBER'));
		 
        $activeTime=trim(I("activeTime"));  
        $phone=trim(I("phoneNumber"));  
        $active=trim(I("active"));
        $openid=trim(I("openid"));
        $activity_id=trim(I("activity_id"));
        $user_activity_id=trim(I("user_activity_id"));
        $headimgurl=I("headimgurl");
        $nickname=I("nickname");
        $mod=trim(I("mod"));
        $func=trim(I('func'));
        $user_type=trim(I('user_type'));
        $user_id=trim(I("user_id"));
		$logo_img=trim(I('logo_img'));
        if(empty($openid)){
			  $openid = cookie('openid');
        }
		
		
        if(empty($mod) || empty($func) || empty($active)){
       	 	$this->flowerror("end");
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
	  	$this->assign("type",0);
       	$this->display("Active/".$active."/index");
    }

    public function foot(){
		//版本号更新css js更新
 		$this->assign('version_number', C('VERSION_NUMBER'));
		 
        $flowSize=I("flowSize");
        $activity_id=I("activity_id");
		$user_activity_id = I("user_activity_id");
        $openid=I("openid");
        $orderid=I("orderid");
        $headimgurl=I("headimgurl");
        $nickname=I("nickname");
        $active=I("active");
        $mobile=I("phone");
        $user_type=I("user_type");
        $user_id=I("user_id");
        if(empty($activity_id)){
       	 	$this->flowerror("end");
            exit();
        }
        if(empty($user_type)){
       	 	$this->flowerror("end");
            exit();
        }

        $map["activity_id"]=$activity_id;
        if($user_type == 1){
            $map['user_type']=1;
            $map['proxy_id']=$user_id;
        }else{
            $map['user_type']=2;
            $map['enterprise_id']=$user_id;
        }
        $map['activity_id']=$activity_id;
        $map['user_activity_id']=$user_activity_id;
        $sua=M("scene_user_activity")->where($map)->find();//获取场景信息 
		$logo_img = $sua['logo_img'];
	
        
       	if($flowSize >= 1024)
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
		
        $this->assign("activity_id",$activity_id);
        $this->assign("user_type",$user_type);
        $this->assign("user_id",$user_id);
		
        $this->assign("activity_rule",$sua['activity_rule']);
        //$this->assign("msg",$msg);
        $this->assign("active",$active);
        $role="/Application/Activity/View/Trailer/trailerRedTraffic/";
        $this->assign("role",$role);

        $this->assign("logo_img",$logo_img);
        $this->assign("user_activity_id",$user_activity_id);


		//获取分享权限
		if($proxy_id>0){
            $we['proxy_id']=$user_id;
        }else{  
            $we['enterprise_id']=$user_id;
        }
        $sinfo=M("SceneInfo")->where($we)->find();//读出活动的信息
		$this->get_shareuser($sinfo);
		if(empty($logo_img))
		{
			$logo_img = $sinfo['logo_img'];
		}

		//生成分享信息
		$this->set_shareinfo($user_type,$user_id,$active,"index",$activity_id,$sua,$user_activity_id);
        $this->display("Trailer/trailerRedTraffic/index");
    }


	 public function get_userinfo($result){
		$APPID = $result['active_appid'];
		$APPSECRET = $result['active_appsecret'];
        if(!isset($_GET['code'])){
	    		$oldurl='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	    		$url= "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$APPID."&redirect_uri=".$oldurl."&scope=snsapi_userinfo&response_type=code&state=123#wechat_redirect";
	    		Header("Location: $url");
        }
	  
        $code = $_GET['code'];
        $submiturl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$APPID."&secret=".$APPSECRET."&code=".$code."&grant_type=authorization_code";
        $rt = https_request($submiturl);
        $obj = json_decode($rt,true); 
        $accesstoken = $obj['access_token'];
        $Openid=$obj['openid'];

		//$submiturl = "https://api.weixin.qq.com/sns/userinfo?access_token=".$accesstoken."&openid=" .$Openid;
			
		$submiturl = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$accesstoken."&openid=" .$Openid. "&lang=zh_CN";
	    $retrnrt = $this->httpGet($submiturl);
        $retrnrtobj = json_decode($retrnrt,true);
        $headimgurl = $retrnrtobj['headimgurl'];
        $nickname = $retrnrtobj['nickname'];
        $newstr = substr($retrnrtobj['headimgurl'],0,strlen($retrnrtobj['headimgurl'])-1); 
        $headimgurl = $newstr."64";
		
			

        $this->assign("openid",$Openid);
        $this->assign("headimgurl",$headimgurl);
        $this->assign("nickname",$nickname);    
		
	    cookie('access_token',$accesstoken);
        cookie('useropenid',$Openid);
        cookie('access_token',$accesstoken);
        cookie("headimgurl",$headimgurl);
		
    }

 	public function getlocaluserinfo($result,$user_type,$user_id,$openid){
		$pd = "";
		$APPID = $result['active_appid'];
		$APPSECRET = $result['active_appsecret'];
		
		$openidkey = "samton".$user_type."_".$user_id."openid";
		if(empty($openid))
		{
		    $config=array();
		    $config['APPID']=$APPID;
		    $config['APPSECRET']=$APPSECRET;
	        $tools = new \JsApiPay($config);	
	        $openid=$tools->GetOpenid();
        	cookie($openidkey,$openid);
		}
	 	$this->assign("openid",$openid);

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
			
	       	$this->assign("headimgurl",$headimgurl);
	        $this->assign("nickname",$nickname);
				
	        cookie('headimgurl',$headimgurl);
	        cookie('nickname',$nickname);
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
       	 	$this->flowerror("unfollow");
            exit();
			
			//$this->get_userinfo($result);
		}
    }

    public function active_result(){
		//版本号更新css js更新
 		$this->assign('version_number', C('VERSION_NUMBER'));
		 
        $user_type = I("user_type");
        $user_id = I("user_id");
        $activity_id=trim(I("aid"));
        $user_activity_id=trim(I("user_activity_id"));
        $mod=trim(I("mod"));
        $func=trim(I('func'));
		
		
        if($user_type==1){
            $proxy_id=$user_id;
            $map['proxy_id']=$proxy_id;
        }else{
            $enterprise_id=$user_id;
            $map['enterprise_id']=$enterprise_id;
        }
        $sinfo=M("SceneInfo")->where($map)->find();//读出活动的信息
		
        $map['user_activity_id']=$user_activity_id;
        $sua=M("scene_user_activity")->where($map)->find();//获取场景信息 
		//获取分享权限
		$this->get_shareuser($sinfo);
		//生成分享信息
		$this->set_shareinfo($user_type,$user_id,$mod,$func,$activity_id,$sua,$user_activity_id);
		
        $role="/Application/Activity/View/Trailer/Frequencylimit/";
        $this->assign("role",$role);
        $this->assign("sharelink",1);
        $this->display("Trailer/Frequencylimit/index");
    }
	
	public function active_location(){
        $user_type = I("user_type");
        $user_id = I("user_id");
        $activity_id=trim(I("aid"));
        $userlongitude= I("longitude");
        $userlatitude = I("latitude");


		if($user_type==1){
            $map['proxy_id']=$user_id;
        }else{
            $map['enterprise_id']=$user_id;
        }
        $map['user_activity_id']=$activity_id;
        $sua=M("scene_user_activity")->where($map)->find(); //读出活动的而信息
	
		//经纬度
		$point = $sua["point"];

        $InfoArray = explode(",",$point);
			
		//纬度
		$latitude = $InfoArray[1];
		//经度
		$longitude = $InfoArray[0];
		//如果当前没有位置则定位为万达写字楼
		if(!$latitude)
		{
			$latitude = 28.701601;
		}
		if(!$longitude)
		{
			$longitude = 115.966275;
		}
		
		//中心区域活动范围
		$enterpriseaccuracy = $sua["accuracy"]*1000;
		
		//地图加密处理
        $this->assign("ak","PeRgT2l367OTeS4m0KpWWrnjc5woO54A");
		//用户地理位置
        $this->assign("userlatitude",$userlatitude);
        $this->assign("userlongitude",$userlongitude);
		

		//商家地理位置
        $this->assign("latitude",$latitude);
        $this->assign("longitude",$longitude);
		$role="/Application/Activity/View/Trailer/Flowunlocation/";
		$this->assign("role",$role);
		$this->display("Trailer/Flowunlocation/index");
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
			$sharlink='http://'.$_SERVER['HTTP_HOST']."/index.php/Activity/Index/index/mod/".$active."/func/".$func."/aid/".$aid."/user_type/".$user_type."/user_id/".$user_id."/user_activity_id/".$user_activity_id;
		}
	 	
		//echo "<script>alert('$active');</script>"; 
        	$this->assign("Link",$sharlink);//字符串
        	
        	
        	
	 	//http://Activity.liuliang.net.cn/index.php/Activity/Index/index/mod/TigerRoundModule/func/index/aid/6/user_type/2/user_id/53
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
		
		//获取分享权限
        	$this->assign("FlowProductTitle",$FlowProductTitle);//标题
        	$this->assign("FlowProductdesc",$FlowProductdesc);//字符串
        	$this->assign("localimgUrl",$localimgUrl);//字符串
    }

     public function  getconfig($user_type,$user_id){
     	
      $map['$user_type']=$user_type;
      if($user_type==1){
          $map['proxy_id']=$user_id;
      }else{
          $map['enterprise_id']=$user_id;
      }
      $sinfo=M("SceneInfo")->where($map)->find();//读出活动的信息
      //$result=M("user_set")->where($map)->find();
      $config=array();
      $config['APPID']=$sinfo['active_appid'];
      $config['APPSECRET']=$sinfo['active_appsecret'];
//    $config['MCHID']=$sinfo['wx_mchid'];
//    $config['KEY']=$sinfo['wx_key'];
      return $config;
    }

	//解密
	protected function CallbackRSAData($data)
	{
		$decrypted = "";
		$private_key = file_get_contents("./Public/RSAKEYM/rsa_private_key.pem");
		$pidKey = openssl_pkey_get_private($private_key);  
		//$pi_key =  openssl_pkey_get_private($private_key);//这个函数可用来判断私钥是否是可用的，可用返回资源
		$data = base64_decode($data);
		openssl_private_decrypt($data,$decrypted,$pidKey);//私钥解密
		return  $decrypted;
	}
}
