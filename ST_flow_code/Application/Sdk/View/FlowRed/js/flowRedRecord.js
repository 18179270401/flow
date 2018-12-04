//跳转到流量详情
function getFlowRedDetail(orderId) {
	window.location.href="/index.php/Sdk/FlowRed/red_code/red_order_id/" + orderId;
}

//动态生成table_cell
function createTableItem() {
	$('#flow-red-record-list').append(
		'<li class=\"mui-table-view-cell\" onclick=\"getFlowRedDetail()\">'+
		'<div class=\"mui-table\">'+
		'<div class=\"mui-table-cell mui-col-xs-5\">'+
		'<h4 class=\"mui-ellipsis\">13970919324的流量红包</h4>'+
		'<p class=\"mui-h6 mui-ellipsis\">2016-03-25 19:05:31</p>'+
		'</div>'+
		'<div class=\"mui-table-cell mui-col-xs-2 mui-text-right\">'+
		'<h4 class=\"mui-ellipsis\">¥3.00</h4>'+
		'<p class=\"mui-h6 mui-ellipsis\">0/1</p>'+
		'</div>'+
		'</div></li><li class=\"mui-table-view-divider\" ></li>'
	);
}

//$(document).ready(function(){
//	for (var i=0;i<10;i++) {
// 	createTableItem();
// }
//});

window.onload=function(){
	$('#share_bg').height($(window).height());
}