<?php
namespace Sdk\Controller;
use Think\Controller;
class WxRechargeRecordController extends Controller {

	public function _initialize() {
		//微信支付
		Vendor("WxPayR.Api");
		Vendor("WxPayR.JsApiPay");
	}
	
    public function aindex()
    {
        // 获取参数
        $rsaKey = $_SERVER["QUERY_STRING"];
		$tmp = stripos($rsaKey, "&");
		if ($tmp != false) {
			$rsaKey = substr($rsaKey, 0, $tmp);
		}

        // 解码
        $strArray = $this -> localdecode($rsaKey);
		$InfoArray = explode(",", $strArray);
        // 取出参数
		$user_type = $InfoArray[0];
		$user_id = $InfoArray[1];
        // $openId = 'oQOm2s5Y0FJ-yPN5cLJzyIETXxNQ';
        // 获取微信配置
        $config = $this -> getconfig($user_type, $user_id);
		$openidkey = $user_type . $user_id . "openid";
		$openId = cookie($openidkey);
		if (empty($openId)) {
			$tools = new \JsApiPay($config);
			$openId = $tools -> GetOpenid();
			cookie($openidkey, $openId);
		}
        // 联表查询充值记录
        $where = array('o.user_type'=>$user_type,'o.we_openid'=>$openId );
        $where['o.pay_status'] = 2;
        if ($user_type == 1) {
			$where['user_type'] = 1;
			$where['proxy_id'] = $user_id;
		} else {
			$where['user_type'] = 2;
			$where['enterprise_id'] = $user_id;
		}
        $data = M('pay_order as o')->field('o.mobile,c.product_name,c.province_id,o.pay_order_code,o.discount_price,o.pay_date')->join('t_flow_channel_product as c on o.product_id = c.product_id','left')->where($where)->order('o.order_date desc')->select();
        $role = "/Application/Sdk/View/WxRechargeRecord/";
        for($i = 0; $i < count($data);$i++)
        {
            $data[$i]['discount_price'] = number_format($data[$i]['discount_price'],2);
        }

        // 查找客服电话，及常见问题
		if ($user_type == 1) {
			$setWhere['user_type'] = 1;
			$setWhere['proxy_id'] = $user_id;
		} else {
			$setWhere['user_type'] = 2;
			$setWhere['enterprise_id'] = $user_id;
		}
        $dataOut = M('user_set') -> where($setWhere) -> find();
		$consumer_phone = $dataOut['consumer_phone'];
        
        // 传递参数
        $this -> assign("user_type", $user_type);
        $this -> assign("user_id", $user_id);
        $this -> assign("consumer_phone", $consumer_phone);
        $this -> assign("data", $data);
        $this -> assign("role", $role);
        $this->display('WxRechargeRecord/index');
    }

    //解密
	public function localdecode($data) {
		$data = base64_decode($data);
		for ($i = 0; $i < strlen($data); $i++) {
			$ord = ord($data[$i]);
			$ord -= 20;
			$string = $string . chr($ord);
		}
		return $string;
	}

	//加密
	public function localencode($data) {
		for ($i = 0; $i < strlen($data); $i++) {
			$ord = ord($data[$i]);
			$ord += 20;
			$string = $string . chr($ord);
		}
		$string = base64_encode($string);
		return $string;
	}

    //获取配置信息
	public function getconfig($user_type, $user_id) {
		if ($user_type == 1) {
			$map['proxy_id'] = $user_id;
		} else {
			$map['enterprise_id'] = $user_id;
		}
		$result = M("user_set") -> where($map) -> find();

		//payment_type  int  1表示运营方收款    2表示企业收款
		$payment_type = $result['payment_type'];
		if ($payment_type == 1) {
			$paymentmap['enterprise_id'] = 90;
			//运营方尚通科技收款
			$result = M("user_set") -> where($paymentmap) -> find();
		}

		//默认还是企业端收款
		$config = array();
		$config['APPID'] = $result['wx_appid'];
		$config['APPSECRET'] = $result['wx_appsecret'];
		$config['MCHID'] = $result['wx_mchid'];
		$config['KEY'] = $result['wx_key'];
		return $config;
	}
}
?>