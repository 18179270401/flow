
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
		var position=$("#position").val();
		//（1.开启，2关闭）
		//var FlowProductTitle = "大转盘－流量活动";
		//var FlowProductdesc = "参加每天大转盘，赢海量奖品";
		//var Link = "http://sdk.liuliang.net.cn/index.php/Sdk/RedFlow/index/red_order_id/1/wx_openid/123456/red_order_code/1462688995500234800";
		//var localimgUrl = "http://crm.eoc.cn/resources/crm/upload/public/2016/02/07/9516d2fa-7c3a-4f9c-9ba6-1ad53ed76952.jpg";
				

		wx.config({
		   debug: false,//调式模式，设置为ture后会直接在网页上弹出调试信息，用于排查问题
		   appId: APPID,
		   timestamp:timestamp,
		   nonceStr: nonceStr,
		   signature: signature,
		   jsApiList: [  
		   //需要使用的网页服务接口
		       'checkJsApi',
		       'openLocation',
		       'getLocation',
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
			
			//开启了地理信息hu
			//开启了地理信息获取
			localStorage.setItem("positionstatue","");
			if(position == 1)
			{

				wx.checkJsApi({
				    jsApiList: [
				        'getLocation'
				    ],
				    success: function (res) {
				        // alert(JSON.stringify(res));
				       	// alert(JSON.stringify(res.checkResult.getLocation));
				        if (res.checkResult.getLocation == false) {
				            alert('你的微信版本太低，不支持微信JS接口，请升级到最新的微信版本！');
				            return;
				        }
				    }
				});
				var user_type=$("#user_type").val();
				var user_id=$("#user_id").val();
			    var user_activity_id=$("#user_activity_id").val();
				wx.getLocation({
				  success: function (res) {
				       var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
				       var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
				       var speed = res.speed; // 速度，以米/每秒计
				       var accuracy = res.accuracy; // 位置精度
						$.ajax({
							type: "POST",
						 	url: '/index.php/Activity/Public/Comparepostion/',
						 	data:{aid:user_activity_id,user_type:user_type,user_id:user_id,latitude:latitude,longitude:longitude},
							dataType: 'json',
							contentType: "application/x-www-form-urlencoded",
							success: function(repData) {
							localStorage.setItem("positionstatue",repData.status);
								if(repData.status != "success")
								{
              					 	window.location="/index.php/Activity/Index/active_location/user_id/"+user_id+"/user_type/"+user_type+"/aid/"+activity_id +"/latitude/"+repData.data.latitude+"/longitude/"+repData.data.longitude;
								}
							},
							error:function(){
							}
						});
				   },
				   cancel: function (res) {
				       alert('对不起。只有在店的周边才可以参与活动喔！');
				   }
				});
			}
		});
		wx.error(function (res) {
			 alert(res.errorCode);  //打印错误消息。及把 debug:false,设置为debug:ture就可以直接在网页上看到弹出的错误提示
		});	
}