$(document).ready(function(){
	$(".recharge_type button").click(function(){
		$(this).addClass("active").siblings().removeClass("active");
	});
	/*$(".recharge_list li").click(function(){
		$(this).addClass("active").siblings().removeClass("active");
	});*/
});



!function(n){function t(){var n=document.documentElement.clientWidth||document.body.clientWidth;e.style.fontSize=n/18.75+"px"}var e=document.querySelector("html");t(),n.onresize=function(){t()}}(window);