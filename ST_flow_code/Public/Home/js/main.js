var vm = new Vue({
  el: '#example',
  data: {
    message: ''
  },
  // 在 `methods` 对象中定义方法
  methods: {
    greet: function () {
      //alert(this.message)
      // 方法内 `this` 指向 vm
      // alert('Hello ' + this.name + '!')
      // `event` 是原生 DOM 事件
      // alert(event.target.tagName)
      getRandomFlowAJAX();
    }
  }
})

//随机领取手机流量
function getRandomFlowAJAX(){
	
	var phoneValue = $("#txtPhoneView").val();
	if(phoneValue != ""){
		$.ajax({
			type:"post",
			url:"http://120.26.126.20:81/index.php/Home/Api/flow_recharge",
			data:{phone:phoneValue},
			success:function(repData){
				window.location.href="http://120.26.126.20:81/index.php/Home/Api/demo?msg="+repData.msg;
			},
		});
	}else{
		alert("手机号码不能为空");
	}
}
