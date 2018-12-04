$('#tableView .list2').hide();
$('#tableView .list1').show();
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
			mui.closePopup();
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
		$('#tableView li').remove();
		loadSpace();
	} else {
		//		$('img').attr("src", "/Application/Sdk/View/WxFlowPayment/images/icon_1.png");
		$('#tableView li').remove();
		loadSpace();
		setCookie('inputText', inputText);
		
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
				$('#OperatorsName').text(parsedJson.data.attribution);
			}
			else
			{
				$('#OperatorsName').text("");
			}
			$.each(parsedJson.data.Type, function(i, item) {
				if(item.type == "全国") {
					$.each(item.packet, function(i, item) {
						BCratButton(item, 'list1', i, inputText);
					});
				} else {
					$.each(item.packet, function(i, item) {
						BCratButton(item, 'list2', i, inputText);
					});
				}
			});

			selectButton('list1'); //千万不要删 删了没有效果
			selectButton('list2'); //千万不要删 删了没有效果
			loadSpace();
		});
	}
}

function countryClick(button) {
	$('#provinceButton').css('backgroundColor', 'white');
	$('#provinceButton').css('color', 'black');
	$('#provinceButton').css('border-color', 'gray');
	$('#countryButton').css('backgroundColor', '#D33B3D')
	$('#countryButton').css('color', 'white')
	$('#countryButton').css('border-color', '#D33B3D');
	$('#tipH5').html('全国可以用，立即生效，当月有效');

	$('#tableView .list2').hide();
	$('#tableView .list1').show();
	loadSpace();
}

function provinceClick(button) {
	$('#tableView .list1').hide();
	$('#tableView .list2').show();
	$('#provinceButton').css('backgroundColor', '#D33B3D');
	$('#provinceButton').css('color', 'white');
	$('#provinceButton').css('border-color', '#D33B3D');

	$('#countryButton').css('backgroundColor', 'white')
	$('#countryButton').css('color', 'black')
	$('#countryButton').css('border-color', 'gray');
	$('#tipH5').html('省内可以用，立即生效，当月有效');
	loadSpace();
}
countryClick();

function provinceClick1(responsetext) {
	alert(responsetext);
}

//创建按钮
function BCratButton(item, id, i,inputText) {

	$('#tableView .' + id).append('' +
			'<li class=\"mui-table-view-cell mui-media mui-col-xs-4 mui-col-sm-3\" style="padding:0.25rem 0.25rem";>' +
			'<a  style="border: 1px solid gray;-moz-border-radius: 5px;-webkit-border-radius: 5px;border-radius:5px;background-color: white;border-color: #C0C0C0;margin:0px\">' +
			'<h4 style=\"margin:2px;color: #43AA57;font-weight:normal\">' + item.size + '</h4> ' +
			'<h5 style=\"margin:2px;color: #43AA57;font-weight:200\"><s style=\"margin:0px;color: #43AA57;\">原价' + item.price_market + '元</s></h5> ' +
			'<h6 style=\"margin:2px;color: #43AA57;\">售价' + item.price_discount + '元</h6> ' +
			'</a>' +
			'</li>');

	$("#tableView ." + id + " li").eq(i)[0].jsonData = item;
}

var phone = $('input').val();
if(phone == "")
{
	//初始化显示联通数据包
	testPhoneNumber("18507085074"); //不能删删了会出bug
}
else
{
	testPhoneNumber();
}
//选择按钮事件
function selectButton(id) {
	jQuery("#tableView ." + id + " li").each(function(index) {
		jQuery(this).click(function() {
			var user_type = $("#user_type").val();
			var user_id = $("#user_id").val();
			//当前选择的产品价格
			var price_market = $(this)[0].jsonData.price_discount;
			var pid = $(this)[0].jsonData.id;
			var openid = $("#openid").val();
			var phone = $('input').val();

			if(phone != "18507085074" && phone != "")
			{
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
							var url = "/index.php/Sdk/WxFlowPayment/alipay?pid=" + pid + "&phone=" + phone + "&user_type=" + user_type + "&user_id=" + user_id;
							location.href = url;
						}
						//alert(url);
						//location.href = url;
					},
					error: function(data) {}
				});
			}
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