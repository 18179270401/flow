//获取流量数据s
window.onload = function(){
	
	var user_type=$("#user_type").val();
	var user_id=$("#user_id").val();
	$.ajax({
		type: "POST",
		url: "/index.php/Sdk/Api/packet_all",
		contentType: "application/x-www-form-urlencoded",
		data:{
			user_id:user_id,
			user_type:user_type
		},
		success: function(parsedJson) {
			$.each(parsedJson.data, function(i, item) {
				if (item.type == "移动") {
					localStorage.setItem("ChinaMobile",JSON.stringify(item.packet));
				} else if(item.type == "联通"){
					localStorage.setItem("ChinaUnicom",JSON.stringify(item.packet));
				}
				else if(item.type == "电信"){
					localStorage.setItem("ChinaTelecomArray",JSON.stringify(item.packet));
				}
			});
		},
		error: function(data) {
		}
	});
	var packetjson = localStorage.getItem("SelectMobleFlow");
	if(packetjson)
	{
		var packetArray = JSON.parse(packetjson);
		var ChinaMobileCount = 0;
		var ChinaUnicomCount = 0;
		var ChinaTelecomArrayCount = 0;
		var TotalMoney = 0.0;
		for(var itemcount in packetArray)
		{
			var item = packetArray[itemcount];
			
			var Type = item['FlowType'];
			var price_market = item['price_market'];
			TotalMoney += price_market;
			if(Type == 1)
			{
				ChinaMobileCount++;
			}
			else if(Type == 2)
			{
				ChinaUnicomCount++;
			}
			else if(Type == 3)
			{
				ChinaTelecomArrayCount++;
			}
		}
		TotalMoney = TotalMoney.toFixed(2);
		
		$('.ChinaMobileCount').html(ChinaMobileCount +'个');
		$('.ChinaUnicomCount').html(ChinaUnicomCount+'个');
		$('.ChinaTelecomArrayCount').html(ChinaTelecomArrayCount+'个');
		//总数量
		var totalCount = ChinaMobileCount+ChinaUnicomCount+ChinaTelecomArrayCount;
		$('.TotalCount').html(totalCount+'个');
		//总价格
		$('.TotalMoney').html('¥'+TotalMoney);
	}
}

function Successinput()
{
	var packetjson = localStorage.getItem("SelectMobleFlow");
	var pay_price = 0.0;
	var packages = "";
	if(packetjson)
	{
		var Array = JSON.parse(packetjson);
		if(Array.length)
		{
			for(var i in Array)
			{
				var item = Array[i];
				var packageid = item['id'];
				var price_market = item['price_market'];
				if(!packages)
				{
					packages = packageid;
				}
				else
				{
					packages = packages +','+ packageid;	
				}
				pay_price += price_market;
			}
			
			var remark = $('#spaninput').val();
			if(remark)
			{
		   		 $("#remark").val(remark);
			}
			else
			{
				 $("#remark").val('现在流行发流量红包!');
			}
		    $("#pay_price").val(pay_price);
		  	$("#packages").val(packages);
			$("#form1").submit();
		}
		else
		{
			alert('请选择流量包!');
		}
	}
	else
	{
		alert('请选择流量包!');
	}
	
	
}
