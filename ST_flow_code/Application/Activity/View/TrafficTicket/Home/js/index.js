/**
 * Created by yyq on 16/7/18.
 */
htmlFontSize();
window.onresize = function() {
    htmlFontSize();
}
function htmlFontSize() {
    var w = document.documentElement.clientWidth;
    var i = (parseInt(w)-15.0/375.0*parseInt(w)) / 768.0;
    document.documentElement.style.fontSize = (i) * 40.0 +"px";
//        var h = document.documentElement.clientHeight;
//        var rem = 20/parseFloat(w)*parseFloat(h);
//        alert(rem);
}

function buttonClick() {

    var phonebool = checkSubmitMobil();
    if (phonebool==1)
    {
        $("#form1").submit();//提交form1表单
        showDiv();//盖一层蒙版
    }
}

 function showDiv() {  
        document.getElementById('popWindow').style.display = 'block';  
    } 
//jquery验证手机号码
function checkSubmitMobil() {
    if ($("#mobile").val() == ""|| $("#mobile").val() == null) {
        alert("手机号码不能为空！");
        $("#mobile").focus();
        return 0;
    }
    if (!$("#mobile").val().match(/^0?(13[0-9]|15[012356789]|18[02356789]|14[57]|17[0-9])[0-9]{8}$/)) {
        alert("手机号码格式不正确！");
        $("#mobile").focus();
        return 0;
    }
    return 1;
}