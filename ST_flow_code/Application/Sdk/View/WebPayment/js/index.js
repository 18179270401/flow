var _data = undefined;
$("body").on("propertychange input", "input", function() {
	testPhoneNumber($('.text').val());
});

testPhoneNumber($('.text').val());

function phoneMobile(phone, callback) {
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
		},
		error: function(data) {}
	});
}

function testPhoneNumber(inputText) {
	$('.table').html('');
	if(!/^(13[0-9]|14[0-9]|15[0-9]|18[0-9]|17[0,0-9])\d{8}$/i.test(inputText)) {
		$('.radio').css('display', 'none');
	} else {
		phoneMobile(inputText, function callback(data) {
			$('.radio').css('display', 'inline-block');

			var parsedJson = eval('(' + data + ')');

			if(parsedJson.msg == 1001) {
				var title = "对不起，只允许" + parsedJson.data.province + "内" + parsedJson.data.operator + "号码充值!";
				$('.radio em').text(title);
				return;
			}

			$('.radio em').text(parsedJson.data.belong);
			_data = parsedJson;
			showAllPacket();
		});
	}
}

function BCratButton(item, i) {
	$('.table').append('<li>\
            <h3>全国流量包</h3>\
            <span>'+item.size+'</span>\
            <p>'+item.price_discount+'元全国流量</p>\
            <a class="prepaid_btn" style="cursor:pointer;">立即充值</a>\
        </li>');
}
function itemClick()
{
	var user_type = $("#user_type").val();
	var user_id = $("#user_id").val();
	var pid = $(this)[0].item.id;
	var phone = $('.text').val();
	var url = "/index.php/Sdk/WebPayment/paytype?pid=" + pid + "&phone=" + phone + "&user_type=" + user_type + "&user_id=" + user_id;
	location.href = url;
}

function showAllPacket() {
	$('.table').html('');
	$.each(_data.data.Type, function(i, item) {
		$.each(item.packet, function(j, item1) {
			BCratButton(item1, j);
			$('.table li .prepaid_btn').eq(j)[0].item = item1;
		});
	});
	$('.table li .prepaid_btn').click(itemClick);
}