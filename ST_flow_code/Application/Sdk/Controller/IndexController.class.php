<?php
namespace Sdk\Controller;
use Think\Controller;
class IndexController extends Controller {
	public function aindex()
	{
        $home_url='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$activity_address = str_replace("Sdk/Index","Activity/Index",$home_url);	
		$activity_address = str_replace("Sdk/index","Activity/index",$activity_address);	
        echo "<script language='javascript' type='text/javascript'> window.location='{$activity_address}'</script>";  
	}
	
    public function index(){
        $home_url='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$activity_address = str_replace("Sdk/Index","Activity/Index",$home_url);	
		$activity_address = str_replace("Sdk/index","Activity/index",$activity_address);	
        echo "<script language='javascript' type='text/javascript'> window.location='{$activity_address}';</script>";  
    }

    public function juanindex(){
        $home_url='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$activity_address = str_replace("Sdk/Index","Activity/FlowValue",$home_url);	
		$activity_address = str_replace("Sdk/index","Activity/FlowValue",$activity_address);		 
        echo "<script language='javascript' type='text/javascript'> window.location='{$activity_address}';</script>";  
    }
	
	public function app()
	{
		// if($this->is_weixin())
		// {
      	// 	echo "<script language='javascript' type='text/javascript'> alert(请使用qq进行扫描二维码)';</script>"; 
		// }
		// else
		// {
		// }
		$role = "/Application/Sdk/View/AppWeb/";
		$this -> assign("role", $role);
     	$this->display("AppWeb/index"); 
	}
	
	function is_weixin() {
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
			return true;
		}
		return false;
	}

}
