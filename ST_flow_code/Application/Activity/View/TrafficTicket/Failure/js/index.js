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