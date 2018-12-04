function getSizeName(size) {
	if (size >= 1024) {
		return Math.floor(size / 1024) + "GB";
	} else {
		return size + "MB";
	}
}

function showList() {
	$('.mui-table-view').html("");
	var listdata = list.list;
	for (var i = 0; i < listdata.length; i++) {
		var item = listdata[i];

		var color;
		var msg;
		if (item.back_fail_desc == undefined) {
			item.back_fail_desc = "充值中";
			color = '#F1E612';
			msg = "充值中";
		}
		else if (parseInt(item.order_status) == 2||parseInt(item.order_status) == 5)
		{
			color = '#75E185';
			msg = "充值成功";
		}
		else if (parseInt(item.order_status) == 6)
		{
			color = '#F9646D';
			msg = "充值失败";
			if(parseInt(item.refund_status) == 2)
			{
				msg= msg+",已退"+item.exchange_score+"积分";
			}
			else
			{
				msg= msg+",正在退还"+item.exchange_score+"积分";
			}
		}

		$('.mui-table-view').append('<li class="mui-table-view-cell unselectcell"><div style="height:2.2rem;"><div class="top" style="height: 1.2rem;"><div class="telephone" style="font-size: 1.2rem;line-height: 1.2rem; height: 1.2rem;font-weight: 300; float: left;">' + item.mobile + '</div><div class="flow" style="font-size: 1.2rem; height: 1.2rem;float: right;font-weight: 300;;line-height: 1.2rem;">' + getSizeName(item.size) + '</div></div><div class="buttom" style="height: 0.8rem; margin-top:0.2rem"><div class="telephone" style="font-size: 0.6rem;line-height: 0.6rem;height:0.8rem;float: left;margin-left: 0.2rem;">' + item.order_date + '</div><div class="flow" style="font-size: 0.6rem; height: 0.8rem;line-height: 0.6rem;float: right;color: '+color+';">' + msg + '</div></div></div></li>');
	}
	//pageCount":5,"pageNum":"1"

	$('.leftButton').removeAttr("disabled"); // 移除disabled属性 
	$('.rightButton').removeAttr("disabled"); //移除disabled属性 
	if (list.pageNum == 1) {
		$('.leftButton').attr('disabled', "true");
	} 
	if (list.pageNum == list.pageCount||list.pageCount==0) {
		$('.rightButton').attr('disabled', "true");
	}
	if(list.pageCount == 0)
	{
		list.pageNum = 1;
		list.pageCount = 1;
	}
	
	$('#pageLabel').html(""+list.pageNum+"/"+list.pageCount);
}

function pageRequest(page) {
	$(".modelClass").css("display", "block");
	$.ajax({
		type: "POST",
		url: "/index.php/PointValueManage/Api/recordData",
		contentType: "application/x-www-form-urlencoded",
		data: {
			userid: userid,
			usertype: usertype,
			openid: openid,
			page: page
		},
		success: function(parsedJson) {
			if (parsedJson.status == 1) {
				list = parsedJson.data;
				showList();
				$(".modelClass").css("display", "none");
			}
		},
		error: function(data) {
			$(".modelClass").css("display", "none");
		}
	});
}

function leftClick() {
	pageRequest(parseInt(list.pageNum) - 1);
}

function rightClick() {
	pageRequest(parseInt(list.pageNum) + 1);

}

showList();