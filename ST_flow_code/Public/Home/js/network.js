var xmlHttpRequest;

function createXmlHttpRequest(){
	
	if(window.ActiveXObject){
		
		return new ActiveXObject("Microsoft.XMLHTTP");
	}else if(window.XMLHttpRequest){
		
		return new XMLHttpRequest();
	}
}

function getFlowProductList(){
	
	//1、创建XMLHttpRequest组建
	xmlHttpRequest = createXmlHttpRequest();
	
	xmlHttpRequest.onreadystatechange = flowProductCallback;
	
	xmlHttpRequest.open("GET", "http://www.w3school.com.cn/ajax/demo_get.asp". true);
	
	xmlHttpRequest.setRequestHeader("Content-Type", "application/x-www.form-urlencoded;");
	
	xmlHttpRequest.send(null);
}

function flowProductCallback(){
	
	alert("result");
	alert("readyState = " + xmlHttpRequest.readyState + " status = " + xmlHttpRequest.status);
	if(xmlHttpRequest.readyState == 4 && xmlHttpRequest.status == 200){
		var result = xmlHttpRequest.responseText;
		alert(result);
	}
}
