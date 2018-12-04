var url = $("#url").val();
var media = document.getElementById("shakeMusic");

window.onload=function(){
	var phone = $("#phone").val();
	$("#phoneNumber").html(phone);
	shake();
};
//加载摇一摇动画方法
function shake(){
	$(function(){
        var SHAKE_THRESHOLD = 1500;
        var last_update = 0;
        var x = y = z = last_x = last_y = last_z = 0;
        var isjoin = "1"; 
        var ROCK = {
            init:function(){
                ROCK.activity_page_main();
            },
            //摇一摇脚本模块
            activity_page_main :function(){
                if (window.DeviceMotionEvent) {
                    window.addEventListener('devicemotion', ROCK.deviceMotionHandler, false);
                } else {
                    alert('暂不支持');
                }
                $('.shake').on('click',function(){
                	isjoin = $('#activity_page_main').attr('isjoin');
                    if(isjoin == '1') {
                        ROCK.doResult();
                        $('#activity_page_main').attr('isjoin','0');
//                      isjoin = false;
//                      $('#activity_page_main').attr('isjoin',isjoin);
                    }                    
                });
            },
            doResult:function(){
                isjoin = $('#activity_page_main').attr('isjoin');
                //限定只能摇一次
                document.getElementById("shake_div").className = "shake_div active";
                setTimeout(function(){						
                    document.getElementById("shake_div").className = "shake_div";
                    
					getFlowSize();
                }, 1300);
            },
            deviceMotionHandler:function(eventData){
                var acceleration = eventData.accelerationIncludingGravity;
                var curTime = new Date().getTime();
                if ((curTime - last_update) > 100) {
                    var diffTime = curTime - last_update;
                    last_update = curTime;
                    x = acceleration.x;
                    y = acceleration.y;
                    z = acceleration.z;
                    var speed = Math.abs(x + y + z - last_x - last_y - last_z) / diffTime * 10000;
                    var status = document.getElementById("status");
                    if (speed > SHAKE_THRESHOLD) {
                    	media.play();
                        $('.shake').trigger('click');
                    }
                    last_x = x;
                    last_y = y;
                    last_z = z;
                }
            }
        }
        ROCK.init();
    });
    //请求获得流量大小
    function getFlowSize(){
    	var phone = $("#phone").val();
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
				//alert("repData = " + repData);
//				$('#mod_shake').show();
//				$('#activity_page_main').addClass('el-fadeoutT');
//				$('#activity_page_main').hide();
//				$('#coloured_ribbon').hide();
				var jsonRepData = jQuery.parseJSON(repData);
				$('#activity_page_main').attr('isjoin','1');
                if(jsonRepData.msg=="yw"){
                    window.location="/index.php/Activity/Index/active_result/user_id/"+user_id+"/user_type/"+user_type+"/aid/"+activity_id +"/mod/"+mod+"/func/"+func+"/user_activity_id/"+user_activity_id;
                    return;
                }
				if(jsonRepData.data.size == undefined){
                    $("#form1").submit();
				}else{
//					if(confirm(jsonRepData.msg)){
					   $("#flowSize").val(jsonRepData.data.size);
                       $("#orderid").val(jsonRepData.data.orderID);
					   $("#form1").submit();
//                  }
				}
				return false;
			},
		});
    }
}