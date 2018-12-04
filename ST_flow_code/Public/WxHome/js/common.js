$(document).ready(function() {
	var rem=parseInt($("html").css("font-size"));
	var bannerH = parseInt($(document).outerWidth()) * 0.621;
	$(".banner").height(bannerH);
	
	
	/*时间选择input类型转换*/
	$(".time_select").click(function(e){
		e.stopPropagation();
		$(".mask").show();
		$(document).on("touchmove",function(e){
			e.preventDefault();
		});
	});
	$(".mask").click(function(e){
		e.stopPropagation();
	});
	$(".close").click(function(e){
		maskHide();
	})
	$(".assign").click(function(){
		maskHide();
	})
	function maskHide(e){
		$(".mask").hide();
		$(document).unbind("touchmove");
	}
	
	$(document).click(function(){
		$(".mask").hide();
	});
	
	
});