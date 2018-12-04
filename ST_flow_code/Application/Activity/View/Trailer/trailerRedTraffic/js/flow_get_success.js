if(getUrlParam('flowSize') == 0){
	$('#txtFlowValue').html("");
	$('#txtGetFlowValueInfo').html("");
}

$("#txtFlowValue").html(getUrlParam('flowSize') + "M")
// $('#txtGetFlowValueSize')[0].innerHTML = getUrlParam('flowSize') + "M";

//电话号码模糊处理
function formatPhone(phone) {
	return phone.replace(/(\d{3})\d{4}(\d{4})/, "$1****$2");
}

//用户名称过长，缩略显示
function fixWx_name(wx_name) {
	if (wx_name.length > 6){
		return wx_name.substring(0,4)+"...";
	}else{
		return wx_name;
	}
}

// 获取获奖用户
var aid=$("#user_activity_id").val();
var user_type=$("#user_type").val();
var user_id=$("#user_id").val();
$.ajax({
	type: "GET",
	url: '/index.php/Activity/Public/rewarded_users',
	data:{aid:aid,user_type:user_type,user_id:user_id},
	dataType: 'json',
	contentType: "application/x-www-form-urlencoded",
	success: function(repData) {
		if(repData.data.length == 0){
			console.log("return 0 size array");
			return;
		}
		$.each(repData.data, function(index, element) {
			console.log(index + " " + element.mobile);
			if(index == 0){
				$('#mobile').html(formatPhone(element.mobile));
				$('#nickname').html(formatPhone(fixWx_name(element.wx_name)));
				document.getElementById("avatar").src=element.wx_photo;
				$('#product').html("抢到"+element.product_name+"流量");
				var str = element.order_date;
		 		$('#date').html(str.substring(5,str.length-3));
			}
		});
	},
});
