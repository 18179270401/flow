<?php
namespace Sdk\Controller;
use Think\Controller;
class EnterpriseactivityController extends Controller {

	public function index() {
		
        $nowtime=date("Y-m-d H:i:s" ,time());
		$u_stoptime="2016-06-01 00:00:01";
		if($nowtime > $u_stoptime) {
        		$url="http://www.liuliang.net.cn/index.php/Sdk/Index/index/mod/SmashingGoldenEggs/func/index/aid/5/user_type/2/user_id/112";
        		echo "<script language='javascript'type='text/javascript'>window.location='".$url."';</script>";  
			return;
		}
		
        $APPID = "wxc455fa54abeb280d";
        $APPSECRET ="dce1684511a38bb87f78b344255fc419";
		$this->get_userinfo($APPID,$APPSECRET);
		$qrlinkurl = gethostwithhttp()."/index.php/Sdk/Enterpriseactivity/setqrcode";
     	$this->assign("qrlinkurl",$qrlinkurl);
	 
		$role = "/Application/Sdk/View/Enterpriseactivity/";
		$this -> assign("role", $role);
		$this -> display("index");
	}

	public function get_userinfo($APPID,$APPSECRET){
	  
//      $APPID = $result['wx_appid'];
//      $APPSECRET =$result['wx_appsecret'];

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
        	
        	
        	$FlowProductTitle = "六一大抽奖！";
        	$this->assign("FlowProductTitle",$FlowProductTitle);//字符串
        	
        	$FlowProductdesc = "迎美好童年，领6100G流量";
        	$this->assign("FlowProductdesc",$FlowProductdesc);//字符串
        	
        $Link=gethostwithhttp()."/index.php/Sdk/Enterpriseactivity/index/user_type/".$user_type."/user_id/".$user_id;
        
        	$this->assign("Link",$Link);//字符串
        	//http://sdk.liuliang.net.cn/Application/Sdk/View/FlowRed/images/Share_CheckRed.png
        	$localimgUrl = 'http://'.$_SERVER['HTTP_HOST']."/Application/Sdk/View/Enterpriseactivity/img/children_icon.png";
    
        	$this->assign("localimgUrl",$localimgUrl);//字符串
        	//分享
        	
        	
		$this->getweixinqrcode($accesstoken);
    }

	public function getweixinqrcode($accesstoken)
	{	
			$submiturl = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$accesstoken;
			$pd = '{"action_name":"QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 123}}}';
      
			$rt = https_request($submiturl,$pd);
			$obj = json_decode($rt,true);
			$qrurl = $obj['url'];
			$src = 'http://pan.baidu.com/share/qrcode?w=150&h=150&url='.$qrurl;
			//公众号二维码
			//让其必须关注
		    $this->assign("qrurl",$src);
	}
	
	public function setqrcode()
	{
//		$user_type = "2";
//		$user_id = "112";
//		
//	    if($user_type==1){
//          $map['proxy_id']=$user_id;
//      }else{
//          $map['enterprise_id']=$user_id;
//      }
//      $result=M("user_set")->where($map)->find();
//	  
//      $APPID = $result['wx_appid'];
//      $APPSECRET =$result['wx_appsecret'];
        $APPID = "wxc455fa54abeb280d";
        $APPSECRET ="dce1684511a38bb87f78b344255fc419";
	  //获取ticketcode
		$submiturl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$APPID."&secret=".$APPSECRET;
		$rt = https_request($submiturl);
		$obj = json_decode($rt,true); 
		$accesstoken = $obj['access_token'];
		$this->getweixinqrcode($accesstoken);
		
		
	    $role="/Application/Sdk/View/Trailer/Flowunfollow/";
		$this->assign("role",$role);
	    $this->display("Trailer/Flowunfollow/index");
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
	  
	public function duanwuindex()
	{
			
        $APPID = "wxd9157d4e6d9f68e8";
        $APPSECRET ="335b1e06c499938d8ca0a45f00286634";
		$this->get_userinfo($APPID,$APPSECRET);
		
		  	
        	$FlowProductTitle = "610G流量大放送";
        	$this->assign("FlowProductTitle",$FlowProductTitle);//字符串
        	
        	$FlowProductdesc = "流量活动天天抢，610G流量每天送";
        	$this->assign("FlowProductdesc",$FlowProductdesc);//字符串
        	
        $Link=gethostwithhttp()."/index.php/Sdk/Enterpriseactivity/duanwuindex";
        
        	$this->assign("Link",$Link);//字符串
        	//http://sdk.liuliang.net.cn/Application/Sdk/View/FlowRed/images/Share_CheckRed.png
        	$localimgUrl = 'http://'.$_SERVER['HTTP_HOST']."/Application/Sdk/View/Enterpriseactivity/img/share_icon.jpg";
    
        	$this->assign("localimgUrl",$localimgUrl);//字符串
        	
        	
		$src = 'http://open.weixin.qq.com/qr/code/?username='."psmdtsh";
		
		$this -> assign("qrurl", $src);
		$role = "/Application/Sdk/View/Enterpriseactivity/";
		$this -> assign("role", $role);
		$this -> display("duanwuindex");
	}
	public function duanwuyouhui()
	{
			
        $APPID = "wxd9157d4e6d9f68e8";
        $APPSECRET ="335b1e06c499938d8ca0a45f00286634";
		$this->get_userinfo($APPID,$APPSECRET);
		$src = 'http://open.weixin.qq.com/qr/code/?username='."psmdtsh";
		
		$this -> assign("qrurl", $src);
		$role = "/Application/Sdk/View/Enterpriseactivity/";
		$this -> assign("role", $role);
		$this -> display("duanwuyouhui");
	}
}
?>