<?php
namespace PointValueManage\Controller;
use Think\Controller;
require "./Public/utils/php/wx/wx_verification.php";
class SignController extends Controller {
	private function createNonceStr($length = 16) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
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
	//调取分享权限
	private function get_shareuser($result){
		$user_type = $result['user_type'];
		if($user_type==1)
	    {
	         $user_id = $result['proxy_id'];
	    }else{
	         $user_id = $result['enterprise_id'];
	    }
			
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
    		$retrnrt = https_request($submiturl);
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
	 	//分享内容
       	//$sharlink='http://'.$_SERVER['HTTP_HOST']."/index.php/PointValueManage/Api/index?user_type=".$user_type."&user_id=".$user_id;
			//字符串
		//分享内容
		$data = $this->localencode($user_type.",".$user_id);
        $sharlink = gethostwithhttp()."/index.php/PointValueManage/Api/index?".$data;


		//echo "<script>alert('$active');</script>"; 
        	$this->assign("Link",$sharlink);//字符串
	 	$localimgUrl = 'http://'.$_SERVER['HTTP_HOST']."/Application/Activity/View/Home/HomePage/images/Share_TigerRound.png";
        	$FlowProductTitle = "流量积分";
        	$FlowProductdesc = "每天签到就可以拿流量！，还有更多的游戏大家参与";
		//获取分享权限
        	$this->assign("FlowProductTitle",$FlowProductTitle);//标题
        	$this->assign("FlowProductdesc",$FlowProductdesc);//字符串
        	$this->assign("localimgUrl",$localimgUrl);//字符串
    }

	public function verificationId($openid, $user_id, $user_type) {
		if (verificationId($openid, $user_id, $user_type)) {

		} else {
			$this -> ReturnJson("error", "验证id失败");
		}
	}

	public function index() {
		$user_type = trim(I("usertype"));
		$openid = trim(I("openid"));
		$userid = trim(I("userid"));

		//设定分享内容
		if ($user_type == 1) {
			$map['proxy_id'] = $userid;
		} else {
			$map['enterprise_id'] = $userid;
		}
		$sinfo = M("SceneInfo") -> where($map) -> find();
		//读出活动的信息s

		$this -> get_shareuser($sinfo);
		//设定分享内容
		$this -> verificationId($openid, $userid, $user_type);
		$wx_user_id = $this -> getUserId($openid, $userid, $user_type);

		$randomNum = I("random");

		$map['user_type'] = $user_type;
		if ($user_type == 2) {
			$enterprise_id = $userid;
			$map['enterprise_id'] = $enterprise_id;
		} else {
			$proxy_id = $userid;
			$map['proxy_id'] = $proxy_id;
		}
		$sinfo = M("wx_enterprise") -> where($map) -> find();
		
			//判断是否跳转活动预热页面
		$start_time = date("H:i:s",strtotime($sinfo['start_time']));//开始时间
		$end_time = date("H:i:s",strtotime($sinfo['end_time']));//结束时间

		$time = date("H:i:s",time());//当前时间  开始时间小于当前时间 结束时间大于当前时间

		//start_date 活动开始时间  end_date  活动结束时间
		$start_date = date("y-m-d H:i:s",strtotime($sinfo['start_date']));//开始日期
		$end_date = date("y-m-d H:i:s",strtotime($sinfo['end_date'])+86399);//结束日期
		$data = date("y-m-d H:i:s",time());//当前时间  开始日期小于当前时间 结束日期大于当前时间

		if((empty($sinfo['start_date'])!=FALSE||empty($sinfo['end_date'])!=FALSE)||(strtotime($start_date)<=strtotime($data)&&strtotime($end_date)>=strtotime($data)))
		{
			if((empty($sinfo['start_time'])!=FALSE||empty($sinfo['end_time'])!=FALSE)||(strtotime($start_time)<=strtotime($time)&&strtotime($end_time)>=strtotime($time)))
			{
				$this -> assign("openid", $openid);
				$this -> assign("userid", $userid);
				$this -> assign("user_type", $user_type);
				$this -> assign("role", "/Application/PointValueManage/View/Sign/");
				$this -> assign("daily_score", $sinfo['daily_score']);
				$this -> assign("flowscore_basic_logo", $sinfo['flowscore_basic_logo']);
				$this -> assign("randomNum", $randomNum);
				$this->assign('version_number', C('VERSION_NUMBER')); 
				$this -> display("Sign/index");
			}
			else
			{
				$time = date("H:i",strtotime($start_time))." — ".date("H:i",strtotime($end_time));//时间
				$data = date("Y.m.d",strtotime($start_date))."-".date("Y.m.d",strtotime($end_date));//日期
				$this->preheat($time,$data);
			}
		}
		else
		{
			$time = date("H:i",strtotime($start_time))." — ".date("H:i",strtotime($end_time));//时间
			$data = date("Y.m.d",strtotime($start_date))."-".date("Y.m.d",strtotime($end_date));//日期
			$this->preheat($time,$data);
		}

	}
	public function preheat($time,$data) {
		// 预热界面
		$role = "/Application/PointValueManage/View/Preheat/";
		$this->assign("role",$role);
		$this->assign("time",$time);
		$this->assign("data",$data);
		$this->assign('version_number', C('VERSION_NUMBER')); 
		$this->display("Preheat/index");
	}

	/**
	 * 获取微信用户id
	 */
	private function getUserId($openid, $user_id, $user_type) {
		if ($user_type == 2) {
			$map['enterprise_id'] = $user_id;
		} else {
			$map['proxy_id'] = $user_id;
		}
		$map['user_type'] = $user_type;
		$map['wx_openid'] = $openid;

		$sinfo = M("wx_user") -> where($map) -> find();
		return $sinfo['wx_user_id'];
	}

	/**
	 * 获取今日签到积分
	 */
	private function getDailyScore($user_type, $enterprise_id, $proxy_id) {
		$map['user_type'] = $user_type;
		if ($user_type == 2) {
			$map['enterprise_id'] = $enterprise_id;
		} else {
			$map['proxy_id'] = $proxy_id;
		}

		$sinfo = M("wx_enterprise") -> where($map) -> find();
		return $sinfo;
	}

	/**
	 * 签到
	 */
	public function dailyOfSign() {

		$openid = I("openid");
		$userid = I("userid");
		$user_type = I("user_type");
		$score_remark = I("score_remark");
		$this -> verificationId($openid, $userid, $user_type);
		$wx_user_id = $this -> getUserId($openid, $userid, $user_type);
		$enterprise_id = 0;
		$proxy_id = 0;
		if ($user_type == 2) {
			$enterprise_id = $userid;
		} else {
			$proxy_id = $userid;
		}

		$dailyInfo = $this -> getDailyScore($user_type, $enterprise_id, $proxy_id);

		$daily_score = $dailyInfo['daily_score'];
		if (!$daily_score) {
			$this -> ReturnJson('fail', '找不到签到积分值');
		}
		$dataRes = array();
		if ($this -> queryDailySign($wx_user_id, $user_type, $enterprise_id, $proxy_id)) {
			//今日已签到
			$status = "fail";
			$msg = "今日已签到";
		} else {
			//今日未签到
			$score = $this -> insertTheSign($wx_user_id, $enterprise_id, $proxy_id, $user_type, $daily_score, $score_remark);
			if ($score != -1) {
				//签到成功
				$status = "success";
				$msg = "签到成功";
				$dataRes['score'] = $score;
			} else {
				//签到失败
				$status = "fail";
				//				$msg = "$wx_user_id-->".$wx_user_id;
				$msg = "签到失败，请重试";
			}
		}

		$this -> ReturnJson($status, $msg, $dataRes);
	}

	/**
	 * 插入签到数据
	 */
	private function insertTheSign($wx_user_id, $enterprise_id, $proxy_id, $user_type, $score_change, $score_remark) {

		$score_record = M("score_record");
		//启动事务
		$score_record -> startTrans();
		// 实例化User对象
		$data['wx_user_id'] = $wx_user_id;
		$data['enterprise_id'] = $enterprise_id;
		$data['proxy_id'] = $proxy_id;
		$data['user_type'] = $user_type;
		$data['score_modify_time'] = date('Y-m-d H:i:s');
		$data['score_change'] = $score_change;
		$data['score_remark'] = $score_remark;
		if ($score_record -> add($data)) {
			//签到成功
			//在wx_user表中加入积分
			$infoData = $this -> upDataScore($wx_user_id, $user_type, $enterprise_id, $proxy_id, $score_change);
			if ($infoData['status'] == 1) {
				$score_record -> commit();
				return $infoData['user_flow_score'];
			} else {
				$score_record -> rollback();
				return -1;
			}
		} else {
			//事件回滚
			$score_record -> rollback();
			return -1;
		}
	}

	/**
	 * 查询今日是否已签到
	 * 返回TRUE表示已签到
	 */
	private function queryDailySign($wx_user_id, $user_type, $enterprise_id, $proxy_id) {
//				return FALSE;
		$score_record = M("score_record");
		// 实例化User对象

		if ($user_type == 2) {
			$map['enterprise_id'] = $enterprise_id;
		} else {
			$map['proxy_id'] = $proxy_id;
		}

		$map['wx_user_id'] = $wx_user_id;
		$map['user_type'] = $user_type;

		// 查找status值为1name值为think的用户数据
		$map['_string'] = 'score_modify_time >=\'' . date('Y-m-d' . ' 00:00:00', time()) . '\' AND score_modify_time<=\'' . date('Y-m-d' . ' 23:59:59', time()) . '\'';
		$data = $score_record -> where($map) -> find();

		if ($data == null) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	/**
	 * 更新个人总积分
	 */
	private function upDataScore($wx_user_id, $user_type, $enterprise_id, $proxy_id, $score_change) {
		$User = M("wx_user");
		// 实例化User对象

		if ($user_type == 2) {
			$map['enterprise_id'] = $enterprise_id;
		} else {
			$map['proxy_id'] = $proxy_id;
		}

		$map['wx_user_id'] = $wx_user_id;
		$map['user_type'] = $user_type;

		$info = $User -> where($map) -> find();
		// 要修改的数据对象属性赋值
		$data['user_flow_score'] = $info['user_flow_score'] + $score_change;

		$isUpData = $User -> where($map) -> save($data);
		// 根据条件更新记录
		if ($isUpData > 0) {
			$status = "1";
		} else {
			$status = "0";
		}
		$data['status'] = $status;

		return $data;
	}

	/**
	 * 返回值
	 */
	private function ReturnJson($status, $msg = '', $data = '') {
		$status = $status == "success" ? 1 : 0;
		$array = array('status' => $status, 'msg' => $msg, 'data' => $data, );

		$this -> ajaxReturn($array);
	}

}
