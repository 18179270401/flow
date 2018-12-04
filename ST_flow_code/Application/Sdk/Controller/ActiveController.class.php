<?php
namespace Sdk\Controller;
use Think\Controller;
class ActiveController extends Controller {
    public function index(){
    	
        $home_url='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$activity_address = str_replace("Active/index","Sdk/Active",$home_url);	
		var_dump($activity_address);
		exit();
		$activity_address = str_replace("Sdk/Index","Activity/Index",$home_url);	
        	echo "<script language='javascript' type='text/javascript'> window.location='{$activity_address}';</script>";  
			
		
    		$time = time();
		
		$lasttime = cookie("time");
		cookie("time",$time);
		if($time - $lasttime < 5000 && !empty($lasttime))
		{
			return;
		}
		
    		$role = "/Application/Sdk/View/Trailer/trailerRedTraffic/";
        $this->assign("role",$role);
        //$this->assign("active",$active);
        $this->display("trailer/trailerRedTraffic/index"); 
//      $active=trim(I("active"));
//      $this->assign("active",$active);
//      $this->display("Home/HomePage/index"); 
    }
	
	
	public function tel()
	{
		
		$phone = I("phone");
		$tel = "tel://".$phone;
        echo "<script language='javascript' type='text/javascript'> location = '{$tel}';</script>";  
	}

	public function game()
	{
		$phone = "18576755269";
		$result = CheckMobile($phone);
		//1为微信 2为app
		$discountdata['discount_type'] = 1;
        //通过省份
		$discountdata['province_id'] = $result["province_id"];
        //通过运营商
		$discountdata['operator_id'] = $result["operator_id"];
		$dicountsData = M('person_discount')->where($discountdata)->find();
        $dicountData = $dicountsData["charge_discount"];
		if((int)$dicountData == 0 || empty($dicountData))
		{
			$dicountData = 10;
		}

		var_dump($dicountData);

		// $fp = fopen("componentverifyticket.json","a");
		// fwrite($fp, "123333");
		// fclose($fp);
	          


		// $game = I("gamename");
    	// 	$role = "/Application/Sdk/View/Game/".$game."/";
        // $this->assign("role",$role);
        // $this->display("Game/".$game."/index"); 
	}
	public function togame()
	{

		// $fp = fopen("componentverifyticket.json","r");
		// $str = fread($fp,2000);
		// fclose($fp);
	    // var_dump($str);


		$game = I("gamename");
    		$role = "/Application/Sdk/View/Game/".$game."/";
        $this->assign("role",$role);
        $this->display("Game/".$game."/index"); 
	}

	public function WxFlowPayment()
	{
    		$role = "/Application/Sdk/View/WxFlowPayment/";
        $this->assign("role",$role);
        $this->display("WxFlowPayment/index"); 
	}
	
	public function gametest()
	{
	
			//http://115.159.154.93:3102/game?cmd=sync&token=debb8b4ebeea25fd35a26620ae0e0af7&now=1468300581082&index=1&pickUp=6.446605762255071e+156&click=95&crit=37
		//http://115.159.154.93:3102/game?cmd=sync&token=debb8b4ebeea25fd35a26620ae0e0af7&now=1468300509196&index=1&pickUp=6.446605762255071e+156&click=95&crit=27
		$token = "debb8b4ebeea25fd35a26620ae0e0af7";
		$now = time()*1000 + (1468300000266 - 1468256781000);
		$index = 0;
		$pickUp = "1.2893211524510142e+157";
		$click = 95;
		$crit = 59;
		$URL = "";
		//$submiturl = "http://115.159.154.213:3103/game?cmd=sync&token=".$token."&now=".$now."&index=".$index."&pickUp=".$pickUp."&click=".$click."&crit=".$crit;
		//$submiturl = "http://115.159.77.244:3101/game?cmd=keeplive&token=".$token."&now=".$now;
		$submiturl = "http://115.159.158.103:3103/game?cmd=battleEnd&token=debb8b4ebeea25fd35a26620ae0e0af7&now=".$now."&index=3&pickUp=1.9339817286765212e+157&click=60&crit=18";
		//$submiturl = "http://115.159.158.103:3103/game?cmd=battleEnd&token=".$token."&now=".$now."&index=".$index."&pickUp=".$pickUp."&click=".$click."&crit=".$crit;
		//$submiturl = "http://115.159.86.162:3101/game?cmd=useSkill&token=".$token."&now=".$now."&id=5&type=1";
		//$submiturl = "http://115.159.77.244:3101/game?cmd=battleStart&token=".$token."&now=".$now."&type=0";
		$rt = https_request($submiturl, $pd);
		var_dump($rt);
		var_dump($submiturl);

	}
	public function gamestart()
	{
		
			//http://115.159.154.93:3102/game?cmd=sync&token=debb8b4ebeea25fd35a26620ae0e0af7&now=1468300581082&index=1&pickUp=6.446605762255071e+156&click=95&crit=37
		//http://115.159.154.93:3102/game?cmd=sync&token=debb8b4ebeea25fd35a26620ae0e0af7&now=1468300509196&index=1&pickUp=6.446605762255071e+156&click=95&crit=27
		$token = "debb8b4ebeea25fd35a26620ae0e0af7";
		$now = time()*1000 + (1468300000266 - 1468256781000);
		$index = 2;
		$pickUp = "1.2893211524510142e+257";
		$click = 95;
		$crit = 59;
		$URL = "115.159.154.93:3103";
		//$submiturl = "http://115.159.154.213:3103/game?cmd=sync&token=".$token."&now=".$now."&index=".$index."&pickUp=".$pickUp."&click=".$click."&crit=".$crit;
		//$submiturl = "http://115.159.77.244:3101/game?cmd=keeplive&token=".$token."&now=".$now;
		$submiturl = "http://".$URL."/game?cmd=battleStart&token=".$token."&now=".$now."&type=0";
		//$submiturl = "http://115.159.86.162:3101/game?cmd=useSkill&token=".$token."&now=".$now."&id=5&type=1";
		//$submiturl = "http://115.159.77.244:3101/game?cmd=battleStart&token=".$token."&now=".$now."&type=0";
		$rt = https_request($submiturl, $pd);
		var_dump($rt);
		var_dump($submiturl);

	}
	public function api()
	{
		
	}
	
	public function test()
	{
//		$rt = array();//json_decode($rt,true);
//		http://115.159.154.93:3102/game?cmd=sync&token=debb8b4ebeea25fd35a26620ae0e0af7&now=1468300658304&index=2&pickUp=1.2893211524510142e+157&click=95&crit=28
//		var_dump($rt);
//		$data = array();
//		array_push($data,'1024','2048','100','10','50');
//		$rt['data'] = $data;
//		$rt = json_encode($rt);
  	  	$role = "/Application/Sdk/View/WxFlowPayment/";
        $this->assign("role",$role);
        $this->display("../../Sdk/View/WxFlowPayment/index"); 
	}
	
	public function ditu()
	{
		//29.58783218174,114.23182632214
    		//纬度
		$enterpriselatitude = "29.581585367458";
		//经度
		$enterpriselongitude = "114.22539195429";
		//中心区域活动范围
		
		$bd_lat="";
		$bd_lon="";
		$x_pi = 3.14159265358979324 * 3000.0 / 180.0;  
		$this->bd_encrypt($enterpriselatitude,$enterpriselongitude,$bd_lat,$bd_lon,$x_pi);
		var_dump($bd_lat.",".$bd_lon);
		 
	}
	 function bd_encrypt($gg_lat, $gg_lon,&$bd_lat,&$bd_lon,$x_pi)
 	{
	    $x = $gg_lon;
	    $y = $gg_lat;  
	    $z = sqrt($x * $x + $y * $y) + 0.00002 * sin($y * $x_pi);  
	    $theta = atan2($y, $x) + 0.000003 * cos($x * $x_pi);  
	    $bd_lon = $z * cos($theta) + 0.0065;  
	    $bd_lat = $z * sin($theta) + 0.006;  
	}
	
    public function active(){
		$submiturl = "http://121.41.8.25:8081/Submit123.php";
		$phone = 18579181625;
		$range = 0;
		$size = 20;
		//单位 M
		$account    = 'LKKKUZMO';
		$api_key    = 'KN0lqsyYbWsB65LCZSo13f2zfZhCPdyW';
//		$account = $sys_api['api_account'];
//		$api_key = $sys_api['api_key'];
		$timeStamp = time();
		$pd = array('account' => $account, 'action' => 'Charge', 'phone' => $phone, 'range' => $range, 'size' => $size, 'timeStamp' => $timeStamp, );
		$pre_str = "{$api_key}account={$account}&action=Charge&phone={$phone}&range={$range}&size={$size}&timeStamp={$timeStamp}{$api_key}";
		$pd['sign'] = md5($pre_str);
		$rt = https_request($submiturl, $pd);
		$ret = json_decode($rt, true);
		var_dump($ret);
    }

    public function foot(){
    		$balance_url = 'http://121.41.8.25:8081/Balance.php';
		$account    = 'ZSORCRGX';
		$api_key    = 'sAAAzPv58SCz7vbeW2GRi2ITN7pnB5Kx';
		$timeStamp = time();
		$bd = array(
		'account'	=> $account,
		'action'	=> 'Balance',
		'timeStamp'	=> $timeStamp,
		);
		$bd['sign'] = md5("{$api_key}account={$account}&action=Balance&timeStamp={$timeStamp}{$api_key}");
		$rt = https_request($balance_url, $bd);
		$ret = json_decode($rt, true);
		var_dump($bd);echo '<hr />';
		var_dump($rt);echo '<hr />';
		var_dump($ret['respMsg']);
    }
}