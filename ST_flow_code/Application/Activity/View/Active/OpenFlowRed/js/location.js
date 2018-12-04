
window.onload=function(){
	
	var nonceStr=$("#nonceStr").val();
	var signature=$("#signature").val();
	var APPID=$("#APPID").val();
	var timestamp=$("#timestamp").val();
	wx.config({
	    debug: true,
	    appId: APPID,
	    timestamp: timestamp,
	    nonceStr: nonceStr,
	    signature: signature,
	    jsApiList: [
	        // 所有要调用的 API 都要加到这个列表中
	        'checkJsApi',
	        'openLocation',
	        'getLocation'
	      ]
	});
	wx.ready(function () {
		
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
	
		wx.getLocation({
		    success: function (res) {
		        var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
		        var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
		        var speed = res.speed; // 速度，以米/每秒计
		        var accuracy = res.accuracy; // 位置精度
		    },
		    cancel: function (res) {
		        alert('用户拒绝授权获取地理位置');
		    }
		});
	});
		wx.error(function (res) {
			 alert(res);  //打印错误消息。及把 debug:false,设置为debug:ture就可以直接在网页上看到弹出的错误提示
		});	
};

