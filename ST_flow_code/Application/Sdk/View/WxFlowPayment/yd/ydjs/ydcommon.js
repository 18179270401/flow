

$("body").on("propertychange input", "input", function () {
	testPhoneNumber();
});

function phoneMobile(phone, callback) {
	$(".modelClass").css("display", "block");
	var user_type = $("#user_type").val();
	var user_id = $("#user_id").val();
	$.ajax({
		type: "POST",
		url: "/index.php/Sdk/WxFlowPayment/GetFlowProtuct",
		data: {
			phone: phone,
			sign: "870d244a38421e6dba1f938e73a44b2e",
			user_type: user_type,
			user_id: user_id,
			product: "1"
		},
		contentType: "application/x-www-form-urlencoded",
		success: function (data) {
			callback(data);
			$(".modelClass").css("display", "none");
		},
		error: function (data) { }
	});
}

function testPhoneNumber(inputText) {

	if (inputText == "" || inputText == null) {
		var inputText = $('input').val();
	}
	if (!/^(13[0-9]|14[0-9]|15[0-9]|18[0-9]|17[0,0-9])\d{8}$/i.test(inputText)) {
		// $('.recharge_list ul li').remove();
		// $('.flow_list ul li').remove();
	} else {
		//$('img').attr("src", "/Application/Sdk/View/WxFlowPayment/images/icon_1.png");
		$('.recharge_list ul li').remove();
		$('.flow_list ul li').remove();
		phoneMobile(inputText, function callback(data) {
			var parsedJson = eval('(' + data + ')');

			if (parsedJson.msg == 1001) {
				var title = "对不起，只允许" + parsedJson.data.province + "内" + parsedJson.data.operator + "号码充值!";
				$('#OperatorsName').text(title);
				return;
			}

			//var parsedJson = jQuery.parseJSON(data);

			if (inputText != "18507085074") {
				$('#OperatorsName').text(parsedJson.data.attribution);
			}
			else {
				$('#OperatorsName').text("号码归属地");
			}
			$.each(parsedJson.data.packetAll, function (i, item) {
				//全国流量折扣价格，显示
				if (item.packetS == null) {
					//只有省内包
					BCratButton(item.packetQ, i, item, inputText);
				}
				else if (item.packetQ == null) {
					//只有全国包
					BCratButton(item.packetS, i, item, inputText);
				}
				else {
					if (item.packetQ.price_discount > item.packetS.price_discount) {
						BCratButton(item.packetS, i, item, inputText);
					}
					else {
						BCratButton(item.packetQ, i, item, inputText);
					}
				}
			});
			//绑定点击效果
			selectButton();
		});
	}
}

//创建按钮
function BCratButton(item, i, data, inputText) {
	$(".recharge_list ul").append('' +
		'<li class=\"favourable\" >' +
		'<div class=\"recharge_item\" >' +
		'<span>' + item.size + '</span> ' +
		'<p>' + item.price_discount + '元<em> ' + item.price_market + '元</em></p> ' +
		'</div>' +
		'</li>');


	$(".recharge_list ul li").eq(i)[0].jsonData = data;
}

var phone = $('input').val();
if (phone == "") {
	//初始化显示联通数据包
	testPhoneNumber("18507085074"); //不能删删了会出bug
}
else {
	testPhoneNumber();
}

//选择按钮事件
function selectButton() {
	jQuery(".recharge_list ul li").each(function (index) {
		jQuery(this).click(function () {

			// if ($(this)[0].jsonData == null) {
			// 	alert("请输入您的手机号码");
			// 	return;
			// }
			$('.active').removeClass("active");
			$(this).addClass("active");

			//清理下列表
			$('.flow_list ul li').remove();
			var item = $(this)[0].jsonData;
			//全国流量折扣价格，显示
			if (item.packetS == null) {
				//只有全国包
				BCratDetailButton(item.packetQ, 0, 1);
			}
			else if (item.packetQ == null) {
				//只有省内包
				BCratDetailButton(item.packetS, 0, 0);
			}
			else {
				//省内全国
				BCratDetailButton(item.packetS, 0, 0);
				BCratDetailButton(item.packetQ, 1, 1);
			}

			//绑定按钮单机事件
			selectDetailButton();

			//先设置所有颜色为默认
			//			$("#tableView li a").css("border-color", "#C0C0C0");

			//设置选中元素的CSS颜色
			//			$("#tableView li a").eq(index).css("border-color", "#D33B3D");

		});
	});
}

//创建按钮
function BCratDetailButton(item, i, type) {
	if (type == 1) {
		//全国
		var address = "全国";
		var addressinfo = "全国可用，即时生效，当月有效";
	}
	else {
		var address = "省内";
		var addressinfo = "省内可用，即时生效，当月有效";
	}

	$(".flow_list ul").append('' +
		'<li>' +
		'<div class=\"flow_item\" >' +
		'<div class=\"flow_left\" >' +
		'<p>' + item.price_discount + '元<em>' + address + '</em></p> ' +
		'<span>' + addressinfo + '</span> ' +
		'</div>' +
		'<div class=\"flow_right\" >' +
		'<button class=\"btnpay\" type=\"button"\ >购买</button>' +
		'</div>' +
		'</div>' +
		'</li>');
	$(".flow_list ul li .btnpay").eq(i)[0].jsonData = item;
}

//全局临时数据存储
var locationData;
//支付方式
//var payType = 0;

//判断是否为微信
function isWeiXin() {
	var ua = window.navigator.userAgent.toLowerCase();
	if (ua.match(/MicroMessenger/i) == 'micromessenger') {
		return true;
	} else {
		return false;
	}
}

$(document).ready(function () {
	$(document).click(function () {
		//针对所有的点击事件关闭弹出框
		$("body").removeClass("ban_scroll");
		setTimeout(function () {
			$(".mask").hide();
		}, 500);
		$(".confirm_box").removeClass("open").addClass("close");
	});

	$(".confirm_header span>i").click(function () {
		$("body").removeClass("ban_scroll");
		setTimeout(function () {
			$(".mask").hide();
		}, 500);
		$(".confirm_box").removeClass("open").addClass("close");
	});
	$(".confirm_box").click(function (e) {
		e.stopPropagation();
	});
	$(".payment").click(function (e) {
		clickpay(locationData);
	});
});

//点击按钮触发事件
function selectDetailButton() {
	jQuery(".flow_list ul li .btnpay").each(function (index) {
		jQuery(this).click(function (e) {

			var inputText = $('.input').val();
			if (inputText == null || inputText.length == 0) {
				alert("请输入您的手机号码");
				inputText = "无";
				return;
			}

			e.stopPropagation();
			$("body").addClass("ban_scroll");
			$(".confirm_box").removeClass("close").addClass("open");
			$(".mask,.confirm_box").show();


			if (!isWeiXin()) {
				$(".zhifubao").addClass("active");//.siblings().removeClass("active");
				$(".weixin").hide();
			}
			else {
				$(".weixin").addClass("active");//.siblings().removeClass("active");
				$(".zhifubao").hide();
			}
			var item = $(this)[0].jsonData;
			//中国移动联通电信
			var OperatorsName = $('#OperatorsName').val();
			var title = OperatorsName + "流量充值" + item.size + "流量包";
			var money = "￥" + item.price_discount;
			$(".selectinfo").text(title);

			$(".teleephonenumber").text(inputText);
			$(".paytextinfo").text(money);

			locationData = item;

		});
	});
}

function clickpay(jsonData) {
	var user_type = $("#user_type").val();
	var user_id = $("#user_id").val();
	//当前选择的产品价格
	var price_market = jsonData.price_market;
	//该流量包来自哪个企业
	var user_id = jsonData.productuser_id;

	var pid = jsonData.id;
	var openid = $("#openid").val();
	var phone = $('input').val();


	$(".modelClass").css("display", "block");
	$.ajax({
		type: "POST",
		url: "/index.php/Sdk/Api/GetBalance",
		data: {
			user_type: user_type,
			user_id: user_id
		},
		contentType: "application/x-www-form-urlencoded",
		success: function (jsondata) {
			//查询当前余额

			var lastmoney = parseFloat(jsondata.data);
			price_market = parseFloat(price_market);
			if (lastmoney < price_market) {
				var url = 'http://' + window.location.host + '/index.php/Sdk/WxFlowPayment/limitmoney';
				location.href = url;
				return;
			}
			//在微信内。并且选择微信
			if (isWeiXin()) {
				setTimeout(function () {
					$(".modelClass").css("display", "none");
				}, 1250);
				var url = "/index.php/Sdk/WxFlowPayment/wxpay?pid=" + pid + "&phone=" + phone + "&openid=" + openid + "&user_type=" + user_type + "&user_id=" + user_id;
				$("#wxpay").load(url);
			}
			else {
				var url = "/index.php/Sdk/WxFlowPayment/alipay?pid=" + pid + "&phone=" + phone + "&user_type=" + user_type + "&user_id=" + user_id;

				location.href = url;
			}

			// var url = "/index.php/Sdk/WxFlowPayment/appwxpay?pid=" + pid + "&phone=" + phone + "&user_type=" + user_type + "&user_id=" + user_id;
			// location.href = url;

			//alert(url);
			//location.href = url;
		},
		error: function (data) { }
	});
}


/*轮播图*/
var count=0;
var timeId;
var bannerItem=$(".banner_item");
$(".banner_row").css("width",bannerItem.length*100+"vw");
bannerRun();
function bannerRun(){
clearTimeout(timeId);
if(count<bannerItem.length){
$(".banner_row").animate({left:-count*100+"vw"},500);
count++;
timeId=setTimeout(bannerRun,2500);
}else{
var firItem=$(".banner_row .banner_item:last-of-type").clone();
$(".banner_row").css("left",0).prepend(firItem).find(".banner_item:last-of-type").remove();
count=1;
timeId=setTimeout(bannerRun,0);
}

};