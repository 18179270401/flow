window.onload=function(){
	getCurrentDate();
}

function getCurrentDate(){
	var currentDate = new Date();
	var year = currentDate.getFullYear();
	var month = currentDate.getMonth()+1;
	var day = currentDate.getDay()+1;
	
	var date = year + "年" + month + "月" + day + "日";
	$("#orderDate").html(date);
}

$("paymentButton").click(function(){
	
	$("#flowOrderForm").submit();
});
