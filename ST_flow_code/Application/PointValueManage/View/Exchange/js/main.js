var _index = undefined;
var _data;
$('#confirm-btn').css('display', 'none');
var user_id = $('#userid').val();
var user_type = $('#usertype').val();
var openid = $('#openid').val();
var inputText;
$("#mobile").on("propertychange input", function() {
	verifyPhoneNumber();
});

function verifyPhoneNumber() {
	inputText = $('#mobile').val();
	if (!/^(13[0-9]|14[0-9]|15[0-9]|18[0-9]|17[0,0-9])\d{8}$/i.test(inputText)) {
		$('.p-content').css('display', 'none');
		$('#confirm-btn').css('display', 'none');
		$('#package-size-text').text("");
		_index = undefined;
	} else {
		_index = -1;
		$('li').removeAttr("id");
		
			$('.loading-view').css('display', 'block');
			$.ajax({
				type: "POST",
				url: "/index.php/PointValueManage/Api/pointValue_packets",
				contentType: "application/x-www-form-urlencoded",
				data: {
					user_id: user_id,
					user_type: user_type,
					phone:inputText
				},
				success: function(parsedJson) {
					$('.loading-view').css('display', 'none');
					$('#confirm-btn').css('display', 'block');
					$('.p-content').css('display', 'block');
					createListItem(parsedJson.data.packets);
				},
				error: function(data) {
					$('.loading-view').css('display', 'none');
				}
			});
	}
}
verifyPhoneNumber();


function createListItem(data) {
	_data = data;
	$('.p-content').empty();
	$.each(data, function() {

		if(this.pointValue >= 10000) {
			$(".p-content").append('<li><a><h1 style="font-size:1rem; margin-top: 0.65rem;">' + 10240 + '<img src="'+role+'/img/integral.png" width="25%"/></h1><h2>' + '10G' + '</h2></a></li>');
		}  else {
			$(".p-content").append('<li><a><h1>' + this.pointValue + '<img src="'+role+'/img/integral.png" width="25%"/></h1><h2>' + this.size + '</h2></a></li>');
		}
	});
	
	var varContent = $('li');
	for (var i = 0; i < varContent.length; i++) {
		var li = varContent.eq(i)[0];
		li.index = i;
	}
	varContent.click(function() {
		$('li').removeAttr("id");
		$(this).attr('id', 'p-content-item-highlight');
		var s = $(this).children("a").children('h2').text();
		$('#package-size-text').text(s);
		_index = $(this)[0].index;
		//		alert(_index);
	});
}

function exchangeClick() {
	if (_index == undefined) {
		alert("请输入正确的手机号");
	} else if (_index >= 0) {
		if(mobile == '-1')
		{
			if (confirm('点击兑换手机号码将绑定，绑定后将不可更改')) {
				//按确认后做什么

			}
			else
			{
				return;
			}
		}
		//		alert(JSON.stringify());
		$('.loading-view').css('display', 'block');
		$.ajax({
			type: "POST",
			url: "/index.php/PointValueManage/Api/exchangeEvent",
			contentType: "application/x-www-form-urlencoded",
			data: {
				userid: user_id,
				usertype: user_type,
				openid: openid,
				packet_id: _data[_index].id,
				input_text: inputText
			},
			success: function(parsedJson) {
				mobile = inputText;
				$('#mobile').attr("readonly",true);
//				$('.tipClass').css("display",'none');
				if (parsedJson.status == 1) {
					$(".socre-item").html(parsedJson.data.score);
					setCookie("score" + random, parsedJson.data.score);
					//				document.cookie="socre="+parsedJson.data.score;
					
					//				var btnArray = ['返回', '继续兑换'];
					//				mui.confirm('您使用' + parsedJson.data.score + '个积分兑换了'+_data[_index].size+'流量', '兑换成功', btnArray, function(e) {
					//					if (e.index == 1) {
					//					} else {
					//					}
					//				});
					$('.modalDialog').css('display', 'block');
				} else {
					alert(parsedJson.msg);
				}
				$('.loading-view').css('display', 'none');
			},
			error: function(data) {
				$('.loading-view').css('display', 'none');

				alert("服务器异常");
			}
		});
	} else {
		alert("请选择套餐类型");
	}
}

function backClick() {
	//	$('.modalDialog').css('display', 'none');
	history.go(-1);
}

function continueExchangeClick() {
	$('.modalDialog').css('display', 'none');
}