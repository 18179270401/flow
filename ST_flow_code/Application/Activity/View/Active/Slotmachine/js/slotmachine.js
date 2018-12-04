var phone = document.getElementById("phone").value;
var isBegin = false; //标识符是否在转
var showPhone = phone.substr(0, 3) + ' ' + phone.substr(3, 4) + ' ' + phone.substr(7, 4);
document.getElementById("phoneNumber").innerHTML = showPhone;

function loadBackground() {
    var w = document.documentElement.clientWidth;
    var i = parseInt(w) / 768;
    var u = 3.325 * i * 40.0;
    var result = 0000; //numRand();
    var arr = jQuery.makeArray($(".num"))
    arr.reverse();
    var num_arr = (result + '').split('');
    $(arr).each(function(index) {
        var _num = $(this);
        _num.css('backgroundPositionY', (u * 60) - (u * num_arr[3 - index]));
    });
}

$(function() {
    $('.btn').click(function() {
        if (isBegin) return false;
        isBegin = true;
        requestServer(phone);
    });
});

function rotate(result, orderid, msg) {
    var w = document.documentElement.clientWidth;
    var i = parseInt(w) / 768 * 40;
    var u = 3.333 * i;
    $(".num").css('backgroundPositionY', 0); //y轴复位
    // console.log('摇奖结果 = '+result);
    var num_arr = (result + '').split('');
    // 处理数字不足四位
    if (num_arr.length < 1) {
        num_arr.unshift('0');
    }
    if (num_arr.length < 2) {
        num_arr.unshift('0');
    }
    if (num_arr.length < 3) {
        num_arr.unshift('0');
    }
    if (num_arr.length < 4) {
        num_arr.unshift('0');
    }
    // 翻转数组，先显示个位的抽奖结果
    var arr = jQuery.makeArray($(".num"))
    arr.reverse();
    $(arr).each(function(index) {
        var _num = $(this);
        setTimeout(function() {
            _num.animate({
                // backgroundPositionY: (u*60) - (u*num_arr[3 - index])
                backgroundPositionY: (u * 10) - (u * num_arr[3 - index])
            }, {
                duration: 3000 + index * 500,
                easing: "easeInOutCirc",
                complete: function() {
                    if (index == 3) {
                        isBegin = false;
                        if (result != 0) {
                            $("#flowSize").val(result);
                            $("#orderid").val(orderid);
                            $("#form1").submit();
                        } else {
                            $("#flowSize").val(result);
                            $("#orderid").val(orderid);
                            $("#form1").submit();
                        }
                    }
                }
            });
        }, (index) * 300);
    });
}

function requestServer(phone) {
    
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
        type: "POST",
        url: "/index.php/Activity/Public/index",
        data: {
                    phone: phone,activity_id:activity_id,openid:openid,user_type:user_type,user_id:user_id,headimgurl:headimgurl,nickname:nickname,user_activity_id:user_activity_id
                },
        contentType: "application/x-www-form-urlencoded",
        success: function(repData) {
            var jsonRepData = jQuery.parseJSON(repData);
            if(jsonRepData.msg=="yw"){
               window.location="/index.php/Activity/Index/active_result/user_id/"+user_id+"/user_type/"+user_type+"/aid/"+activity_id +"/mod/"+mod+"/func/"+func+"/user_activity_id/"+user_activity_id;
                    return;
            }
            console.log("流量：" + repData.data);
            if (jsonRepData.data.size == undefined) {
                rotate(0, 0, repData.msg);
            } else {
                rotate(jsonRepData.data.size, jsonRepData.data.orderID, jsonRepData.msg);
            }
        },
    });
}
