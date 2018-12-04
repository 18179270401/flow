/**
 * Created by Yeoman on 2017/2/15.
 */
/*以iPhone6的屏幕宽度为基础，1rem=20px*/
/*1rem小于12px时会失效，因为在谷歌浏览器中为了客户体验，默认最小的字体是12px*/
(function (win) {
    var oHtml=document.querySelector('html');//获取html标签
    setRem();
    win.onresize=function () {
        setRem();
    };
    function setRem() {
        var winW=document.documentElement.clientWidth||document.body.clientWidth;
        oHtml.style.fontSize=winW/18.75+"px";
    };
})(window);