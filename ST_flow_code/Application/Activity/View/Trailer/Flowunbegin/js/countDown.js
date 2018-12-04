var dateDiv = document.getElementById("countDown");
// var time = "2016/07/2 16:34:00";
var time=$("#start_date").val();
var EndTime = new Date(time);

setInterval(showCountDown, 1000);
function showCountDown() {
  var NowTime = new Date();
  var t = EndTime.getTime() - NowTime.getTime();
  var d = 0;
  var h = 0;
  var m = 0;
  var s = 0;
  if (t >= 0) {
    // d = Math.floor(t / 1000 / 60 / 60 / 24);
    // h = Math.floor(t / 1000 / 60 / 60 % 24);
    // m = Math.floor(t / 1000 / 60 % 60);
    // s = Math.floor(t / 1000 % 60);
    d = Math.floor(t / 1000 / 60 / 60 / 24);
    h = Math.floor(t / 1000 / 60 / 60);
    m = Math.floor(t / 1000 / 60 % 60);
    s = Math.floor(t / 1000 % 60);
  }
  console.log(h + " " + m + " " + s);
  if(h>99){
    document.getElementById("t_h0").innerHTML = 9;
    document.getElementById("t_h1").innerHTML = 9;
  }else{
    document.getElementById("t_h0").innerHTML = splitNum(h)[0];
    document.getElementById("t_h1").innerHTML = splitNum(h)[1];
  }
  document.getElementById("t_m0").innerHTML = splitNum(m)[0];
  document.getElementById("t_m1").innerHTML = splitNum(m)[1];
  document.getElementById("t_s0").innerHTML = splitNum(s)[0];
  document.getElementById("t_s1").innerHTML = splitNum(s)[1];
}

function splitNum(num) {
  var arr = [];
  if (num < 10) {
    arr[0] = 0;
    arr[1] = num;
  } else {
    num = "" + num;
    arr = num.split("");
  }
  return arr;
}

/*获取某个时间格式的时间戳*/
function getTimestampToStr(strTime) {
  var timestamp = Date.parse(new Date(strTime));
  timestamp = timestamp / 1000;
  return timestamp;
}

/*得到当前时间戳*/
function getNowTimestamp() {
  return Date.parse(new Date());
}
