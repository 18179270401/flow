	var flows = jsonData.data;
	var isDuringAnimation = false;
	var isEndRoate = false;
	var _jsonRepData;
	var phone = $("#phone").val();
	if (phone != null && phone.length > 0) {
		var showPhone = phone.substr(0, 3) + ' ' + phone.substr(3, 4) + ' ' + phone.substr(7, 4);
		$('#phoneNumber').html(showPhone);
	}
	
	
	var user_activity_id=$("#user_activity_id").val();
	var activity_id=$("#activity_id").val();
	var openid=$("#openid").val();
	var user_type=$("#user_type").val();
	var user_id=$("#user_id").val();
	var headimgurl=$("#headimgurl").val();
	var nickname=$("#nickname").val();
	$("#lotteryBtn").rotate({
		angle: 180,
		bind: {
			click: function() {
				if (isDuringAnimation) {
					return;
				}
				isEndRoate = false;
				isDuringAnimation = true;
				$.ajax({
					type: "POST",
					url: "/index.php/Activity/Public/index",
					data: {
						phone: phone,activity_id:activity_id,openid:openid,user_type:user_type,user_id:user_id,nickname:nickname,headimgurl:headimgurl,user_activity_id:user_activity_id
					},
					contentType: "application/x-www-form-urlencoded",
					success: function(repData) {
						try {
							isEndRoate = true;
							_jsonRepData = repData;
						} catch (e) {
							isDuringAnimation = false;
						}
	
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						isDuringAnimation = false;
					},
				});
				rotateFunction();
			}
		}
	
	});
	
var timeOut = function() { //超时函数
	$("#rotate-bgId").rotate({
		angle: 0,
		duration: 10000,
		animateTo: 2160, //这里是设置请求超时后返回的角度，所以应该还是回到最原始的位置，2160是因为我要让它转6圈，就是360*6得来的
		callback: function() {
			alert('网络超时')
		}
	});
};

function rotateFunction() {
	$('#rotate-bgId').stopRotate();
	$("#rotate-bgId").rotate({
		angle: 0,
		duration: 1000,
		animateTo: 360, //angle是图片上各奖项对应的角度，1440是我要让指针旋转4圈。所以最后的结束的角度就是这样子^^
		callback: function() {
			if (isEndRoate) {
				rotateFunc();
				isEndRoate = false;
			} else {
				rotateFunction();
			}
		},
		easing: function(x, t, b, c, d) {
			return (t / d) * c * 4;
		}
	});
}
var rotateFunc = function() { //awards:奖项，angle:奖项对应的角度
	var jsonRepData = jQuery.parseJSON(_jsonRepData);
	var size = 0;
	var orderID = "";
	
	var activity_id=$("#activity_id").val();
	var user_type=$("#user_type").val();
	var user_id=$("#user_id").val();
    var mod=$("#mod").val();
    var func=$("#func").val();
	var user_activity_id=$("#user_activity_id").val();
	//alert(jsonRepData.msg);
	if(jsonRepData.msg=="yw"){
			window.location="/index.php/Activity/Index/active_result/user_id/"+user_id+"/user_type/"+user_type+"/aid/"+activity_id +"/mod/"+mod+"/func/"+func+"/user_activity_id/"+user_activity_id;
                    return;
    }
	if (jsonRepData.data.size == undefined) {
		size = 0;
	} else {
		size = parseInt(jsonRepData.data.size)
	}
	if (jsonRepData.data.orderID == undefined) {
		orderID = "";
	} else {
		orderID = jsonRepData.data.orderID;
	}
	var roundTo = new Array();
	for (var i = 0; i < flows.length; i++) {
		if ((flows[i] + '') == (size + '')) {
			roundTo.push(i);
		}
	}
	var finalIndex = roundTo[Math.floor(Math.random() * roundTo.length)];
	var activity_id = $("#activity_id").val();
	var openid = $("#openid").val();

	var awards = 0;
	var angle = finalIndex * 360.0 / flows.length + 360.0 / flows.length / 2;
	var text = jsonRepData.msg;
	$('#rotate-bgId').stopRotate();
	$("#rotate-bgId").rotate({
		angle: 0,
		duration: 10000,
		animateTo: -angle - 1440 * 4 -180, //angle是图片上各奖项对应的角度，1440是我要让指针旋转4圈。所以最后的结束的角度就是这样子^^
		callback: function() {
			//				arotateFunctionlert(text);
//			if (confirm(text)) {
				//按确认后做什么
				$("#flowSize").val(size);
				$("#orderid").val(orderID);
				$("#form1").submit();
//			}

			isDuringAnimation = false;

			//			setTimeout(function() {
			//				window.location.href = "../../../trailer/trailerRedTraffic/html/flow_get_success.html?msg=" + escape(text) + "&flowSize=" + size;
			//			isDuringAnimation = false;
			//			}, 1000);
		},
		easing: function(x, t, b, c, d) {
			return -c * ((t = t / d - 1) * t * t * t - 1) + b;
		}
	});
};

function wrapText(context, text, x, y, maxWidth, lineHeight) {
	var words = text.split(' ');
	var line = '';

	for (var n = 0; n < words.length; n++) {
		if (line.length >=2) {
			//设置字体样式
			context.font = "bold 40px Arial";
		} else {
			context.font = "40px Courier New";
		}

		var testLine = line + words[n] + '';
		var lineMetrics = context.measureText(line);
		var lineWidth = lineMetrics.width;

		var metrics = context.measureText(testLine);
		var testWidth = metrics.width;
		if (testWidth > maxWidth && n > 0) {

			context.fillText(line, x - lineWidth / 2, y);
			line = words[n] + '';
			y += lineHeight;
		} else {
			line = testLine;
		}
	}
	if (line.length >= 2) {
		//设置字体样式
		context.font = "bold 40px Arial";
	} else {
		context.font = "40px Courier New";
	}
	var lineMetrics = context.measureText(line);
	var lineWidth = lineMetrics.width;

	context.fillText(line, x - lineWidth / 2, y);
}

//扇形
function draw(ctx, x, y, radius, sDeg, eDeg, showText) {

	var angle = 90 * Math.PI / 180;
	// 初始保存
	ctx.save();
	// 位移到目标点
	ctx.translate(x, y);

	ctx.beginPath();
	// 画出圆弧
	ctx.arc(0, 0, radius, sDeg + angle, eDeg + angle);
	// 再次保存以备旋转
	ctx.save();
	// 旋转至起始角度

	ctx.rotate(eDeg);

	//	ctx.moveTo(radius, 0);
	// 连接到圆心
	ctx.lineTo(0, 0);
	// 还原
	ctx.restore();
	// 旋转至起点角度
	ctx.rotate(sDeg + angle);
	// 从圆心连接到起点
	ctx.lineTo(radius, 0);
	ctx.closePath();
	// 还原到最初保存的状态
	ctx.restore();
	ctx.fill();
	ctx.stroke();

	ctx.save();
	ctx.translate(x, y);
	ctx.rotate((sDeg + angle + eDeg + angle) / 2);

	//设置字体填充颜色
	ctx.fillStyle = "#9D4E00";
	//从坐标点(50,50)开始绘制文字
	//ctx.fillText(showText, 146, 16);
	ctx.translate(280, 0);
	ctx.rotate(Math.PI / 2);
	wrapText(ctx, showText, 0, 0, 16, 40);

	// 移动到终点，准备连接终点与圆心

	ctx.restore();

	return ctx;
}

function ctxDraw() {

	var ctx = document.getElementById('rotate-bgId').getContext('2d');
	ctx.clearRect(0, 0, parseFloat(ctx.width), parseFloat(ctx.height));
	//	draw(ctx, 100, 100, 50, 0, Math.PI / 180 * 230).fill();
	ctx.strokeStyle = "#9c4EE0";
	var deg = Math.PI / 180;
	for (var i = 0; i < flows.length; i++) {
		var flow = flows[i];
		var angle = 360.0 / flows.length;
		if (i % 2 == 0) {
			ctx.fillStyle = "#ffea73";
		} else {
			ctx.fillStyle = "#ffcf55";
		}

		var zeroFlows = ['谢 谢 参 与', '不 要 灰 心'];
		var showText;
		if (flow == 0) {
			showText = zeroFlows[Math.floor(Math.random() * 2)];
		} else {
			if(flow>=1024)
			{
				var flowG = Math.floor(flow/1024);
				showText = flowG + 'G 流 量 ';

			}
			else
			{
			showText = flow + 'M 流 量 ';
			}
		}

		draw(ctx, 400, 400, 172.5 * 2, i * angle * deg, (i + 1) * angle * deg, showText);
	}
}
ctxDraw();