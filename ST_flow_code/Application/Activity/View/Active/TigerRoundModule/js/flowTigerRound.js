var flows = jsonData.data;
var click=false;
var flowListMap;

var lottery={
	index:-1,	//当前转动到哪个位置，起点位置
	count:0,	//总共有多少个位置
	timer:0,	//setTimeout的ID，用clearTimeout清除
	speed:20,	//初始转动速度
	times:0,	//转动次数
	cycle:50,	//转动基本次数：即至少需要转动多少次再进入抽奖环节
	prize:-1,	//中奖位置
	flowSize:0,	//获取到的流量值
	rewardMsg:"",//获取流量内容描述
	init:function(id){
		if ($("#"+id).find(".lottery-unit").length>0) {
			$lottery = $("#"+id);
			$units = $lottery.find(".lottery-unit");
			this.obj = $lottery;
			this.count = $units.length;
			$lottery.find(".lottery-unit-"+this.index).addClass("active");
			//$(".lottery-unit-" + this.index).addClass("active");
		};
	},
	roll:function(){
		var index = this.index;
		var count = this.count;
		var lottery = this.obj;
		//alert($(".rewardBg").eq(index));
		//$(".rewardBg").eq(index).removeClass("active");
		//$(".rewardBg").eq(index).html("adfdfd");
		$(lottery).find(".lottery-unit-"+index).removeClass("active");
		index += 1;
		if (index>count-1) {
			index = 0;
		};
		//$(".rewardBg").eq(index).addClass("active");
		$(lottery).find(".lottery-unit-"+index).addClass("active");
		this.index=index;
		return false;
	},
	stop:function(index){
		this.prize=index;
		return false;
	}
};

function roll(){
	lottery.times += 1;
	lottery.roll();
	if (lottery.times > lottery.cycle+10 && lottery.prize==lottery.index) {
		clearTimeout(lottery.timer);
		lottery.prize=-1;
		lottery.times=0;
		click=false;
		
		setTimeout(function(){
//			if(confirm(lottery.rewardMsg)){
				$("#flowSize").val(lottery.flowSize);
				$("#orderid").val(lottery.orderid);
			   	$("#form1").submit();
//			}
		}, 1);
		
	}else{
		if (lottery.times<lottery.cycle) {
			lottery.speed -= 10;
		}else if(lottery.times==lottery.cycle) {
			//var index = Math.random()*(lottery.count)|0;
			//lottery.prize = index;		
		}else{
			if (lottery.times > lottery.cycle+10 && ((lottery.prize==0 && lottery.index==7) || lottery.prize==lottery.index+1)) {
				lottery.speed += 110;
			}else{
				lottery.speed += 20;
			}
		}
		if (lottery.speed<40) {
			lottery.speed=40;
		};
		console.log(lottery.times+'^^^^^^'+lottery.speed+'^^^^^^^'+lottery.prize);
		lottery.timer = setTimeout(roll,lottery.speed);
	}
	return false;
}

window.onload=function(){
	lottery.init('lottery');
	//alert(flows);
	flowListMap = new HashMap();
	drawTigerRound();
	//var phoneNumber = getUrlParam("phone");
	var phoneNumber = $("#phone").val();
	$("#phoneNumber").html(phoneNumber);
};

//动态获取活动流量产品包，更新UI, 缓存数据
function drawTigerRound(){
	$.each(flows, function(index, item){
				
		//alert("item " + index + " " + item);
		$(".flowSizeIndex-" + index).html(item >= 1024 ? item / 1024 : item);
		$(".flowSizeUnit-" + index).html(item >= 1024 ? "G流量" : "M流量");
		flowListMap.put(item, index);
	});
}

//点击开始抽奖按钮事件
$("#btnStartId").click(function(){

	if(click){
		return false;
	}else{
		click=true;
		//var phoneNumber = getUrlParam("phone");
		var phoneNumber = $("#phone").val();
		var activity_id=$("#activity_id").val();
		var user_activity_id=$("#user_activity_id").val();
		var openid=$("#openid").val();
		var user_type=$("#user_type").val();
        var user_id=$("#user_id").val();
        var headimgurl=$("#headimgurl").val();
		var nickname=$("#nickname").val();
		var mod=$("#mod").val();
		var func=$("#func").val();
		//alert("phoneNumber = " + phoneNumber);
		var tenMArrIndex = new Array(0, 7);
		var hundredMArrIndex = new Array(5, 10);
		var zeroMArrIndex = new Array(1, 2, 4, 6, 9, 11);
		$.ajax({
			type:"POST",
			url:"/index.php/Activity/Public/index",
			data:{
                    phone: phoneNumber,activity_id:activity_id,openid:openid,user_type:user_type,user_id:user_id,headimgurl:headimgurl,nickname:nickname,user_activity_id:user_activity_id
                },
			contentType:"application/x-www-form-urlencoded",
			success: function(repData){
				//alert("repData = " + repData);
				var jsonRepData = jQuery.parseJSON(repData);
				if(jsonRepData.msg=="yw"){
				window.location="/index.php/Activity/Index/active_result/user_id/"+user_id+"/user_type/"+user_type+"/aid/"+activity_id +"/mod/"+mod+"/func/"+func+"/user_activity_id/"+user_activity_id;
                    return;
   		 	}
				//alert("size = " + jsonRepData.data.size);
				if(jsonRepData.data.size == undefined){
					lottery.flowSize = 0;
					lottery.orderID="";
				}else{
					lottery.flowSize = jsonRepData.data.size;
					lottery.orderid =jsonRepData.data.orderID;
				}
					
				lottery.rewardMsg = jsonRepData.msg;
				
				var random;
				switch(flowListMap.get(lottery.flowSize)){
					case 0:
						//alert("size = " + lottery.flowSize + " index = 0");
						random = Math.floor(Math.random()*2);					
						lottery.prize = tenMArrIndex[random];
						//alert("ssindex = " + lottery.prize);
						break;
					case 1:
						//alert("size = " + lottery.flowSize + " index = 1");
						random = Math.floor(Math.random()*2);
						lottery.prize = hundredMArrIndex[random];
						//alert("ssindex = " + lottery.prize);
						break;
					case 2:
						//alert("size = " + lottery.flowSize + " index = 2");
						lottery.prize = 3
						break;
					case 3:
						//alert("size = " + lottery.flowSize + " index = 3");
						lottery.prize = 8
						break;
					default:
						//alert("size = " + lottery.flowSize + " index = any");
						random = Math.floor(Math.random()*6);
						lottery.prize = zeroMArrIndex[random];
						break;
				}
				
				lottery.speed=100;
				roll();
				return false;
			},
		});
	}
	
});