	var award = $("#mui-table-view");


//电话号码模糊处理
function formatPhone(phone) {
	return phone.replace(/(\d{3})\d{4}(\d{4})/, "$1****$2");
}

//动态生成table_cell
function createTableItem(element) {
	var str = element.order_date;
	$('#mui-table-view').append(
		
		'<li class=\"mui-table-view-cell mui-media\" style=\"height: 4rem;width:20rem;\">' +
		'<img class=\"mui-media-object mui-pull-left\" src=\"'+element.wx_photo+'\" style=\"border-radius:0.3rem;width: 2.8rem;max-width:2.8rem;height:2.8rem\">' +
		'<div class=\"mui-media-body\" style=\"color:white;display:inline-block;font-size:0.8rem\">' +element.wx_name+
		'<p class=\"mui-ellipsis\" style=\"font-size:0.8rem;margin-top:0.2rem;\">' + formatPhone(element.mobile) +
		'</p>' + '</div>' + '<div class=\"mui-media-right\" style=\"font-size:0.8rem;\">' +
		'抢到' + element.product_name + '<p class=\"mui-ellipsis\" style=\"text-align:right;font-size:0.6rem;margin-top:0.2rem;\">' + str.substring(5,str.length-3) +
		'</p>' + '</div>' + '</li>'
	);
	loop();
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
}

for(var i = 0;i< result.length;i++)
{
	var item = result[i];
	var element = {};
	element.order_date = item.receive_date;
	element.wx_photo = item.wx_photo;
	element.wx_name = item.wx_name;
	element.mobile = item.mobile;
	element.product_name = item.product_name;
	
	createTableItem(element);
}