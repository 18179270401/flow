
function onShareFlowRed() {
	$('#share_bg').show();
	document.html.style.backgroundColor="#D84E43";
}

function hidenShareBg(){
	$('#share_bg').hide();
	document.html.style.backgroundColor="#fffaf5";
}


//获取流量数据s
window.onload = function(){
		var nonceStr=$("#nonceStr").val();
		var signature=$("#signature").val();
		var APPID=$("#APPID").val();
		var timestamp=$("#timestamp").val();
		var localimgUrl=$("#localimgUrl").val();
		var FlowProductTitle=$("#FlowProductTitle").val();
		var FlowProductdesc=$("#FlowProductdesc").val();
		var Link=$("#Link").val();
		//var FlowProductTitle = "大转盘－流量活动";
		//var FlowProductdesc = "参加每天大转盘，赢海量奖品";
		//var Link = "http://sdk.liuliang.net.cn/index.php/Sdk/RedFlow/index/red_order_id/1/wx_openid/123456/red_order_code/1462688995500234800";
		//var localimgUrl = "http://crm.eoc.cn/resources/crm/upload/public/2016/02/07/9516d2fa-7c3a-4f9c-9ba6-1ad53ed76952.jpg";
		if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') == false ) {  
			setShareInfo({
				title:FlowProductTitle,
				summary:FlowProductdesc,
				pic:localimgUrl,
				url:Link,
				WXconfig:       {
					swapTitleInWX: true,
					appId: APPID,
					timestamp: timestamp,
					nonceStr: nonceStr,
					signature: signature
				}
			});
    	}    

		wx.config({
		   debug: false,//调式模式，设置为ture后会直接在网页上弹出调试信息，用于排查问题
		   appId: APPID,
		   timestamp:timestamp,
		   nonceStr: nonceStr,
		   signature: signature,
		   jsApiList: [  //需要使用的网页服务接口
		       'checkJsApi',  //判断当前客户端版本是否支持指定JS接口
		       'onMenuShareTimeline',//分享到朋友圈
		       'onMenuShareAppMessage',  //分享给好友
		       'onMenuShareQQ',  //分享到QQ
		       'onMenuShareWeibo' //分享到微博
		   ]
		 });
		wx.ready(function(){
			//好友
			wx.onMenuShareAppMessage({
				title:FlowProductTitle,
				desc:FlowProductdesc,
				link: Link,
				imgUrl:localimgUrl,
				success: function (res) {
				},
				cancel: function (res) {
				}
			});
			//朋友圈
			wx.onMenuShareTimeline({
								title: FlowProductTitle,
            						link: Link,
            						imgUrl: localimgUrl,
								success: function(){
								},
								cancel: function(){
								}
			});		
			//分享到QQ
			wx.onMenuShareQQ({
						title: FlowProductTitle,
   						desc: FlowProductdesc,
      					link: Link,
      					imgUrl: localimgUrl,
						success: function(){
						},
						cancel: function(){
						}			
			});	
		});
}