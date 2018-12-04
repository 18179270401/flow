


function doLogin(){
   	//alert('g');
	$('#redbg_view').removeClass('pt-page-moveFromTop');
	$('#updiv_view').removeClass('pt-page-moveToTopFade hide');
		
		//锁消失的场景
	$('#Getred').addClass('pt-page-rotateSlideOut');
		//rotateYDIV();
		
	$('#updiv_view').addClass('pt-page-moveToTopFade');
	$('#redbg_view').addClass('pt-page-moveToBottomEasing');
	setTimeout(function(){
			$('#Getred').css('display','none');
	},500);	
		
   	setTimeout(function(){
		
		createTableItem();
		createTableItem();
		createTableItem();
		setTimeout(function(){
			$('#updiv_view').removeClass('pt-page-moveIconUp');
			$('#redbg_view').removeClass('pt-page-moveToBottomEasing');
			
			$('#updiv_view').css('display','none');
			$('#redbg_view').css('display','none');
			//$('#Success_view').addClass('pt-page-rotateRightSideFirst');
		},1250);
	},200);
}	
var x,y,ny=0,rotYINT
function rotateYDIV()
{
	y=document.getElementById("Getred")
	clearInterval(rotYINT)
	rotYINT=setInterval(1)
}

function startYRotate()
{
	ny=ny+1
	y.style.transform="rotateY(" + ny + "deg)"
	y.style.webkitTransform="rotateY(" + ny + "deg)"
	y.style.OTransform="rotateY(" + ny + "deg)"
	y.style.MozTransform="rotateY(" + ny + "deg)"
	if (ny==180 || ny>=360)
	{
		clearInterval(rotYINT)
		if (ny>=360){ny=0}
	}
}

//动态生成table_cell
function createTableItem() {
	$('#mui-table-view').append(
		'<li class=\"mui-table-view-cell mui-media\" style=\"height: 4rem;\">' +
		'<img class=\"mui-media-object mui-pull-left\" src=\"../images/shuijiao.jpg\" style=\"border-radius:0.3rem;\">' +
		'<div class=\"mui-media-body\" style=\"color:white;display:inline-block;font-size:0.8rem\">lv' +
		'<p class=\"mui-ellipsis\" style=\"font-size:0.8rem;margin-top:0.2rem;\">' + 18079104948 +
		'</p>' + '</div>' + '<div class=\"mui-media-right\" style=\"font-size:0.8rem;\">' +
		'抢到' + '5M流量红包' + '<p class=\"mui-ellipsis\" style=\"text-align:right;font-size:0.6rem;margin-top:0.2rem;\">' + '5M' +
		'</p>' + '</div>' + '</li>'
	);
}