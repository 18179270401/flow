//轮播
function loop() {
	var items = award.find('.li-view');
	if (items.length > 3) {
		items.eq(0).addClass('move');
		setTimeout(function() {
			items.eq(0).appendTo(award).removeClass('move');
			loop();
		}, 2000);
	}
};

//电话号码模糊处理
function formatPhone(phone) {
	return phone.replace(/(\d{3})\d{4}(\d{4})/, "$1****$2");
}

// 获取获奖用户
var aid=$("#aid").val();
var user_type=$("#user_type").val();
var user_id=$("#user_id").val();
$.ajax({
	type: "POST",
 	url: '/index.php/Activity/Public/flowvalue_users/',
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
//	alert(element.wx_photo);
//	alert(element.mobile);
//	alert(element.order_date);
//	alert(element.product_name);
//	alert(element.wx_name);
	var str = element.order_date;
	$('#ui-view').append(
//		'<li class=\"mui-table-view-cell mui-media\" style=\"height: 4rem;\">' +
//		'<img class=\"mui-media-object mui-pull-left\" src=\"'+element.wx_photo+'\" style=\"border-radius:0.3rem;\">' +
//		'<div class=\"mui-media-body\" style=\"color:white;display:inline-block;font-size:0.8rem\">' +fixWx_name(element.wx_name)+
//		'<p class=\"mui-ellipsis\" style=\"font-size:0.8rem;margin-top:0.2rem;\">' + formatPhone(element.mobile) +
//		'</p>' + '</div>' + '<div class=\"mui-media-right\" style=\"font-size:0.8rem;\">' +
//		'抢到' + element.product_name + '<p class=\"mui-ellipsis\" style=\"text-align:right;font-size:0.6rem;margin-top:0.2rem;\">' + str.substring(5,str.length-3) +
//		'</p>' + '</div>' + '</li>'
		'<li class="li-view"><div id="li-phone"><p>'+formatPhone(element.mobile)+'</p></div><div class="li-right"><p>抢到'+element.product_name+'流量</p><p><span>'+str.substring(5,str.length-3)+'</span></p></div></li>'
	);
	loop();
}

var timer;
function setTheStatus() {
	switch (flowticket_status){
		case 0:
			//默认状态
			document.getElementById('btn-exchenge-rate').className = "yes-btn";
			document.getElementById('btn-exchenge-rate').disabled= false;
			timer = setInterval(showCountDown, 1000);
			break;
		case 1:
			//已兑换

			document.getElementById("juan-status").innerHTML = "流量劵已兑换";
			document.getElementById('btn-exchenge-rate').className = "no-btn";
			document.getElementById('btn-exchenge-rate').disabled= true;
			clearInterval(timer);
			break;
		case 2:
			//已过期
			document.getElementById("juan-status").innerHTML = "流量劵已过期";
			document.getElementById('btn-exchenge-rate').className = "no-btn";
			document.getElementById('btn-exchenge-rate').disabled= true;
			clearInterval(timer);
			break;
		case 3:
			//已失效
			document.getElementById("juan-status").innerHTML = "流量劵已失效";
			document.getElementById('btn-exchenge-rate').className = "no-btn";
			document.getElementById('btn-exchenge-rate').disabled= true;
			clearInterval(timer);
			break;
		default:
			break;
	}
}


function showCountDown() {
  var t = end_time - new Date().getTime();
  var d = 0;
  var h = 0;
  var m = 0;
  var s = 0;
  if (t >= 0) {
    // d = Math.floor(t / 1000 / 60 / 60 / 24);
    // h = Math.floor(t / 1000 / 60 / 60 % 24);
    // m = Math.floor(t / 1000 / 60 % 60);
    // s = Math.floor(t / 1000 % 60);
    d = Math.floor(t / 1000 / 60 / 60 / 24);
    h = Math.floor(t / 1000 / 60 / 60);
    m = Math.floor(t / 1000 / 60 % 60);
    s = Math.floor(t / 1000 % 60);
  }
  console.log(h + " " + m + " " + s);
  document.getElementById("juan-status").innerHTML = "流量劵过期倒计时：";
  if(h > 0) {
  	$('#juan-status').append(h+"时");
  	$('#juan-status').append(m+"分");
  	$('#juan-status').append(s+"秒");
  } else if(h == 0 && m > 0) {
  	$('#juan-status').append(m+"分");
  	$('#juan-status').append(s+"秒");
  } else{
  	$('#juan-status').append(s+"秒");
  }
  
}
