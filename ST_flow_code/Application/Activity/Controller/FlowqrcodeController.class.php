<?php
/*
 * 流量码活动控制器
 *
 */
namespace Activity\Controller;
use Think\Controller;
class FlowqrcodeController extends Controller {


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

		$user_type=trim(I("user_type"));
        $user_id=trim(I("user_id"));

    
		
        if($user_type==1){
            $proxy_id=$user_id;
            $map['proxy_id']=$proxy_id;
        }else{
            $enterprise_id=$user_id;
            $map['enterprise_id']=$enterprise_id;
        }
        
        $role="/Application/Activity/View/Flowqrcode/";
        $this->assign("role",$role);

        $this->assign("user_type",$user_type);
        $this->assign("user_id",$user_id);
        $this->display("Flowqrcode/index");
    }

	
    public function active()
	{
		//版本号更新css js更新
 		$this->assign('version_number', C('VERSION_NUMBER'));
        $code_phone = trim(I("phone"));  


        //查询手机归属地
		$phoneinfo = CheckMobile($code_phone);


		$randValue = rand(1, 100);

		if ($phoneinfo['operator_id'] == 1) {
            if($randValue > 50)
            {
                $size = 10;
                $product_name = "10M";
            }
            else
            {
                $size = 5;
                $product_name = "5";
            }
		} elseif ($phoneinfo['operator_id'] == 2) {

            if($randValue > 50)
            {
                $size = 20;
                $product_name = "20M";
            }
            else
            {
                $size = 30;
                $product_name = "30M";
            }
		} else {
            $size = 5;
            $product_name = "5M";
		}		



		$record['user_type'] = 2;
		$record['enterprise_id'] = 784;
		//查询api
		$sys_api = M("sys_api") -> where($record) -> find();


        //下单
		$respinfo = $this->Apiflowsubmit($size,$code_phone,$sys_api);

        //包名称
		$this->assign("product_name",$product_name);
        //电话号码
		$this->assign("phone",$code_phone);
        //运营商名称：例如：中国电信
        //运营商名称：例如：中国电信
        $operator_name = $phoneinfo["operator_name"];
        $title = $operator_name.$product_name."流量 当月生效";
		$this->assign("title",$title);
        $role="/Application/Activity/View/Flowqrcode/";
		$this->assign("role",$role);
        $this->display("Flowqrcode/foot");
    }

    //下单
	private function Apiflowsubmit($size, $phone,$sys_api)
	{
		$submiturl = C("API_SUBMIT");
		$phone = $phone;
			//全国包
		$range = 0;
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


    public function aindex(){


        if($user_type==1){
            $proxy_id=$user_id;
            $map['proxy_id']=$proxy_id;
        }else{
            $enterprise_id=$user_id;
            $map['enterprise_id']=$enterprise_id;
        }
        
        $this->assign("role",$role);
        $role="/Application/Activity/View/Flowqrcode/";
        $this->assign("role",$role);
        $this->display("Flowqrcode/aindex");
    }
}