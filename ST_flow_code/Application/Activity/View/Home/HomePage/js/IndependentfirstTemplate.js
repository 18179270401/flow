//随机领取手机流量
function getRandomFlowAJAX() {

	var phoneValue = $("#phoneNumber").val();
	var mod=$("#mod").val();
	var func=$("#func").val();
	//var url=$("#url").val();
	if (phoneValue != "") {
		if (!/^(13[0-9]|14[0-9]|15[0-9]|18[0-9]|17[0,0-9])\d{8}$/i.test(phoneValue)) {

			alert("请输入正确的手机号");
			//mui.alert("请输入正确的手机号");
		} else {
			$("#form1").submit();
		}
	} else {
		//mui.alert("手机号码不能为空");
		alert("手机号码不能为空");
	}
}

//活动规则弹出框
$("#rule").click(function(){
  $('.bg').fadeIn(200);
  $('.content').fadeIn(400);
});

//活动规则遮罩层
$(".bg").click(function(){
  $('.bg').fadeOut(200);
  $('.content').fadeOut(200);
});

//电话号码模糊处理
function formatPhone(phone) {
	return phone.replace(/(\d{3})\d{4}(\d{4})/, "$1****$2");
}

//轮播
function loop() {
	var items = award.find('.mui-table-view-cell');
	if (items.length > 3) {
		items.eq(0).addClass('move');
		setTimeout(function() {
			items.eq(0).appendTo(award).removeClass('move');
			loop();
		}, 2000);
	}
};


// 获取获奖用户
var aid=$("#aid").val();
var user_type=$("#user_type").val();
var user_id=$("#user_id").val();
$.ajax({
	type: "POST",
 	url: '/index.php/Activity/Public/independentrewarded_users/',
 	data:{aid:aid,user_type:user_type,user_id:user_id},
	dataType: 'json',
	contentType: "application/x-www-form-urlencoded",
	success: function(repData) {
		$.each(repData.data, function(index, element) {
			createTableItem(element);
			console.log(index + " " + element.mobile);
		});
	},
	error:function(){
	}
});

//用户名称过长，缩略显示
function fixWx_name(wx_name) {
	if (wx_name.length > 6){
		return wx_name.substring(0,4)+"...";
	}else{
		return wx_name;
	}
}

//动态生成table_cell
function createTableItem(element) {
	var str = element.order_date;
	$('#mui-table-view').append(
		'<li class=\"mui-table-view-cell mui-media\" style=\"height: 4rem;\">' +
		'<div class=\"mui-media-body\" style=\"color:white;display:inline-block;font-size:0.8rem\">' +
		'<p class=\"mui-ellipsis\" style=\"font-size:0.8rem;margin-top:0.4rem; color:white \"> 恭喜' + formatPhone(element.mobile) +
		'</p>' + '</div>' + '<div class=\"mui-media-right\" style=\"font-size:0.8rem;\">' +
		'抢到' + element.product_name + '<p class=\"mui-ellipsis\" style=\"text-align:right;font-size:0.6rem;margin-top:0.2rem;\">' + str.substring(5,str.length-3) +
		'</p>' + '</div>' + '</li>'
	);
	loop();
}

	$('.zandiv').bind('click', function () {
		alert('123');
    		var M = '<?php echo show()?>';
		alert(M);
	});