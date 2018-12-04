// JavaScript Document
$(function(){
	$(window).ready(function() {
		loginWindow();
		$(window).resize(function(){
			loginWindow();
		})
        $(".text").focus(function(){
		  $(this).parent().addClass("focus");	
		})
		$(".text").blur(function(){
		  $(this).parent().removeClass("focus");	
		})
    });
  })
  function loginWindow(){
	var winH=$(window).height(); 
	var $loginHeaderH=$(".login_header").outerHeight(); 
	var $loginBodyH=$(".login_body").outerHeight();
	var $loginFooterH=$(".login_footer").outerHeight();
	var $h=parseInt($loginHeaderH)+parseInt($loginBodyH)+parseInt($loginFooterH);
	var $top=(parseInt(winH)-$h)/2-10;
	$(".login_header").css("margin-top",$top);
  }