var isDuringAnimation = false;
var phone = $("#phone").val();
var url = $("#url").val();
var scratchNumber;
var dataInfo;
var user_activity_id=$("#user_activity_id").val();
var activity_id=$("#activity_id").val();
var openid=$("#openid").val();
var user_type=$("#user_type").val();
var user_id=$("#user_id").val();
var headimgurl=$("#headimgurl").val();
var nickname=$("#nickname").val();
var mod=$("#mod").val();
var func=$("#func").val();
$.ajax({
		type:"POST",
		url:"/index.php/Activity/Public/index",
		data:{
                    phone: phone,activity_id:activity_id,openid:openid,user_type:user_type,user_id:user_id,headimgurl:headimgurl,nickname:nickname,user_activity_id:user_activity_id
                },
		contentType:"application/x-www-form-urlencoded",
		success: function(repData){
//			alert("repData = " + repData);
			var jsonRepData = jQuery.parseJSON(repData);
			var data = 0;
			dataInfo = jsonRepData;
			scratchNumber = jsonRepData.data.size;

			if(jsonRepData.msg=="yw"){
        		window.location="/index.php/Activity/Index/active_result/user_id/"+user_id+"/user_type/"+user_type+"/aid/"+activity_id +"/mod/"+mod+"/func/"+func+"/user_activity_id/"+user_activity_id;
                    return;
    		}
			if (typeof(scratchNumber)!="undefined" && scratchNumber!=0)  {
				
				if(scratchNumber >= 1024) {
					$('#gua_span').text(scratchNumber/1024+"G流量");
				} else {
					$('#gua_span').text(scratchNumber+"M流量");
				}
			} else {
				scratchNumber = 0;
				$('#gua_span').text("谢谢参与");
			}
		},
	});