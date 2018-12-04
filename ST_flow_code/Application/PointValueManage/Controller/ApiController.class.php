<?php
namespace PointValueManage\Controller;
use Think\Controller;
require "./Public/utils/php/wx/wx_verification.php";

class ApiController extends Controller {
	public function _initialize() {
		Vendor("WxPayR.Api");
		Vendor("WxPayR.JsApiPay");
		Vendor("WxPayR.WxPayConfig");
		Vendor('WxPayR.WxPayData');
		Vendor('WxPayR.Exception');
	}
	
	// 加载解码
	public function localdecode($data) {
		$data = base64_decode($data);
		for ($i = 0; $i < strlen($data); $i++) {
			$ord = ord($data[$i]);
			$ord -= 20;
			$string = $string . chr($ord);
		}
		return $string;
	}

	//本地加码
	public function localencode($data) {
		for ($i = 0; $i < strlen($data); $i++) {
			$ord = ord($data[$i]);
			$ord += 20;
			$string = $string . chr($ord);
		}
		$string = base64_encode($string);
		return $string;
	}

	// 首頁
	public function index() {
		$rsaKey = $_SERVER["QUERY_STRING"];
		$tmp = stripos($rsaKey, "&");

		if ($tmp != false) {
			$rsaKey = substr($rsaKey, 0, $tmp);
		}
		// var_dump($rsaKey);
		// exit();

		$strArray = $this -> localdecode($rsaKey);
		$InfoArray = explode(",", $strArray);

		$user_type = $InfoArray[0];
		$user_id = $InfoArray[1];

		// $user_id = I("user_id");
		// $user_Type = I("usert_ype");
		$map["usert_ype"] = $user_Type;
		if ($user_type == 1) {
			$map['proxy_id'] = $user_id;
		} else {
			$map['enterprise_id'] = $user_id;
		}
		$sinfo = M("SceneInfo") -> where($map) -> find();
		//读出活动的信息s

		$APPSECRET = $sinfo['active_appsecret'];
		$APPID = $sinfo['active_appid'];
		$WXName = $sinfo["active_wx_name"];

		$activity_type = $sinfo["active_wx_type"];
		if ($activity_type == 2) {
			//该公众号为订阅号类型必须回复才可以得流量
			$wx_name = $sinfo["active_wx_name"];
			$src = 'http://open.weixin.qq.com/qr/code/?username=' . $wx_name;

			// 公众号二维码
			$this -> assign("qrurl", $src);
			$this -> flowerror("unfollow");
			exit();
		}
		$openidkey = "samtonpoint" . $user_type . "_" . $user_id . "openid";
		$openid = cookie($openidkey);
		if (empty($openid)) {
			$config = array();
			$config['APPID'] = $APPID;
			$config['APPSECRET'] = $APPSECRET;
			$tools = new \JsApiPay($config);
			$openid = $tools -> GetOpenid();
			cookie($openidkey, $openid);
		}

		$submiturl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $APPID . "&secret=" . $APPSECRET;
		$rt = https_request($submiturl);
		$obj = json_decode($rt, true);
		$accesstoken = $obj['access_token'];

		$submiturl = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $accesstoken . "&openid=" . $openid . "&lang=zh_CN";

		$retrnrt = https_request($submiturl);
		$retrnrtobj = json_decode($retrnrt, true);
		$subscribe = $retrnrtobj['subscribe'];
		if ($subscribe == 1) {
			$newstr = substr($retrnrtobj['headimgurl'], 0, strlen($retrnrtobj['headimgurl']) - 1);

			$headimgurl = $newstr . "64";
			$nickname = $retrnrtobj['nickname'];

			$openid = $openid . "";

			$data['wx_openid'] = $openid;
			$user_type = $sinfo['user_type'];
			$data['user_type'] = $user_type;
			if ($user_type == 1) {
				$user_id = $sinfo['proxy_id'];
				$data['proxy_id'] = $user_id;
			} else {
				$user_id = $sinfo['enterprise_id'];
				$data['enterprise_id'] = $user_id;
			}

			$info = M("wx_user") -> where($data) -> find();
			if (!$info) {
				$data['mobile'] = -1;
				$data['user_flow_score'] = 0;
				$data['wx_photo_url'] = $headimgurl;
				$data['wx_name'] = $nickname;
				$data['last_flow_date'] = date("Y-m-d H:i:s", time());
				if (! M("wx_user") -> add($data)) {
					die();
				}
			}
			$link = 'http://' . $_SERVER['HTTP_HOST'] . "/index.php/PointValueManage/Api/pointValue?openid=" . $openid . "&usertype=" . $user_type . "&userid=" . $user_id;
			echo "<script language='javascript' type='text/javascript'> window.location='{$link}';</script>";
		} else {
			$wx_name = $sinfo["active_wx_name"];
			$src = 'http://open.weixin.qq.com/qr/code/?username=' . $wx_name;

			//公众号二维码
			$this -> assign("qrurl", $src);
			//让其必须关注
			$this -> flowerror("unfollow");
			exit();
		}
	}

	//调取分享权限
	private function get_shareuser($result) {
		$user_type = $result['user_type'];
		if ($user_type == 1) {
			$user_id = $result['proxy_id'];
		} else {
			$user_id = $result['enterprise_id'];
		}

		$APPSECRET = $result['active_appsecret'];
		$APPID = $result['active_appid'];
		//获取ticketcode
		$submiturl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $APPID . "&secret=" . $APPSECRET;
		$rt = https_request($submiturl);
		$obj = json_decode($rt, true);
		$accesstoken = $obj['access_token'];

		$submiturl = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=" . $accesstoken;

		//$submiturl = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$accesstoken."&openid=" .$Openid. "&lang=zh_CN";
		//$retrnrt = https_request($submiturl);
		$retrnrt = https_request($submiturl);
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
		//分享内容
		$data = $this -> localencode($user_type . "," . $user_id);
		$sharlink = gethostwithhttp() . "/index.php/PointValueManage/Api/index?" . $data;
		// var_dump($sharlink);
		// exit();

		//$sharlink = 'http://' . $_SERVER['HTTP_HOST'] . "/index.php/PointValueManage/Api/index?user_type=" . $user_type . "&user_id=" . $user_id;

		//echo "<script>alert('$active');</script>";
		$this -> assign("Link", $sharlink);
		//字符串
		$localimgUrl = 'http://' . $_SERVER['HTTP_HOST'] . "/Application/PointValueManage/View/PointValue/images/share_pointvalue.jpg";
		$FlowProductTitle = "流量积分";
		$FlowProductdesc = "每天签到就可以拿流量！，还有更多的游戏大家参与";
		//获取分享权限
		$this -> assign("FlowProductTitle", $FlowProductTitle);
		//标题
		$this -> assign("FlowProductdesc", $FlowProductdesc);
		//字符串
		$this -> assign("localimgUrl", $localimgUrl);
		//字符串
	}

	private function createNonceStr($length = 16) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}

	private function flowerror($error) {
		$role = "/Application/PointValueManage/View/Pointunfollow/";
		$this -> assign('version_number', C('VERSION_NUMBER')); 
		$this -> assign("role", $role);
		$this -> display("Pointunfollow/index");
	}

	public function verificationId($openid, $user_id, $user_type) {
		$data = verificationId($openid, $user_id, $user_type);
		if ($data) {
			return $data;
		} else {
			$this -> ReturnJson("error", "验证id失败");
		}
	}

	public function record() {
		$user_id = I('userid');
		$user_type = I('usertype');
		$openid = I('openid');

		//设定分享内容
		if ($user_type == 1) {
			$map['proxy_id'] = $user_id;
		} else {
			$map['enterprise_id'] = $user_id;
		}
		$sinfo = M("SceneInfo") -> where($map) -> find();
		//读出活动的信息s

		$this -> get_shareuser($sinfo);
		//设定分享内容

		$this -> verificationId($openid, $user_id, $user_type);
		//		$page = (int)trim(I("page"));
		$where['er.user_type'] = $user_type;
		if ((int)$user_type == 1) {
			$where['er.proxy_id'] = $user_id;

		} else {
			$where['er.enterprise_id'] = $user_id;
		}

		$whereWxUser['user_type'] = $user_type;
		if ((int)$user_type == 1) {
			$whereWxUser['proxy_id'] = $user_id;

		} else {
			$whereWxUser['enterprise_id'] = $user_id;
		}
		$whereWxUser['wx_openid'] = $openid;

		$userRes = M('wx_user') -> where($whereWxUser) -> find();
		if ($userRes) {
			$where['er.wx_user_id'] = $userRes['wx_user_id'];
			$where['er.exchange_status'] = 2;
			// 根据页码和条件查询历史记录
			$list = D('SceneInfo') -> get_point_value_pay_order_list($where, "1");
			$role = "/Application/PointValueManage/View/Record/";
			$this -> assign("role", $role);
			$this -> assign("userid", $user_id);
			$this -> assign("usertype", $user_type);
			$this -> assign("openid", $openid);
			$this -> assign("list", json_encode($list));
			$this -> assign('version_number', C('VERSION_NUMBER')); 
			$this -> display("Record/index");
		} else {
			$this -> ReturnJson("error", "找不到用户");
		}

	}

	public function recordData() {
		$user_id = I('userid');
		$user_type = I('usertype');
		$page = (int)trim(I("page"));
		$openid = I('openid');
		$this -> verificationId($openid, $user_id, $user_type);

		$where['er.user_type'] = $user_type;
		if ((int)$user_type == 1) {
			$where['er.proxy_id'] = $user_id;

		} else {
			$where['er.enterprise_id'] = $user_id;
		}
		$whereWxUser['user_type'] = $user_type;
		if ((int)$user_type == 1) {
			$whereWxUser['proxy_id'] = $user_id;

		} else {
			$whereWxUser['enterprise_id'] = $user_id;
		}
		$whereWxUser['wx_openid'] = $openid;

		$userRes = M('wx_user') -> where($whereWxUser) -> find();
		if ($userRes) {
			$where['er.wx_user_id'] = $userRes['wx_user_id'];
			$where['er.exchange_status'] = 2;
			$list = D('SceneInfo') -> get_point_value_pay_order_list($where, $page);
			$this -> ReturnJson("success", "翻页成功", $list);
		}
	}

	public function getPointValue() {
		$userid = I("userid");
		$userType = I("usertype");
		$openid = I("openid");
		if ((int)$userType == 1) {
			$data['user_type'] = 1;
			$data['proxy_id'] = $userid;
		} else {
			$data['user_type'] = 2;
			$data['enterprise_id'] = $userid;
		}
		$data['wx_openid'] = $openid;

		$userData = M("wx_user") -> where($data) -> find();
		if (!$userData) {
			$this -> ReturnJson("error", "未找到积分");
		}
		return $userData;
	}

	public function exchange() {

		$userData = $this -> getPointValue();
		$userid = I("userid");
		$userType = I("usertype");
		$openid = I("openid");

		//设定分享内容
		if ($userType == 1) {
			$map['proxy_id'] = $userid;
		}
		else {
			$map['enterprise_id'] = $userid;
		}
		$sinfo = M("SceneInfo") -> where($map) -> find();
		//读出活动的信息s

		$this -> get_shareuser($sinfo);
		//设定分享内容

		$userInfoData = $this -> verificationId($openid, $userid, $userType);
		$random = I("random");
		$role = "/Application/PointValueManage/View/Exchange/";
		$this -> assign("role", $role);
		$this -> assign("userid", $userid);
		$this -> assign("usertype", $userType);
		$this -> assign("openid", $openid);
		$this -> assign("score", $userData['user_flow_score']);
		$this -> assign("randomNum", $random);
		$this -> assign("mobile",$userInfoData['mobile']);
		$this -> assign('version_number', '1'.C('VERSION_NUMBER')); 
		$this -> display("Exchange/index");
	}

	// 签到绑定一个用户
	public function exchangeEvent() {
		$user_type = (int)trim(I("usertype"));
		$user_id = (int)trim(I("userid"));
		$openid = I("openid");
		$userInfoData = $this -> verificationId($openid, $user_id, $user_type);
//					$this -> ReturnJson("error",$openid);

		// 流量包产品id
		$packet_id = I("packet_id");

		// 手机号
		$input_text = I("input_text");

		// 获取企业
		if ($user_type == 1) {
			$data['proxy_id'] = $user_id;
		} else {
			$data['enterprise_id'] = $user_id;
		}
		$mobile = $userInfoData['mobile'];
//		$this -> ReturnJson("error", $userInfoData);

		if ($mobile == '-1') {
			$findPhoneMap['user_type'] = $user_type;
			
			
			$map['user_type'] = $user_type;
			if ((int)$user_type == 2) {
				$map['enterprise_id'] = $user_id;
				$findPhoneMap['enterprise_id'] = $user_id;
			} else {
				$map['proxy_id'] = $user_id;
				$findPhoneMap['proxy_id'] = $user_id;
			}
			$map['wx_openid'] = $openid;
			$findPhoneMap['mobile'] = $input_text;
			$wx_userDatas = M('wx_user') -> where($findPhoneMap) -> select();
			
			if(count($wx_userDatas) == 0)
			{
					$wx_userData = M('wx_user') -> where($map) -> setField('mobile', $input_text);
//					$this -> ReturnJson("error",json_encode( M('wx_user') -> where($map) -> find()));
					
//					$this -> ReturnJson("error",json_encode($map));
			}
			else if(count($wx_userDatas) == 1)
			{
				$aWx_user = $wx_userDatas[0];
				if($aWx_user['wx_openid'] == $openid)
				{
				}
				else{
					$this -> ReturnJson("error", "当前手机号码已被注册");
				}
			}
			else
			{
					$this -> ReturnJson("error", "警告：当前手机号码已被多个用户注册");
			}
		} else {
			if($input_text!=$mobile)
			{
				$this -> ReturnJson("error", "当前用户绑定的不是这个手机号码");
			}
			
			$input_text = $mobile;
		}
		// 企业配置表获得积分换算比
		$enterprise_set = M("wx_enterprise") -> where($data) -> find();
		if ($enterprise_set) {
			$rate = $enterprise_set['flowscore_exchange_rate'];
			// 到产品表找对应的包
			$map = array('status' => 1, 'province_id' => 1, 'product_id' => (int)$packet_id);
			$msg = D('ChannelProduct') -> channelproductinfoFromMap($map);
			if ($msg) {
				//
				$data['wx_openid'] = $openid;
				$userData = $this -> getPointValue($openid);
				$usedScore = (int)($msg['size'] * $rate);
				$score = (int)$userData['user_flow_score'] - $usedScore;
				if ($score < 0) {
					$this -> ReturnJson("error", "积分不够");
				}
				$userData['user_flow_score'] = $score;
				$id = $userData['wx_user_id'];

				$packet_msg = $msg;
				$packet_msg['mobile'] = $input_text;
				if (1 == $user_type) {
					$map['proxy_id'] = $user_id;
				} else {
					$map['enterprise_id'] = $user_id;
				}
				$api_info = M('sys_api') -> where($map) -> find();
				if (!$api_info) {
					$this -> ReturnJson("error", "找不到企业信息");
				}
				// 下单
				//				var_dump($packet_msg);
				$order = $this -> _createInFrom($packet_msg, $api_info);
				//				$order['respCode'] = '0000';
				//				$order['orderID'] = '180791049481466492432027034568';

				//				var_dump($list);
				if ($order['respCode'] == '0000') {

					$response = M('wx_user') -> where(array('wx_user_id' => $id)) -> save($userData);
					if ($response) {

						$addData['product_id'] = $packet_id;
						$addData['operator_id'] = $msg['operator_id'];
						$addData['mobile'] = $input_text;

						$addData['exchage_time'] = date('Y-m-d H:i:s', time());
						$addData['order_date'] = date('Y-m-d H:i:s', time());
						$addData['exchange_status'] = 2;
						$addData['refund_status'] = 0;
						$addData['exchange_score'] = $usedScore;
						$addData['wx_user_id'] = $id;
						$addData['order_code'] = $order['orderID'];
						if (1 == $user_type) {
							$addData['enterprise_id'] = 0;
							$addData['proxy_id'] = $user_id;
						} else {
							$addData['proxy_id'] = 0;
							$addData['enterprise_id'] = $user_id;
						}
						$addData['user_type'] = $user_type;
						$addScuess = M('exchange_record') -> add($addData);

						if ($addScuess) {
		                                                                                                                                                                     					$this -> ReturnJson("success", "成功", array('score' => $score));
						}
	  				}                        
				} else {
					if ($order['respCode'] == '0001') {
						$this -> ReturnJson("error", "充值中", $order);
					} else if ($order['respCode'] == '0002') {
						$this -> ReturnJson("error", "充值成功", $order);

					} else if ($order['respCode'] == '0003') {
						$this -> ReturnJson("error", "充值失败", $order);

					} else if ($order['respCode'] == '1000') {
						$this -> ReturnJson("error", "用户不存在", $order);

					} else if ($order['respCode'] == '1001') {
						$this -> ReturnJson("error", "IP鉴权失败", $order);

					} else if ($order['respCode'] == '1002') {
						$this -> ReturnJson("error", "签名校验失败", $order);

					} else if ($order['respCode'] == '1003') {
						$this -> ReturnJson("error", "该订单不存在", $order);

					} else if ($order['respCode'] == '1004') {
						$this -> ReturnJson("error", "您无权查看别人的订单状态", $order);

					} else if ($order['respCode'] == '1005') {
						$this -> ReturnJson("error", "参数提交不完全", $order);

					} else if ($order['respCode'] == '3002') {
						$this -> ReturnJson("error", "无效手机号", $order);

					} else if ($order['respCode'] == '3003') {
						$this -> ReturnJson("error", "无效区域代号", $order);

					} else if ($order['respCode'] == '3004') {
						$this -> ReturnJson("error", "无效流量包大小", $order);

					} else if ($order['respCode'] == '3005') {
						$this -> ReturnJson("error", "无法找到相应的产品", $order);

					} else if ($order['respCode'] == '3006') {
						$this -> ReturnJson("error", "无法找到相应的库存", $order);

					} else if ($order['respCode'] == '3007') {
						$this -> ReturnJson("error", "用户无权限给此运营商的手机号冲流量", $order);

					} else if ($order['respCode'] == '4000') {
						$this -> ReturnJson("error", "企业余额不足", $order);
					} else if ($order['respCode'] == '4001') {
						$this -> ReturnJson("error", "企业余额不足（上级）", $order);
					} else if ($order['respCode'] == '8000') {
						$this -> ReturnJson("error", "其他错误。请练习工程师跟进。", $order);
					}
				}
			} else {
				$this -> ReturnJson("error", "未找到流量产品");
			}
		} else {
			$this -> ReturnJson("error", "未找到指定兑换率");
		}
	}

	//我的积分界面
	public function pointValue() {
		$userid = I("userid");
		$userType = I("usertype");
		$openid = I("openid");

		//设定分享内容
		if ($userType == 1) {
			$map['proxy_id'] = $userid;
		} else {
			$map['enterprise_id'] = $userid;
		}
		$sinfo = M("scene_info") -> where($map) -> find();
		//		//读出活动的信息s
		//
		$this -> get_shareuser($sinfo);
		//		//设定分享内容

		$userData = $this -> getPointValue($openid);
		$this -> verificationId($openid, $userid, $userType);
		//判断时间跳转

		$role = "/Application/PointValueManage/View/PointValue/";
		$this -> assign("role", $role);
		$this -> assign("userid", $userid);
		$this -> assign("usertype", $userType);
		$this -> assign("openid", $openid);
		$this -> assign("name", $userData['wx_name']);
		$this -> assign("score", $userData['user_flow_score']);
		$this -> assign("randomNum", mt_rand());
		$this -> assign('version_number', C('VERSION_NUMBER'));
		$this -> display("PointValue/index");
	}

	protected function ReturnJson($status, $msg = '', $data = '') {
		$status = $status == "success" ? 1 : 0;
		$array = array('status' => $status, 'msg' => $msg, 'data' => $data, );
		$this -> ajaxReturn($array);
		exit();
	}

	public function signPage() {
		$role = "/Application/Sdk/View/Active/Sign/";
		$this -> assign("role", $role);
		$this -> assign('version_number', C('VERSION_NUMBER')); 
		$this -> display("Active/Sign/index");
	}

	public function pointValue_packets() {
		$user_type = trim(I("user_type"));
		$user_id = trim(I("user_id"));
		$mobile = trim(I('phone'));


		if ($user_type == 1) {
			$data['proxy_id'] = $user_id;
		} else {
			$data['enterprise_id'] = $user_id;
		}
		$enterprise_set = M("wx_enterprise") -> where($data) -> find();
		$rate = $enterprise_set['flowscore_exchange_rate'];
		
		//手机结构数组
		$result = CheckMobile($mobile);
		
		if($result['operator_id']==0){
			$this -> ReturnJson(false, "失败：手机号码错误！");
		}

		
		//调用聚合数据接口
		//聚合接口没有返回数据
		if ($result['status'] = false)
			$this -> ReturnJson(false, $result['msg']);
		//筛选出省流量包和全国流量包
		$p_ids = 1;
		if ($result['province_id']) {
			$p_ids = $p_ids . "," . $result['province_id'];
		}

		$map = array('operator_id' => $result['operator_id'], 'status' => 1, 'province_id' => array('IN', $p_ids));
		//全国包
		$packety = array();

		//获取手机号产品信息
		//所有通道产品 
		$product_list = D('ChannelProduct')->get_product_list($map,$user_id,$result);
		foreach ($product_list as $key => $row) {
			$is_status=1;//表示产品有效
			if($list){
				foreach ($list as $l){
					if($l==$row['size']){
						$is_status=2;//表示该产品无效
					}
				}
			}
			$s = ($row['size'] >= 1024) ? ($row['size']/1024):1;
			if(is_int($s) && $is_status==1){
				$size = ($row['size'] >= 1024) ? ($row['size']/1024)."G" : ($row['size'])."M";
	
				if ((int)$row['province_id'] == 1) {
					$mid = array('id' => $row['product_id'], 'size' => $size, 'pointValue' => $row['size'] * $rate);
					//默认折扣为全国折扣
					$packety[] = $mid;
				}
			}
		}

		//组装返回数组
		$packet = array( 'operator_id' => $result['operator_id'], 'packets' => $packety);
		//返回数据
		$this -> ReturnJson('success', '', $packet);



		// //获取移动全国流量包
		// $map = array('operator_id' => 1, 'status' => 1, 'province_id' => 1);
		// $msg = D('ChannelProduct') -> get_product_list($map,$user_id);
		// $packety = array();
		// //全国包
		// foreach ($msg as $key => $row) {
		// 	$size = ($row['size'] >= 1024) ? ($row['size'] / 1024) . "G" : ($row['size']) . "M";
		// 	$mid = array('id' => $row['product_id'], 'size' => $size, 'pointValue' => $row['size'] * $rate);
		// 	$packety[] = $mid;
		// }

		// //获取联通全国流量包
		// $map1 = array('operator_id' => 2, 'status' => 1, 'province_id' => 1);
		// $msg1 = D('ChannelProduct') -> get_product_list($map1,$user_id);
		// $packetl = array();
		// //全国包
		// foreach ($msg1 as $key => $row) {
		// 	$size = ($row['size'] >= 1024) ? ($row['size'] / 1024) . "G" : ($row['size']) . "M";
		// 	$mid = array('id' => $row['product_id'], 'size' => $size, 'pointValue' => $row['size'] * $rate);
		// 	$packetl[] = $mid;
		// }

		// //获取电信全国流量包
		// $map2 = array('operator_id' => 3, 'status' => 1, 'province_id' => 1);
		// $msg2 = D('ChannelProduct') -> get_product_list($map2,$user_id);
		// $packetd = array();
		// //全国包
		// foreach ($msg2 as $key => $row) {
		// 	$size = ($row['size'] >= 1024) ? ($row['size'] / 1024) . "G" : ($row['size']) . "M";
		// 	$mid = array('id' => $row['product_id'], 'size' => $size, 'pointValue' => (int)($row['size'] * $rate));
		// 	$packetd[] = $mid;
		// }
	}

	private function _createInFrom($packet_msg, $api_info) {
		// 电话
		$phone = $packet_msg['mobile'];
		// 大小
		$size = $packet_msg['size'];
		// 
		$api_key = $api_info['api_key'];
		$api_account = $api_info['api_account'];
		//				$api_account    = 'LKKKUZMO';
		//				$api_key    = 'KN0lqsyYbWsB65LCZSo13f2zfZhCPdyW';
		$range = (1 == $packet_msg['province_id']) ? 0 : 1;
		$timeStamp = time();
		$post_data = array('account' => $api_account, 'action' => 'Charge', 'phone' => $phone, 'range' => $range, 'size' => $size, 'timeStamp' => $timeStamp, );

		$pre_str = "{$api_key}account={$api_account}&action=Charge&phone={$phone}&range={$range}&size={$size}&timeStamp={$timeStamp}{$api_key}";
		$post_data['sign'] = md5($pre_str);

		$rt = https_request(C('APP_COMMIT_URL'), $post_data);
		if ($rt == false) {
			$this -> ReturnJson("error", "无法连接到服务器", $rt);
		}

		//		$this -> ReturnJson("error", C('APP_COMMIT_URL'), array('score' => $score));
		$arr_rt = json_decode($rt, true);
		return $arr_rt;
		//['respCode'];
	}

}
