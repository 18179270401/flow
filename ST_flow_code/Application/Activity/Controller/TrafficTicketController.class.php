<?php
namespace Activity\Controller;
use Think\Controller;
class TrafficTicketController extends Controller {
        
    public function _initialize(){
        Vendor("WxPayR.Api");
        Vendor("WxPayR.JsApiPay");
        Vendor("WxPayR.WxPayConfig");
        Vendor('WxPayR.WxPayData');
        Vendor('WxPayR.Exception');
    }
   //TODO 流量券领取表 t_flow_ticket_receive  中查询号码是否领取  流量券兑换表 t_flow_ticket_exchange 下单改变状态兑换
//
//    public function Receive() {
//        $role = "/Application/Activity/View/TrafficTicket/Receive/";
//        $this->assign("role",$role);
//        $this->display("TrafficTicket/Receive/index");
//    }
//
//    public function Home() {
//        $role = "/Application/Activity/View/TrafficTicket/Home/";
////		$time = $data;
////		$this->assign("time",$time);
//        $this->assign("role",$role);
//        $this->display("TrafficTicket/Home/index");
//    }
//    public function Failure() {
//        $role = "/Application/Activity/View/TrafficTicket/Failure/";
////		$time = $data;
////		$this->assign("time",$time);
//        $this->assign("role",$role);
//        $this->display("TrafficTicket/Failure/index");
//    }
    //解密
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
		$user_activity_id = $InfoArray[2];
		$openid = $InfoArray[3];
		
        if($user_type==1){
            $proxy_id=$user_id;
            $map['proxy_id']=$proxy_id;
        }
        else if($user_type==2)
        {
            $enterprise_id=$user_id;
            $map['enterprise_id']=$enterprise_id;
        }
        $sinfo=M("SceneInfo")->where($map)->find();//读出活动的信息
        //获取微信个人信息
        $this->getlocaluserinfo($sinfo,$user_type,$user_id,$openid);

        $map['user_type'] = $user_type;
        $map['user_activity_id']=$user_activity_id;
        $sua=M("scene_user_activity")->where($map)->find(); //读出活动规则信息
        $this->assign("activity_id",$sua["activity_id"]);//TODO 活动ID
        $activity_rule = $sua['activity_rule'];
        $logo_img = $this->ActivityLogoImage($user_type,$user_id,$user_activity_id);
        $role = "/Application/Activity/View/TrafficTicket/Home/";
        $this->assign("activity_rule",$activity_rule);//活动规则相关
        $this->assign("logo_img",$logo_img);
        $this->assign("user_type",$user_type);//TODO 获取企业类型 1 代理商 2企业端
        $this->assign("user_id",$user_id);//TODO 用户id 代理商ID proxy_id   企业端IDenterprise_id
        $this->assign("user_activity_id",$user_activity_id);//TODO 活动ID
        $this->assign("role",$role);
        $this->display("TrafficTicket/Home/index");

    }
    //获取logoImage
    private function ActivityLogoImage ($user_type, $user_id,$user_activity_id)
    {
//TODO 获取企业类型 1 代理商 2企业端 用户id 代理商ID proxy_id   企业端IDenterprise_id  $user_activity_id 活动ID
        if($user_type==1){
            $proxy_id=$user_id;
            $map['proxy_id']=$proxy_id;
        }
        else if($user_type==2)
        {
            $enterprise_id=$user_id;
            $map['enterprise_id']=$enterprise_id;
        }
        $sinfo = M("SceneInfo")->where($map)->find();//TODO 读出活动的信息
        $sinfo_logo_img = $sinfo['logo_img'];
        $map['user_type'] =$user_type;
        $map['user_activity_id']=$user_activity_id;
        $sceneUser = M("scene_user_activity")->where($map)->find();//TODO scene_user_activity获取用户logo_img
        //每个活动对应的图片,如果找不到就去找原来的图片
        $logo_img = $sceneUser['logo_img'];
        if(empty($logo_img))//判断logo_img是否是空
        {
            $logo_img = $sinfo_logo_img;
        }
        return $logo_img;
    }




//TODO 领取流量券按钮点击链接
    public function indexClickButton (){

		//版本号更新css js更新
 		$this->assign('version_number', C('VERSION_NUMBER'));
		 

        $status = "error";
        $user_type=trim(I("user_type"));//TODO 获取企业类型 1 代理商 2企业端
        $user_id=trim(I("user_id"));//TODO 用户id 代理商ID proxy_id   企业端ID enterprise_id
        $user_activity_id=trim(I("user_activity_id"));//流量券ID 唯一
        $mobile = I("mobile");//手机号

        $rule = "/^0?(13[0-9]|15[012356789]|18[02356789]|14[57]|17[0-9])[0-9]{8}$/";
        $tag = preg_match($rule, $mobile);
        if ($tag == 0) {
            $this -> ReturnJson($status, "失败：手机号码错误！");
        }
        $logo_img = $this->ActivityLogoImage($user_type,$user_id,$user_activity_id);//获取到logo图片

        $map['mobile'] = $mobile;
        $map['user_activity_id'] = $user_activity_id;
        $ticketReceive=M("ticket_receive")->where($map)->find();//获取流量券领取表 t_flow_ticket_receive

        $receive_time = $ticketReceive['receive_time'];//领取时间
        $effective_duration =$ticketReceive['effective_duration']*3600;//流量券有效时长
        $flowticket_status = (int)$ticketReceive['flowticket_status'];
        $flowticket_receive_id =$ticketReceive['flowticket_receive_id'];//领取id 传到兑换表兑换id
        $redeem_code = $ticketReceive['redeem_code'];//兑换码
        $operator_id = $ticketReceive['operator_id'];//运营商
        $this->assign('aid',$user_activity_id);
        $this->assign("user_type",$user_type);
        $this->assign("user_id",$user_id);
        $time = time();//今天时间戳  
        // 1判断表中是否有 手机号  2 判断 flowticket_status值 流量券状态（默认状态 已兑换、已过期、已失效）0  1  2 3
        if($ticketReceive['mobile'])
        {
            switch($flowticket_status)
            {
                case 0:
                {
                        if($effective_duration>=($time - strtotime($receive_time)))
                        {

                            //活动时长大于或等于 领取时间和今天的差值 跳转到领取页面 查询表得到领取的流量值
                            //t_flow_channel_product   查询表得到领取的流量大小
                            $product_id = $ticketReceive['product_id'];//流量包ID
                            if(empty($product_id))
                            {
                                //没有领取到卷
                                $this->flowInterfaceJudge("HasTheFailure");//已失效
                                exit();
                            }
				            $size = D('ChannelProduct')->get_size_by_pid($product_id,$user_id);
                

                            $product_name = $size."M";//流量包名称
                            $product_type = 0;//默认为全国模式

                            $role = "/Application/Activity/View/TrafficTicket/Receive/";

                            $this->assign("size",$size);
                            $this->assign('product_name',$product_name);
                            $this->assign("flowUnit","M");
                            $this->assign("logo_img",$logo_img);//logo图片地址
                            $this->assign("role",$role);
                            $this->display("TrafficTicket/Receive/index");
                            //流量兑换需要的部分数据数组
//                            $flowTicketExchangeArr['redeem_id'] = $flowticket_receive_id;//兑换Id
                            $flowTicketExchangeArr['redeem_code'] = $redeem_code;//兑换码
                            $flowTicketExchangeArr['operator_id'] = $operator_id;//三大运营商
//                            $flowTicketExchangeArr['order_id'] = $orderID;//订单id
                            $flowTicketExchangeArr['product_id'] = $product_id;//流量包Id
                            $flowTicketExchangeArr['mobile'] = $mobile;//手机号
                            $flowTicketExchangeArr['exchange_time'] = date("Y-m-d H:i:s", time());//兑换时间
                            $flowTicketExchangeArr['order_date'] = date("Y-m-d H:i:s", time());//下单时间
                            $flowTicketExchangeArr['complete_time'] = date("Y-m-d H:i:s", time());//下单完成时间
//                            $flowTicketExchangeArr['order_status'] = "";//充值状态
                            $flowTicketExchangeArr['user_activity_id'] = $user_activity_id;//流量券活动Id

                            $respCode = $this->recharge($flowTicketExchangeArr,$user_type,$user_id,$size,$product_type);//TODO 下单
                            if($respCode == "0000")
                            {
                                $ticketinfo['flowticket_status'] = 1;
                                $ticketReceive=M("ticket_receive")->where($map)->save($ticketinfo);//获取流量券领取表 t_flow_ticket_receive
                            }
                            else
                            {
                                $ticketinfo['flowticket_status'] = 1;
                                $ticketReceive=M("ticket_receive")->where($map)->save($ticketinfo);//获取流量券领取表 t_flow_ticket_receive
                            }
                            exit();
                        }
                        else
                        {
                            $this->flowInterfaceJudge("expired");//已过期
                            exit();
                        }
                }
                    break;
                case 1:
                    $this->flowInterfaceJudge("HasChange");//已兑换
                    exit();
                    break;
                case 2:
                    $this->flowInterfaceJudge("expired");//已过期
                    exit();
                    break;
                case 3:
                    $this->flowInterfaceJudge("HasTheFailure");//已失效
                    exit();
                    break;
                default:
            }

        }
        else
        {//该手机号未领取到流量券 数据库没有该手机号
            $this->flowInterfaceJudge("HasNotReceived");//已过期
            exit();
        }
    }
    //判断界面
    private function flowInterfaceJudge ($errortype)
    {
        if($errortype == "HasChange")
        {
            $bgimage = "Failure.jpg";
            //已兑换
            $role = "/Application/Activity/View/TrafficTicket/Failure/";
            $this->assign("role",$role);
            $this->assign("bgimage",$bgimage);
            $this->display("TrafficTicket/Failure/index");
        }
        else if($errortype == "expired")
        {
            $bgimage = "expired.jpg";
            //已过期
            $role = "/Application/Activity/View/TrafficTicket/Failure/";
            $this->assign("role",$role);
            $this->assign("bgimage",$bgimage);
            $this->display("TrafficTicket/Failure/index");
        }
        else if($errortype == "HasTheFailure")
        {
            $bgimage = "HasTheFailure.jpg";
            //已失效
            $role = "/Application/Activity/View/TrafficTicket/Failure/";
            $this->assign("bgimage",$bgimage);
            $this->assign("role",$role);
            $this->display("TrafficTicket/Failure/index");
        }
        else if($errortype == "HasNotReceived")
        {
            $bgimage = "HasNotReceived.jpg";
            //未领取到流量券
            $role = "/Application/Activity/View/TrafficTicket/Failure/";
            $this->assign("role",$role);
            $this->assign("bgimage",$bgimage);
            $this->display("TrafficTicket/Failure/index");
        }
        else if($errortype == "unfollow")
        {
            $role="/Application/Activity/View/Trailer/Flowunfollow/";
			$this->assign("role",$role);
		    $this->display("Trailer/Flowunfollow/index");
        }
    }


    //返回值
    protected function ReturnJson($status, $msg = '', $data = '') {
        $status = $status == "success" ? 1 : 0;
        $array = array('status' => $status, 'msg' => $msg, 'data' => $data, );

        $this -> ajaxReturn($array);
    }
    //TODO 向流量服务器下单
    private function recharge($flowTicketExchangeArr,$user_type,$user_id,$size,$product_type) {
        $phone = $flowTicketExchangeArr['mobile'];

        $user_activity_id = $flowTicketExchangeArr['user_activity_id'];
        $product_id = $flowTicketExchangeArr['product_id'];
        $status = "error";
        if (empty($user_type)) {
            $this -> ReturnJson($status, "参数错误", "");
        }
        if (!$phone) {
            $msg = '请输入电话号码';
            $this -> ReturnJson($status, $msg);
        }
        $rule = "/^0?(13[0-9]|15[012356789]|18[02356789]|14[57]|17[0-9])[0-9]{8}$/";
        $tag = preg_match($rule, $phone);
        if ($tag == 0) {
            $this -> ReturnJson($status, "失败：手机号码错误！");
        }

        //TODO 通过企业信息查询下单秘钥
        $apimap['user_type'] = $user_type;
        if ($user_type == 1) {
            $proxy_id = $user_id;
            $apimap['proxy_id'] = $proxy_id;
        } else {
            $enterprise_id = $user_id;
            $apimap['enterprise_id'] = $enterprise_id;
        }
        $sys_api = M("sys_api") -> where($apimap) -> find();


        $submiturl = C("API_SUBMIT");//获取URL
        $range = $product_type;
//单位 M
//$account    = 'LKKKUZMO';
//$api_key    = 'KN0lqsyYbWsB65LCZSo13f2zfZhCPdyW';
        $account = $sys_api['api_account'];
        $api_key = $sys_api['api_key'];
        $timeStamp = time();
        $pd = array('account' => $account, 'action' => 'Charge', 'phone' => $phone, 'range' => $range, 'size' => $size, 'timeStamp' => $timeStamp, );
        $pre_str = "{$api_key}account={$account}&action=Charge&phone={$phone}&range={$range}&size={$size}&timeStamp={$timeStamp}{$api_key}";
        $pd['sign'] = md5($pre_str);
        // TODO 下单
        $rt = https_request($submiturl, $pd);
        $ret = json_decode($rt, true);

        $nickname = trim(I("nickname"));
        $headimgurl = trim(I("headimgurl"));

        //得到订单号 失败为空  记录到领取表中
        $orderID = $ret['orderID'];
        $flowTicketExchange = $flowTicketExchangeArr;
        //将企业信息存入
        $flowTicketExchange['user_type'] = $user_type;
        if ($user_type == 1) {
            $flowTicketExchange['proxy_id'] = $user_id;
        } else {
            $flowTicketExchange['enterprise_id'] = $user_id;
        }
        $flowTicketExchange['order_id'] = $orderID;//订单id
        $flowTicketExchange['wx_name'] = $nickname;//名称
        $flowTicketExchange['wx_photo'] = $headimgurl;//头像
        if(!$orderID)
        {
            //下单失败
            $flowTicketExchange['order_status']= 6;
            M('ticket_exchange') -> add($flowTicketExchange);
        }
        else
        {
            //下单失败
            $flowTicketExchange['order_status']= 2;
            M('ticket_exchange') -> add($flowTicketExchange);
        }
        return $rt["respCode"];
    }
    
 	public function getlocaluserinfo($result,$user_type,$user_id,$openid){
		$pd = "";
		$APPID = $result['active_appid'];
		$APPSECRET = $result['active_appsecret'];
		
		$openidkey = "samton".$user_type."_".$user_id."openid".$APPID;
		
        $openid 		= cookie($openidkey);
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
	
		$retrnrt = https_request($submiturl);
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
       	 	$this->flowInterfaceJudge("unfollow");
            exit();
			
			//$this->get_userinfo($result);
		}
    }

}
