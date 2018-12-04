//动态生成table_cell
function createTableItem() {
	$('#flow-red-record-detail').append(
		'<li class=\"mui-table-view-cell\">'+
		'<div class=\"mui-table\">'+
		'<div class=\"mui-table-cell mui-col-xs-4\">'+
		'<h4 class=\"mui-ellipsis\">未有人领取</h4>'+
		'<p class=\"mui-h6 mui-ellipsis\">2016-03-25 19:05:31</p>'+
		'</div>'+
		'<div class=\"mui-table-cell mui-col-xs-5 mui-text-right\">'+
		'<h4 class=\"mui-ellipsis\">中国移动10M流量包</h4>'+
		'</div>'+
		'</div></li><li class=\"mui-table-view-divider\" ></li>'
	);
}

//$(document).ready(function(){
//	for (var i=0;i<3;i++) {
// 		createTableItem();
// }
//});

function showShareBg() {
	$('#share_bg').show();
	document.html.style.backgroundColor="#D84E43";
}

function hidenShareBg(){
	$('#share_bg').hide();
	document.html.style.backgroundColor="#fffaf5";
}
