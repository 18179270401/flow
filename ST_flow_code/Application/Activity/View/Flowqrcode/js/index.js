

$(document).ready(function(){
	$(".dole_btn").click(function(){
		
		var phoneValue = document.getElementById("phoneNumber").value;
		if (phoneValue != "") {
			if (!/^(13[0-9]|14[0-9]|15[0-9]|18[0-9]|17[0,0-9])\d{8}$/i.test(phoneValue)) {
				alert("请输入正确的手机号");	
			} else {
				var url = "/index.php/Activity/Flowqrcode/active/phone/"+phoneValue;
    			window.location.href = url;
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

$(window).resize(function(){
var scale = 1 / devicePixelRatio;
document.querySelector('meta[name="viewport"]').setAttribute('content', 'initial-scale=' + scale + ', maximum-scale=' + scale + ', minimum-scale=' + scale + ', user-scalable=no');
document.documentElement.style.fontSize = document.documentElement.clientWidth / 75 + 'px';
});