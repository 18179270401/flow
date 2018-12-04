// JavaScript Document
$(function(){
	
	//二级菜单伸缩
	$(".navone").click(function(){
		var $navone=$(this).parent("dd").siblings().find(".navone");
		$(this).toggleClass("active").next(".navonecon").slideToggle();
		$navone.removeClass("active").next(".navonecon").slideUp();
		
	})
	
	//三级菜单伸缩
	$(".navtwo").click(function(){
		var $navtwo=$(".navtwo").not(this);
		$(this).toggleClass("active");
		if($(this).next().is(".navtwocon")){
			$(this).next(".navtwocon").slideToggle();
		}else{
			$(this).addClass("active");
		}
		$navtwo.removeClass("active").next(".navtwocon").slideUp();
	})
	
	//三级菜单选中效果
	$(".navtwocon li").click(function(){
		$(".navtwocon li").not(this).removeClass("active");
		$(this).addClass("active");
	})
	// tab切换
	$(".tab_hd h3").click(function(){
		var $index=$(this).index();
		$(this).addClass("active").siblings().removeClass("active");
		$(this).parent().next(".tab_bd").children().eq($index).show().siblings().hide();
	})
	
	//菜单伸缩
	$("#menuslide").click(function(){
		var $logo=$(".header .logo");
		var $navbox=$(".navbox");
		var $mainbox=$(".main_box");
		var $left=$navbox.css("left");
		if(parseInt($left)==0){
			$logo.stop(true,true).animate({width:"0px"},300);
			$navbox.stop(true,true).animate({left:"-216px"},300);
			$mainbox.stop(true,true).animate({left:"0px"},300);
		}else{
			$logo.stop(true,true).animate({width:"216px"},300);
			$navbox.stop(true,true).animate({left:"0px"},300);
			$mainbox.stop(true,true).animate({left:"216px"},300);
		}
		
	})
	//省份选择
	$(".discountset_operator .province_btn").click(function(event){
		event.stopPropagation();
		var $top=parseInt($(this).offset().top)+30;
		var $left=$(this).offset().left;
		$("#province_box").css({position:"fixed",top:$top,left:$left,zIndex:2000}).show();
	})
	
	//地区选择
	$(".saleset_add").click(function(event){
		event.stopPropagation();
		var $contentW=$(this).parents(".saleset_con").width();
		var $left=$(this).offset().left;
		var $top=$(this).offset().top;
		$(".saleset_add").siblings("dl").hide();
		alert(parseInt($contentW)-parseInt($left));
		if(parseInt($contentW)-parseInt($left)<472){
		   $(this).siblings("dl").css({left:parseInt($contentW)-parseInt($left)-472})
		}else{
		   $(this).siblings("dl").css({left:0})
		}
		$(this).siblings("dl").show();
	})
	$(document).click(function(){
	    $(".saleset_nav").find("dl").hide();
	});
	$(".saleset_nav dl dd a").click(function(){
		var $value=$(this).text();
		var $str='<li><span class="saleset_name">'+$value+'</span><input type="text" class="inputtext" /><a href="javascript:" class="saleset_btn">x</a></li>';
		$(this).parents("li").before($str);
    })
	
	//提醒
	$("#tips").click(function(event){
		event.stopPropagation();
		$(this).children(".tipcon").show();
	})
	$(document).click(function(){
	    $("#tips",top.document).children(".tipcon").hide();
	});
	
	//更多菜单点击效果
	$(".moreperation .moreperationbtn").click(function(){
		$(this).siblings(".moreperation_con").show().parents("td").addClass("overflowautoy");
		$(this).parents(".moreperation").mouseleave(function(){
			$(this).find(".moreperation_con").hide();
			$(this).parents("td").removeClass("overflowautoy")
		})
	})
	//省份选择
	$(".region_down").click(function(event){
	event.stopPropagation();
	$(this).next(".region_box").show();
	})
	$(document).click(function(){
		$(".region_box").hide();
	})
	$(".region_con li").click(function(event){
		var $text=$(this).text();
		$(this).parents(".region_box").prev(".region_down").find("span").text($text);
	})
	
	$(".navbox .home_pannel").click(function(){
		$(this).find("a").removeClass("collapsed");
		$(".panel_con .panel_tit").find("a").addClass("collapsed");
	})
	$(".panel_con .panel_tit").click(function(){
		$(".home_pannel").find("a").addClass("collapsed");
	})
	
	//二级菜单点击
	$(".panel_body li").click(function(){
	  $(".home_pannel").find("a").addClass("collapsed");
	  $(".panel_body li").removeClass("current");
	  $(this).addClass("current").parents(".panel_body").siblings(".panel_tit").find("a").removeClass("collapsed");	
	})
	
	//首页单点击
	$(".home_pannel").click(function(){
		$(this).find("a").removeClass("collapsed");
		$(".panel_tit").find("a").addClass("collapsed");
		$(".panel_body li").removeClass("current");
	})
	
	//登录输入框获得焦点
	$(".logintext .text").focus(function(){
		$(this).parents(".logintext").addClass("focus");
	})
	
	//登录输入框失去焦点
	$(".logintext .text").blur(function(){
		$(this).parents(".logintext").removeClass("focus");
	})
	
	
	//多选
	$(".checkbox").click(function(){
		$(this).toggleClass("checked");
	})
	
	//时间控件
	$(".start_datetime").datetimepicker({ //开始时间
		language:  'zh-TW',
		format: "yyyy-mm-dd hh:ii",
		autoclose: true
	}).on("click",function(ev){
		$(".start_datetime").datetimepicker("setEndDate", $(".end_datetime").val());
	});
	$(".end_datetime").datetimepicker({ //结束时间
		language:  'zh-TW',
		format: "yyyy-mm-dd hh:ii",
		autoclose: true
	}).on("click", function (ev) {
		$(".end_datetime").datetimepicker("setStartDate", $(".start_datetime").val());
	});
	
	// 下拉列表控件触发
	$(".select").select2();
		
	tablelistboxHeight();
	tableSize();
	$(window).resize(function(){
		loginHeight();
        tablelistboxHeight();
		tableSize();
    });
	$(".tablelist_tbody").scroll(function(){
		//$(".tablelist_thead").scrollLeft($(this).scrollLeft());
		$(".tablelist_thead").scrollLeft($(this).scrollLeft());
	})
	
	//横向滚动条
	//$(".mCustomScrollbar_x").mCustomScrollbar({
//		axis:"x",
//		autoHideScrollbar:true,
//		theme:"3d-thick",
//		scrollInertia:0,
//	});
	
	//纵向滚动条
	$(".mCustomScrollbar_y").mCustomScrollbar({
       axis:"y", // horizontal scrollbar
	   scrollbarPosition:"outside",
	   autoHideScrollbar:true,
	   scrollInertia:0,
    });
	
	//$(".mCustomScrollbar_xy").mCustomScrollbar({
//       axis:"yx", // horizontal scrollbar
//	   scrollbarPosition:"outside",
//	   autoHideScrollbar:true,
//	   scrollInertia:0,
//    });
	
	//$(".tablelist_con").mCustomScrollbar({
//       axis:"yx", // horizontal scrollbar
//	   scrollInertia:0,
//	   autoHideScrollbar:true,
//	    callbacks:{
//		   whileScrolling: function(){
//			   if(this.mcs.direction == "y"){
//				    var $h=-parseInt(this.mcs.top);
//				   $(".tablelist_thead").css({top:$h})
//			   }
//		   } ,
//		onUpdate: function(){  
//			   setTimeout(function(){
//				   var $top=$("#mCSB_1_container").css("top");
//			       var $headtop =- parseInt($top);
//				   $(".tablelist_thead").css("top",$headtop);
//				},50);
//		   }  
//	   }, 
//    });
	
	//提示小工具
	$('[data-toggle="tooltip"]').tooltip({
		trigger:'hover',
		});
	
	//首页图表下拉列表点击效果
	$(".chart_tit .dropmenu li").click(function(){
		var $text = $(this).text();
		$(this).addClass("current").siblings().removeClass("current");
		$(this).parent(".dropmenu").siblings(".droptit").find(".text").text($text);
	})
	
	//文字无间断滚动代码，兼容IE、Firefox、Opera
	if(document.getElementById("demo")){
		var scrollup = new ScrollText("demo");
		scrollup.LineHeight = 34;        //单排文字滚动的高度
		scrollup.Amount = 1;            //注意:子模块(LineHeight)一定要能整除Amount.
		scrollup.Delay = 10;           //延时
		scrollup.Start();             //文字自动滚动
		scrollup.Direction = "up";   //默认设置为文字向上滚动
	}
	
});

//alert提示信息
function alertFade(obj){
	$("#"+obj).fadeIn(300);
	var t=setTimeout("alertHide("+obj+")",2000);
}
//alert信息隐藏
function alertHide(obj){
	$(obj).fadeOut(300);
}

//表单区域高度计算函数
function tablelistboxHeight(){
	var $searchboxH=$(".search_box").outerHeight();
	var $totalH=parseInt($searchboxH)+47+52;
	$(".tablelistboxH").css("height","calc(100% - "+$totalH+"px)");
}

// 通用列表宽度计算
//function tableSize()
//{
//   var tablelistW=$(".tablelist_tbody table").width();
//   var $w=parseInt(tablelistW)-20-2;
//   var $totalWidth=0;
//   var $changeW=0;
//   var $unchangeW=0;
//   $(".tablelist_thead th").each(function(i) {
//	 var $thWidth=parseInt($(this).attr("width"));
//	 $totalWidth=$totalWidth+$thWidth;
//	 if($(this).is(".change")){
//		 $(this).css("width",$thWidth);
//		 $(".tablelist_tbody tr:first td").eq(i).css("width",$thWidth);
//		 $changeW=$changeW+$thWidth;
//	 }
//	 else{
//		 $(this).css("width",$thWidth);
//		 $(".tablelist_tbody tr:first td").eq(i).css("width",$thWidth);
//		 $unchangeW=$unchangeW+$thWidth;
//	 }
//  });
//  if($w>$totalWidth)
//  {
//	  var $surplusW=$w-$unchangeW;
//	 $(".tablelist_thead th").each(function(i) {
//		 var $thWidth=parseInt($(this).attr("width"));
//		 if($(this).is(".change"))
//		 {
//			var $thW=($thWidth/$changeW)*$surplusW;
//			$(this).css("width",$thW);
//			$(".tablelist_tbody tr:first td").eq(i).css("width",$thW);
//		}
//		else
//		{
//			$(this).css("width",$thWidth);
//			$(".tablelist_tbody tr:first td").eq(i).css("width",$thW);
//		}
//	 })
//  }
//} 

// 通用列表宽度计算
function tableSize()
{
   var tablelistW=$(".tablelist_tbody table").innerWidth();
   var $w=parseInt(tablelistW)-20-2;
   var $totalWidth=0;
   var $changeW=0;
   var $unchangeW=0;
   var $theadW=0;
   $(".tablelist_thead th").each(function(i) {
	 var $thWidth=parseInt($(this).attr("width"));
	 $totalWidth=$totalWidth+$thWidth;
	 if($(this).is(".change")){
		 $(this).css("width",$thWidth);
		 $(".tablelist_tbody tr:first td").eq(i).css("width",$thWidth);
		 $changeW=$changeW+$thWidth;
	 }
	 else{
		 $(this).css("width",$thWidth);
		 $(".tablelist_tbody tr:first td").eq(i).css("width",$thWidth);
		 $unchangeW=$unchangeW+$thWidth;
	 }
  });
  if($w>$totalWidth)
  {
	  var $surplusW=$w-$unchangeW;
	 $(".tablelist_thead th").each(function(i) {
		 var $thWidth=parseInt($(this).attr("width"));
		 if($(this).is(".change"))
		 {
			var $thW=($thWidth/$changeW)*$surplusW;
			$(this).css("width",$thW);
			$(".tablelist_tbody tr:first td").eq(i).css("width",$thW);
		}
		else
		{
			$(this).css("width",$thWidth);
			$(".tablelist_tbody tr:first td").eq(i).css("width",$thW);
		}
	 }) 
  }
  var $tablebodyw=$(".tablelist_tbody").outerWidth();
  $theadW=$tablebodyw;
  if($(".tablelist_tbody").get(0)){
	  var $innerW=$(".tablelist_tbody").get(0).clientWidth;
	  var $outerW=$(".tablelist_tbody").get(0).offsetWidth;
	  var $sw=parseInt($outerW)-parseInt($innerW);
	  if($sw>0){
	    $(".tablelist_thead").width($theadW-$sw);
	  }else{
		 $(".tablelist_thead").width("100%"); 
	  }
  }

} 

//弹出框第一个输入框获得焦点
function inputFocus(formname){
	$("form[name='"+formname+"']",top.document).find("input[type='text']").first().focus();
}

