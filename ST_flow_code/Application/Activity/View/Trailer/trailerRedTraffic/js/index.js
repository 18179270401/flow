//随机领取手机流量
function getRandomFlowAJAX(){
	
	var phoneValue = $("#phoneNumber").val();
	
	if(phoneValue != ""){
		alert("1212121212121");
		if(!/^(13[0-9]|14[0-9]|15[0-9]|18[0-9]|17[0,0-9])\d{8}$/i.test(phoneValue)){
			
			//alert("发送方是否");
			mui.alert("请输入正确的手机号");
		}else{
			
			$.ajax({
				type:"POST",
				url:'http://' + window.location.host + '/Flow_Server_PHP/Flow_SDK_Module/module/TrafficPlatformUsageScenarios/php/flowRecharge.php',
				data:"phone="+phoneValue,
				contentType: "application/x-www-form-urlencoded",
				success:function(repData){
					alert("repData = " + repData);
					var jsonRepData = jQuery.parseJSON(repData);
					window.location.href="demo.html?msg="+escape(jsonRepData.msg);
				},
			});
		}
		
	}else{
		mui.alert("手机号码不能为空");
	}
}
