<?php
namespace WXServer\Controller;
use Think\Controller;

class WXReceiveText{
	public function receiveText($object)
	{
		$Openid = $object->FromUserName;
		$Content = $object->Content;
		//做一次字符串转换（重要）
		$Openid = $Openid."";
		// if($Content == "积分")
		// {
		// 	//返回领取流量url链接
		// 	return $this->receivepointvalue($Openid);
		// }
		if($Content == "流量大礼包")
		{
			return $this->receivebigtraffic($Openid);
		}
		else
		{
			return $this->receivetraffic($Openid, $Content);
		}
	}
	//流量大礼包
	public function receivebigtraffic($openid)
	{
	    $user_id = $_GET["user_id"];
	    $user_type = $_GET["user_type"];
		
		//将该用户信息存入
		$openidkey = "samton".$user_type."_".$user_id."openid";
        cookie($openidkey,$openid);
        cookie("receivestatue","2");
		return "请输入您的大礼包密钥";
	}
	
	public function receivepointvalue($openid,$sinfo,$replyactivityinfo)
	{
		$APPID   = $_GET["APPID"];
        // $map['active_appid']=$APPID;
        // $sinfo=M("SceneInfo")->where($map)->find();//读出活动的信息s
		$APPSECRET = $sinfo['active_appsecret'];
		$submiturl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$APPID."&secret=".$APPSECRET;
		$rt = https_request($submiturl);
		$obj = json_decode($rt,true); 
		$accesstoken = $obj['access_token'];
		
		
		$submiturl = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$accesstoken."&openid=" .$openid."&lang=zh_CN";
	
		$retrnrt = https_request($submiturl);
		$retrnrtobj = json_decode($retrnrt,true);
		$subscribe = $retrnrtobj['subscribe'];
		$nickname = $retrnrtobj['nickname'];
		
		if(empty($nickname))
		{
			return "对不起，请管理员通过微信公众号认证才可以接下去的操作";
		}
		
		
	    $newstr = substr($retrnrtobj['headimgurl'],0,strlen($retrnrtobj['headimgurl'])-1); 
		
		$headimgurl = $newstr."64";
			
				
		$openid = $openid."";
		
        $data['wx_openid']=$openid;
		$user_type = $sinfo['user_type'];
        	$data['user_type']=$user_type;
        if($user_type==1)
        {
            $user_id=$sinfo['proxy_id'];
        		$data['proxy_id']=$user_id;
        }else{
            $user_id=$sinfo['enterprise_id'];
        		$data['enterprise_id']=$user_id;
        }
		
		$info = M("wx_user")->where($data)->find();
		if(!$info){
			$data["mobile"] = -1;
	        $data['user_flow_score']=0;
	        $data['wx_photo_url']=$headimgurl;
	        $data['wx_name']=$nickname;
	        $data['last_flow_date']=date("Y-m-d H:i:s" ,time());
	        if(!M("wx_user")->add($data)){
	        		return "大家猜一猜";	
			}
		}
		
        //$result=M("user_set")->where($map)->find();//读出
       // http://192.168.12.4:8001/index.php/PointValueManage/Api/pointValue?openid=333&usertype=2&userid=139
        $link='http://'.$_SERVER['HTTP_HOST']."/index.php/PointValueManage/Api/pointValue?openid=".$openid."&usertype=".$user_type."&userid=".$user_id;
	 	$localimgUrl = 'http://'.$_SERVER['HTTP_HOST']."/Application/WXServer/images/Activity/wx_PointValue.png";

		$FlowProductTitle = "流量积分";
		//标题
		$replyimg_title = $replyactivityinfo["replyactivity_title"];
		if (!empty($replyimg_title)) {
			$FlowProductTitle = $replyimg_title;
		}
		//图片
		$replyimg_img = $replyactivityinfo["replyactivity_img"];
		if (!empty($replyimg_img)) {
			$localimgUrl ='http://'.$_SERVER['HTTP_HOST'].$replyimg_img;
		}
		


		$contactArray = $this->TransStr($FlowProductTitle,$localimgUrl,$link);
        return $contactArray;//$this->shorurl($link,$APPID,$APPSECRET);
	}
	
	//领取流量。返回链接
	public function receivetraffic($openid,$Content)
	{
	    $APPID   = $_GET["APPID"];
        $map['active_appid'] = $APPID;
        $sinfo=M("SceneInfo")->where($map)->find();//读出活动的信息

		
		//id和type的信息
		$user_type = $sinfo["user_type"];
		$usermap["user_type"] = $user_type;
		 if($user_type==1){
		   	$user_id = $sinfo["proxy_id"];
            $usermap['proxy_id']=$user_id;
        }else{
		   	$user_id = $sinfo["enterprise_id"];
            $usermap['enterprise_id']=$user_id;
        }
		$Content = $Content."";
		$usermap['reply_keyword']=$Content;
		//关键词查找
		$sua = M("wxs_reply")->where($usermap)->find(); //读出活动的而信息
		if(!empty($sua))
		{
			//没有找到当前关键词字段
			//回复类型（1文字，2图文。3多图文。4活动）
			$reply_type = $sua["reply_type"];
			//回复关键词id
			$reply_keywordid = $sua["reply_keywordid"];;
			return $this->rpkeyword($reply_type,$reply_keywordid,$openid, $sinfo);
		}
		return "";
	}

	public function rpkeyword($reply_type,$reply_keywordid,$openid,$sinfo)
	{
		switch ($reply_type) {
			case "1":
				{
					return $this->transtext($reply_keywordid);
				}
				break;
			case "2":
				{
					return $this->transpic($reply_keywordid);
				}
				break;
			case "3":
				{
					return $this->transpic($reply_keywordid);
				}
				break;
			case "4":
				{
					return $this->transactivity($reply_keywordid,$openid,$sinfo);
				}
				break;
			
			default:
				return "";
				break;
		}
	}

	//回复纯文字
	private function transtext($activity_id)
	{
		$where["replytext_id"] = $activity_id;
    	$info = M("wxs_replytext")->where($where)->find();

		$replytext_contact = $info["replytext_contact"];
		return $replytext_contact;
	}

	//回复图文
	private function transpic($activity_id)
	{
		$where["replymoreimg_id"] = $activity_id;
    	$info = M("wxs_replymoreimg")->where($where)->find();
		//查找到相关的素材
		$replymoreimg_material = $info["replymoreimg_material"];
		//素材为逗号隔开的数组
		$InfoArray = explode(",",$replymoreimg_material);
		
		$wxrpinfoArray = array();
		foreach($InfoArray as $replyimg_id){
			$replyimg["replyimg_id"] = $replyimg_id;
			$info = M("wxs_replyimg")->where($replyimg)->find();
			//标题
			$replyimg_title = $info["replyimg_title"];
			if(empty($replyimg_title))
			{
				continue;
			}
			
			//图片
			$replyimg_img = $info["replyimg_img"];
			//链接
			$replyimg_url = $info["replyimg_url"];
			//详细内容
			$replyimg_description = $info["replyimg_description"];

			$localimgUrl ='http://'.$_SERVER['HTTP_HOST'].$replyimg_img;
			$wxrpinfo["Title"] = $replyimg_title;
			$wxrpinfo["PicUrl"] = $localimgUrl;
			$wxrpinfo["Url"] = $replyimg_url;
			$wxrpinfo["Description"] = $replyimg_description;
			array_push($wxrpinfoArray,$wxrpinfo);
		}

		return $wxrpinfoArray;
	}

	//回复活动
	private function transactivity($replyactivity_id,$openid,$sinfo)
	{
		$where["replyactivity_id"] = $replyactivity_id;
    	$replyactivityinfo = M("wxs_replyactivity")->where($where)->find();
		$user_activity_id = $replyactivityinfo["user_activity_id"];
		if($user_activity_id == -1)
		{
			return $this->receivepointvalue($openid,$sinfo,$replyactivityinfo);
		}
		//查找当前活动表
		//$activity_id = $sua["activity_id"];
		$map["user_activity_id"] = $user_activity_id;
    	$sua=M("scene_user_activity")->where($map)->find();
		$user_activity_type = $sua["user_activity_type"];
		if($user_activity_type == "2")
		{
			return $this->juranactivity($sua,$openid,$replyactivityinfo);
		}
		return $this->normalactivity($sua,$openid,$replyactivityinfo);
	
	}

	//解密
	public function localencode($data) {
		for($i=0;$i<strlen($data);$i++){
			$ord = ord($data[$i]);
			$ord += 20;
			$string = $string.chr($ord);
		}
        $string = base64_encode($string);
        return $string;
    }

	private function juranactivity($sua,$openid,$replyactivityinfo)
	{
		$user_type = $sua["user_type"];
		if($user_type == 1)
		{
			$user_id = $sua["proxy_id"];
		}
		else 
		{
			$user_id = $sua["enterprise_id"];
		}
		$user_activity_id = $sua["user_activity_id"];
		//分享内容
		$data = $this->localencode($user_type.",".$user_id.",".$user_activity_id.",".$openid);
        $activity_address = gethostwithhttp()."/index.php/Activity/TrafficTicket/index?".$data;


		//标题
		$replyimg_title = $replyactivityinfo["replyactivity_title"];
		if (!empty($replyimg_title)) {
			$FlowProductTitle = $replyimg_title;
		}
		//图片
		$replyimg_img = $replyactivityinfo["replyactivity_img"];
		if (!empty($replyimg_img)) {
			$localimgUrl ='http://'.$_SERVER['HTTP_HOST'].$replyimg_img;
		}
		//详细内容
		$replyimg_description = $replyactivityinfo["replyactivity_description"];

		$contactArray = $this->TransStr($FlowProductTitle,$localimgUrl,$activity_address,$replyimg_description);
		return $contactArray;
	}

	private function normalactivity($sua,$openid,$replyactivityinfo)
	{
		//活动类别
        $activity_id = $sua["activity_id"];
		$FlowProductTitle = "大家一起摇一摇";;
	 	if($activity_id == 1)
	 	{
	 	 	$localimgUrl = 'http://'.$_SERVER['HTTP_HOST']."/Application/WXServer/images/Activity/wx_RoundAbout.png";
	 		//流量幸运大转盘
	 		$FlowProductTitle = "转一转得流量";
	 	}
		else if($activity_id == 2)
		{
	 	 	$localimgUrl = 'http://'.$_SERVER['HTTP_HOST']."/Application/WXServer/images/Activity/wx_Shke.png";
			//流量摇一摇
	 		$FlowProductTitle = "摇一摇得流量";
		}
		else if($activity_id == 3)
		{
	 	 	$localimgUrl = 'http://'.$_SERVER['HTTP_HOST']."/Application/WXServer/images/Activity/wx_ScratchModule.png";
			//流量刮刮乐
	 		$FlowProductTitle = "大家一起刮流量";
		}
		else if($activity_id == 5)
		{
			//砸金蛋
	 		$FlowProductTitle = "大家一起砸流量";
	 	 	$localimgUrl = 'http://'.$_SERVER['HTTP_HOST']."/Application/WXServer/images/Activity/wx_Egg.png";
		}
		else if($activity_id == 6)
		{
	 	 	$localimgUrl = 'http://'.$_SERVER['HTTP_HOST']."/Application/WXServer/images/Activity/wx_TigerRound.png";
			//幸运大抽奖
	 		$FlowProductTitle = "幸运大抽奖";
		}
		else if($activity_id == 4)
		{
	 	 	$localimgUrl = 'http://'.$_SERVER['HTTP_HOST']."/Application/WXServer/images/Activity/wx_Slotmachine.png";
			//流量老虎机
	 		$FlowProductTitle = "全民摇摇乐";
		}

		//将活动id也带入
		$user_activity_id = $replyactivityinfo["user_activity_id"];
		$activity_address = $sua["activity_address"];
		if(strpos($activity_address,"user_activity_id") == false)
		{
			//没有找到这个字符串则将信息加到后面去
			$activity_address = $activity_address."/user_activity_id/".$user_activity_id."/openid/".$openid;
		}
		else
		{
			$activity_address = $activity_address."/openid/".$openid;
		}
		//$activity_address = str_replace("http://test.liuliang.net.cn/","http://Sdk.liuliang.net.cn/",$activity_address);

		//标题
		$replyimg_title = $replyactivityinfo["replyactivity_title"];
		if (!empty($replyimg_title)) {
			$FlowProductTitle = $replyimg_title;
		}
		//图片
		$replyimg_img = $replyactivityinfo["replyactivity_img"];
		if (!empty($replyimg_img)) {
			$localimgUrl ='http://'.$_SERVER['HTTP_HOST'].$replyimg_img;
		}
		//详细内容
		$replyimg_description = $replyactivityinfo["replyactivity_description"];

		$contactArray = $this->TransStr($FlowProductTitle,$localimgUrl,$activity_address,$replyimg_description);
		return $contactArray;
	}

	private function TransStr($Title, $PciUrl,$Url,$Description = "")
	{
		$infoArray = array();
        $info["Title"] = $Title;
        $info["PicUrl"] = $PciUrl;
        $info["Url"] = $Url;
        $info["Description"] = $Description;

        array_push($infoArray,$info);
		return $infoArray;
	}
}
?>