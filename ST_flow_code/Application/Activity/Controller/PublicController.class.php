<?php
/*
 * 地理位置计算。下单中转，获取最近参与人员控制器
 *
 */
namespace Activity\Controller;
use Think\Controller;
class PublicController extends Controller {

	public function index() {
		$phoneNumber = I("phone");
		$openid = I("openid");
		$user_type = I("user_type");
		$user_id = I("user_id");
		$headimgurl = I("headimgurl");
		$nickname = I("nickname");
		$activity_id = I("activity_id");
		$user_activity_id = I("user_activity_id");
		$submiturl = gethostwithhttp() . "/index.php/Activity/Api/flow_recharge";
		$pd = array('phone' => $phoneNumber, 'openid' => $openid, 'activity_id' => $activity_id, "user_type" => $user_type, "user_id" => $user_id, "headimgurl" => $headimgurl, "nickname" => $nickname, "user_activity_id" => $user_activity_id);
		$rt = https_request($submiturl, $pd);
		//$rt=json_decode($rt,true);
		echo $rt;
	}

	public function rewarded_users() {
		$status = "error";
		$msg = "企业信息错误";

		$user_type = I("user_type");
		$user_id = I("user_id");

		$activity_id = I("aid");
	

		$data['user_type'] = $user_type;
		if ($user_type == 1) {
			$data['proxy_id'] = $user_id;
		} else {
			$data['enterprise_id'] = $user_id;
		}

		$data["user_activity_id"] = $activity_id;
		//让图片必须现实头像
		$data["product_name"] = array("gt", 0);
		$data['wx_photo'] = array("neq", "");
		$oder_list = M("scene_record") -> where($data) -> limit("10") -> order('receive_date desc') -> select();


		if (!$oder_list) {
			$msg = '没有领取记录';
			$this -> ajaxReturn($status, $msg);
		}
		$dt = array();
		foreach ($oder_list as $vo) {
			$da = array();
			$da['wx_photo'] = $vo['wx_photo'];
			$da['wx_name'] = $vo['wx_name'];
			$da['mobile'] = $vo['mobile'];
			$da['product_name'] = $vo['product_name'];
			$da['order_date'] = $vo['receive_date'];
			array_push($dt, $da);
		}
		$status = "success";
		$msg = "查询成功";
		$array = array('status' => $status, 'msg' => $msg, 'data' => $dt, );
		$this -> ajaxReturn($array);
	}

	public function flowvalue_users() {
		$status = "error";
		$msg = "企业信息错误";

		$user_type = I("user_type");
		$user_id = I("user_id");

		$activity_id = I("aid");
	
    	$order_list=M("ticket_exchange as t")
                ->join("t_flow_product as c on c.product_id = t.product_id","inner")
                ->where(array("t.user_activity_id"=>$activity_id))
                -> limit("10") -> order('exchange_time desc') -> select();
		if (!$order_list) {
			$msg = '没有领取记录';
			$this -> ajaxReturn($status, $msg);
		}
		$dt = array();
		foreach ($order_list as $vo) {
			$da = array();
			$da['wx_photo'] = $vo['wx_photo'];
			$da['wx_name'] = $vo['wx_name'];

			$da['product_name'] = $vo['product_name'];
			
			$da['mobile'] = $vo['mobile'];
			$da['order_date'] = $vo['exchange_time'];
			array_push($dt, $da);
		}
		$status = "success";
		$msg = "查询成功";
		$array = array('status' => $status, 'msg' => $msg, 'data' => $dt, );
		$this -> ajaxReturn($array);
	}

	public function independentrewarded_users() {
		$status = "error";
		$msg = "企业信息错误";

		$activity_id = I("aid");
		$user_type =I("user_type");
		$user_id = I("user_id");

		$data["user_activity_id"] = $activity_id;


		if ($user_type == 1) {
			$data['proxy_id'] = $user_id;
		} else {
			$data['enterprise_id'] = $user_id;
		}

		$oder_list = M("scene_record") -> where($data) -> find();

		if (!$oder_list) {
			$msg = '没有领取记录';
			$this -> ajaxReturn($status, $msg);
		}
		$dt = array();
		foreach ($oder_list as $vo) {
			$da = array();
			$da['wx_photo'] = $vo['wx_photo'];
			$da['wx_name'] = $vo['wx_name'];
			$da['mobile'] = $vo['mobile'];
			$da['product_name'] = $vo['product_name'];
			$da['order_date'] = $vo['receive_date'];
			array_push($dt, $da);
		}
		$status = "success";
		$msg = "查询成功";
		$array = array('status' => $status, 'msg' => $msg, 'data' => $dt, );
		$this -> ajaxReturn($array);
	}

	//判定用户是否在商家设定的区域范围内
	public function Comparepostion() {
		$user_id = I("user_id");
		$user_type = I("user_type");
		$activity_id = I("aid");

		//用户存在的未转换的
		//纬度
		$latitude = I("latitude");
		//经度
		$longitude = I("longitude");

		if ($user_type == 1) {
			$map['proxy_id'] = $user_id;
		} else {
			$map['enterprise_id'] = $user_id;
		}
		$map['activity_id'] = $activity_id;
		$suaty = M("scene_user_activity") -> where($map) -> find();
		//读出活动的而信息

		//经纬度
		$point = $suaty["point"];
		$InfoArray = explode(",", $point);

		//纬度
		$enterpriselatitude = $InfoArray[1];
		//经度
		$enterpriselongitude = $InfoArray[0];
		//活动半径
		$enterpriseaccuracy = $suaty["accuracy"] * 1000;
		//如果当前没有位置则定位为万达写字楼
		if (!$enterpriselatitude) {
			$enterpriselatitude = 28.701601;
		}
		if (!$enterpriselongitude) {
			$enterpriselongitude = 115.966275;
		}
		if (!$enterpriseaccuracy) {
			$enterpriseaccuracy = 1000;
		}

		//		$bd_lat="";
		//		$bd_lon="";
		//		$x_pi = 3.14159265358979324 * 3000.0 / 180.0;
		//		$this->bd_encrypt($enterpriselatitude,$enterpriselongitude,$bd_lat,$bd_lon,$x_pi);

		$coords = $longitude . "," . $latitude;
		$ak = "MuCNA4lawFkdYNhDG4hg2Z30vbdbim7C";

		//
		$submiturl = "http://api.map.baidu.com/geoconv/v1/?coords=" . $coords . "&ak=" . $ak . "&output=json" . "&from=1&to=5";
		$rt = https_request($submiturl);
		$obj = json_decode($rt, true);
		$result = $obj["result"];
		$bd_longitude = $result[0]["x"];
		$bd_latitude = $result[0]["y"];

		//$status = $this->getAround($enterpriselatitude,$enterpriselongitude,$enterpriseaccuracy,$bd_latitude,$bd_longitude);
		$kilosmite = $this -> distanceBetween($bd_latitude, $bd_longitude, $enterpriselatitude, $enterpriselongitude);
		if ($kilosmite > $enterpriseaccuracy) {
			$status = "faile";
			$msg = "范围之外";
		} else {
			$status = "success";
			$msg = "范围之内";
		}

		$bd_longitude = sprintf("%.6f", $bd_longitude);
		$bd_latitude = sprintf("%.6f", $bd_latitude);
		//返回转换之后的坐标
		//用户存在的未转换的
		$data["longitude"] = $bd_longitude;
		$data["latitude"] = $bd_latitude;
		$data["enterpriselatitude"] = $enterpriselatitude;
		$data["enterpriselongitude"] = $enterpriselongitude;

		$array = array('status' => $status, 'msg' => $msg, 'data' => $data);
		$this -> ajaxReturn($array);
	}

	/**
	 * 计算两个坐标之间的距离(米)
	 * @param float $fP1Lat 起点(纬度)
	 * @param float $fP1Lon 起点(经度)
	 * @param float $fP2Lat 终点(纬度)
	 * @param float $fP2Lon 终点(经度)
	 * @return int
	 */
	function distanceBetween($fP1Lat, $fP1Lon, $fP2Lat, $fP2Lon) {
		$fEARTH_RADIUS = 6378137;
		//角度换算成弧度
		$fRadLon1 = deg2rad($fP1Lon);
		$fRadLon2 = deg2rad($fP2Lon);
		$fRadLat1 = deg2rad($fP1Lat);
		$fRadLat2 = deg2rad($fP2Lat);
		//计算经纬度的差值
		$fD1 = abs($fRadLat1 - $fRadLat2);
		$fD2 = abs($fRadLon1 - $fRadLon2);
		//距离计算
		$fP = pow(sin($fD1 / 2), 2) + cos($fRadLat1) * cos($fRadLat2) * pow(sin($fD2 / 2), 2);
		return intval($fEARTH_RADIUS * 2 * asin(sqrt($fP)) + 0.5);
	}

	public function test() {
		//次数已用完
		$role = "/Application/Activity/View/Trailer/Flowunlocation/";
		$this -> assign("role", $role);
		$this -> display("Trailer/Flowunlocation/index");
	}

	//代码如下，其中 bd_encrypt 将 GCJ-02 坐标转换成 BD-09 坐标， bd_decrypt 反之。
	//国测局GCJ-02坐标体系（谷歌、高德、腾讯），与百度坐标BD-09体系的转换
	//
	function bd_encrypt($gg_lat, $gg_lon, &$bd_lat, &$bd_lon, $x_pi) {
		$x = $gg_lon;
		$y = $gg_lat;
		$z = sqrt($x * $x + $y * $y) + 0.00002 * sin($y * $x_pi);
		$theta = atan2($y, $x) + 0.000003 * cos($x * $x_pi);
		$bd_lon = $z * cos($theta) + 0.0065;
		$bd_lat = $z * sin($theta) + 0.006;
	}

	//
	//void bd_decrypt(double bd_lat, double bd_lon, double &gg_lat, double &gg_lon)
	//{
	//  double x = bd_lon - 0.0065, y = bd_lat - 0.006;
	//  double z = sqrt(x * x + y * y) - 0.00002 * sin(y * x_pi);
	//  double theta = atan2(y, x) - 0.000003 * cos(x * x_pi);
	//  gg_lon = z * cos(theta);
	//  gg_lat = z * sin(theta);
	//}
}
