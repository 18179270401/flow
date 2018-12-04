

$(document).ready(function(){
	$(".dole_btn").click(function(){
		
		var phoneValue = document.getElementById("phoneNumber").value;
		if (phoneValue != "") {
			if (!/^(13[0-9]|14[0-9]|15[0-9]|18[0-9]|17[0,0-9])\d{8}$/i.test(phoneValue)) {
				alert("请输入正确的手机号");	
			} else {
					$.ajax({
						type: "POST",
						url: "/index.php/Activity/Flowcode/CheckMobile",
						data: {
							phone: phoneValue,
						},
						contentType: "application/x-www-form-urlencoded",
						success: function(data) {
							var address = data.phoneaddress;
							$("#title").text("亲爱的" + phoneValue);
							$("#desctitle").text(address+"用户，请输入您的流量兑换码");
						},
						error: function(data) {}
					});
					
				//查询
				$(".mask").show();
				$(".import_code").show();
			}
		} else {
			alert("手机号码不能为空");
		}
	
	});

	$(".btnexchange").click(function(){

		var phoneNumber = $("#phoneNumber").val();
		var user_type = $('#user_type').val();
		var openid = $("#openid").val();
		var user_id = $('#user_id').val();
		var phoneCode = $('#phoneCode').val();
		//点击兑换phoneCode
		var url  = "/index.php/Activity/Flowcode/active/user_id/"+user_id+"/user_type/"+user_type+"/phoneNumber/"+phoneNumber+"/flowcode_code/"+phoneCode+"/openid/"+openid;
	
    	window.location.href = url;
	});


});
