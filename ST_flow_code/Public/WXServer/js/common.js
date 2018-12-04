$(document).ready(function() {
	var rem=parseInt($("html").css("font-size"));
	var bannerH = parseInt($(document).outerWidth()) * 0.621;
	$(".banner").height(bannerH);
});
