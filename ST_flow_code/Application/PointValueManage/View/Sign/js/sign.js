var score_remark = '今日签到';
var isSign;
/**
 * 今日签到
 */
function dailySign() {
	sign();
}

//签到
function sign() {
	$('.modelClass').css('display', 'block');
	$.ajax({
		type:"POST",
		url:"/index.php/PointValueManage/Sign/dailyOfSign",
		data:{
			 openid:openid,userid:userid,user_type:user_type,score_remark:score_remark
		},
		contentType:"application/x-www-form-urlencoded",
		success: function(repData){
//			alert(JSON.stringify(repData));
			$('.modelClass').css('display', 'none');

			if(repData.status == 1) {
				signInSuccess();
//				alert(repData.data.score);
				setCookie("score"+random, repData.data.score);
			} else {
				signInFailure(repData.msg);
			}
		},
		error: function(data) {
			$('.modelClass').css('display', 'none');
		},
	});
}


/**
 * 签到成功
 */
function signInSuccess() {
	var btnArray = ['兑换积分', '查看积分'];
	mui.confirm('恭喜您获得' + daily_score + '个积分', '签到成功', btnArray, function(e) {
		if (e.index == 1) {
			window.history.back(-1); 
		} else {
			window.location.href = "../../../index.php/PointValueManage/Api/exchange?openid="+openid+"&usertype=" + user_type + "&userid=" + userid+"&random="+random;

		}
	});
}

/**
 * 签到失败
 */
function signInFailure($msg) {
	var btnArray = ['兑换积分', '查看积分'];
	mui.confirm($msg, '签到失败', btnArray, function(e) {
		if (e.index == 1) {
			window.history.back(-1); 
		} else {
			window.location.href = "../../../index.php/PointValueManage/Api/exchange?openid="+openid+"&usertype=" + user_type + "&userid=" + userid+"&random="+random;
		}
	});
}
