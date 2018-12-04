//随机领取手机流量
function getRandomFlowAJAX() {

	var phoneValue = document.getElementById("phoneNumber").value;
	if (phoneValue != "") {
		if (!/^(13[0-9]|14[0-9]|15[0-9]|18[0-9]|17[0,0-9])\d{8}$/i.test(phoneValue)) {
			alert("请输入正确的手机号");	
		} else {
			$("#form1").submit();
		}
	} else {
		alert("手机号码不能为空");
	}
}