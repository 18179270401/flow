<?php
$APPID = "wx8dfc60ab54550312";
if(!isset($_GET['code'])){
	$oldurl='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$url= "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$APPID."&redirect_uri=".$oldurl."&scope=snsapi_userinfo&response_type=code&state=123#wechat_redirect";
	Header("Location: $url");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>流量充值</title>
    <script src="/Flow_SDK_Module/utils/js/jquery-2.2.3.js"></script>
    <script type="text/javascript" charset="utf-8">
    var url = 'http://' + window.location.host + '/Flow_SDK_Module/module/wxpay/WxauthorizationInfo.php';
		$(document).ready(function(){
			var code = $("#code").val();			
			$("#wxinfo").load(url+"?code="+code);
			
			var code = $("#wxinfo").val();		
		});
    </script>
</head>
<body>
	<input type="hidden" name="code" id="code" value="<?php echo $_GET['code'];?>">
	<div id="wxinfo" type="hidden"></div>
	<div></div>
</body>
</html>
