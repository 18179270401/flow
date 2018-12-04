<?php
	include "WxPay.JsApiPay.php";
	if(isset($_GET['code'])){
		$url=$_GET['oldurl']."?code=".$_GET['code'];
		Header("Location: $url");
	}else{
		$tools = new JsApiPay();
		$tools->GetCode($baseUrl);
	}
?>