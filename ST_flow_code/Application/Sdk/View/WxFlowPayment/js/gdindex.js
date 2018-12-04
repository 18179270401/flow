$('list2').hide();
$('list1').show();


$("body").on("propertychange input", "input", function() {
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
			user_id: user_id
		},
		contentType: "application/x-www-form-urlencoded",
		success: function(data) {
			callback(data);
			$(".modelClass").css("display", "none");
		},
		error: function(data) {}
	});
}

function testPhoneNumber(inputText) {
    
    if(inputText ==""||inputText == null)
    {
	    var inputText = $('input').val();
    }
	if(!/^(13[0-9]|14[0-9]|15[0-9]|18[0-9]|17[0,0-9])\d{8}$/i.test(inputText)) {
		$('.recharge_list ul li').remove();
	} else {
		//$('img').attr("src", "/Application/Sdk/View/WxFlowPayment/images/icon_1.png");
		$('.recharge_list ul li').remove();



		phoneMobile(inputText, function callback(data) {
			var parsedJson = eval('(' + data + ')');

			if(parsedJson.msg == 1001) {
				var title = "对不起，只允许" + parsedJson.data.province + "内" + parsedJson.data.operator + "号码充值!";
				$('#OperatorsName').text(title);
				return;
			}

			//var parsedJson = jQuery.parseJSON(data);

    		if(inputText != "18507085074")
			{
				var role = $("#role").val();
				$('#OperatorsName').text(parsedJson.data.attribution);
				// if(parsedJson.data.belong == "中国联通")
				// {
				// 	var path = role + "images/liantong.png";
				// 	$("#OperatorsImg").attr("src", path);//改变图片源 
				// }
				// else if(parsedJson.data.belong == "中国电信")
				// {
				// 	$("#OperatorsImg").attr("src", "./images/dianxin.png");//改变图片源 
				// }
				// else if(parsedJson.data.belong == "中国移动")
				// {
				// 	$("#OperatorsImg").attr('src',"images/yidong.png ");
				// }
			}
			else
			{
				$('#OperatorsName').text("归属地");
			}
			$.each(parsedJson.data.Type, function(i, item) {
				if(item.type == "全国") {
					$.each(item.packet, function(i, item) {
						BCratButton(item, 'list1', i,inputText);
					});
				} else {
					$.each(item.packet, function(i, item) {
						BCratButton(item, 'list2', i,inputText);
					});
				}
			});

			selectButton('list1'); //千万不要删 删了没有效果
			selectButton('list2'); //千万不要删 删了没有效果
		});
	}
}
function countryClick(button) {
	$('#tip').html('全国可以用，立即生效，当月有效');

	$('.list2').hide();
	$('.list1').show();

	$('#country').addClass("active");
	$('#province').removeClass("active");
}

function provinceClick(button) {
	$('.list1').hide();
	$('.list2').show();
	$('#tip').html('省内可以用，立即生效，当月有效');

	$('#province').addClass("active");
	$('#country').removeClass("active");
}
countryClick();


//创建按钮
function BCratButton(item, id, i,inputText) {

	// if(item.sizeType == 1)
	// {
	// 	$('.' + id + " ul").append('' +
	// 		'<li' +
	// 		'<div class=\"item_row\" >' +
	// 		    '<div class=\"item_txt\" >' +
    //                 '<span>' + item.size + '</span> ' +
    //                 '<em class=\"throuline_txt\">' + item.givesize + '</em> ' +
    //                 '<em">售价' + item.price_discount + '元</em> ' +
	// 		    '</div>' +
	// 		'</div>' +
	// 		'</li>');
	// }
	// else
	// {         

		$('.' + id + " ul").append('' +
			'<li> ' +
			
			'<div >' +
			    '<div>' +
                    '<span>' + item.size + '</span> ' +
                    '<em class=\"prime_cost\"><s style=\"margin:0px;\">原价' + item.price_market + '</s></em> ' +
                    '<em  class=\"price\" >售价' + item.price_discount + '元</em> ' +
			    '</div>' +
			'</div>' +
			'</li>');
	// }
    //设定一个联通号码默认显示联通包
    if(inputText != "18507085074")
	    $('.' + id + " ul li").eq(i)[0].jsonData = item;
}

//初始化显示联通数据包
testPhoneNumber("18507085074"); //不能删删了会出bug

//选择按钮事件
function selectButton(id) {
	jQuery("." + id + " ul li").each(function(index) {
		jQuery(this).click(function() {
			var user_type = $("#user_type").val();
			var user_id = $("#user_id").val();
            if($(this)[0].jsonData == null)
            {
                alert("请输入您的手机号码");
                return;
            }
			//当前选择的产品价格
			var price_market = $(this)[0].jsonData.price_market;
			var pid = $(this)[0].jsonData.id;   
			var openid = $("#openid").val();
			var phone = $('input').val();

			var givesize = $(this)[0].jsonData.givesize;
			var sizeType = $(this)[0].jsonData.sizeType;

			
			$(".modelClass").css("display", "block");
			$.ajax({
				type: "POST",
				url: "/index.php/Sdk/Api/GetBalance",
				data: {
					user_type: user_type,
					user_id: user_id
				},
				contentType: "application/x-www-form-urlencoded",
				success: function(jsondata) {
					//查询当前余额

					var lastmoney = parseFloat(jsondata.data);
					price_market = parseFloat(price_market);
					if(lastmoney < price_market) {
						var url = 'http://' + window.location.host + '/index.php/Sdk/WxFlowPayment/limitmoney';
						location.href = url;
						return;
					}
					if(isWeiXin()){
						setTimeout(function() {
							$(".modelClass").css("display", "none");
						}, 1250);
						var url = "/index.php/Sdk/WxFlowPayment/wxpay?pid=" + pid + "&phone=" + phone + "&openid=" + openid + "&user_type=" + user_type + "&user_id=" + user_id;
						$("#wxpay").load(url);
					}
					else
					{	
						var url = "/index.php/Sdk/WxFlowPayment/alipay?pid=" + pid + "&phone=" + phone + "&user_type=" + user_type + "&user_id=" + user_id + "&sizeType=" + sizeType + "&givesize=" + givesize;
						location.href = url;
					}

						// var url = "/index.php/Sdk/WxFlowPayment/appwxpay?pid=" + pid + "&phone=" + phone + "&user_type=" + user_type + "&user_id=" + user_id;
						// location.href = url;

					//alert(url);
					//location.href = url;
				},
				error: function(data) {}
			});
			function isWeiXin(){
				var ua = window.navigator.userAgent.toLowerCase();
				if(ua.match(/MicroMessenger/i) == 'micromessenger'){
					return true;
				}else{
					return false;
				}
			}
			//先设置所有颜色为默认
			//			$("#tableView li a").css("border-color", "#C0C0C0");

			//设置选中元素的CSS颜色
			//			$("#tableView li a").eq(index).css("border-color", "#D33B3D");

		});
	});
}


	// function getCookie(name) 
	// { 
	// 	var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)"); 
	// 	if(arr=document.cookie.match(reg)) return unescape(arr[2]); 
	// 	else return null; 
	// } 
	// function setCookie(name, value) {
	// 	var Days = 30;
	// 	var exp = new Date();
	// 	exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);
	// 	document.cookie = name + "=" + escape(value) + ";expires" + exp.toGMTString();
	// }
