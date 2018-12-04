function scrollTxt(){
    var controls={},
        values={},
        t1=500, /*播放动画的时间*/
        t2=3000, /*播放时间间隔*/
        si;
    controls.rollWrap=$("#demo");
    controls.rollWrapUl=controls.rollWrap.children();
    controls.rollWrapLIs=controls.rollWrapUl.children();
    values.liNums=controls.rollWrapLIs.length;
    values.liHeight=controls.rollWrapLIs.eq(0).height();
    values.ulHeight=controls.rollWrap.height();
    this.init=function(){
        autoPlay();
        pausePlay();
    }
    /*滚动*/
    function play(){
        controls.rollWrapUl.stop(true,true).animate({"margin-top" : "-"+values.liHeight}, t1, function(){
            $(this).css("margin-top" , "0").children().eq(0).appendTo($(this));
        });
    }
    /*自动滚动*/
    function autoPlay(){
        /*如果所有li标签的高度和大于.roll-wrap的高度则滚动*/
        if(values.liHeight*values.liNums > values.ulHeight){
            si=setInterval(function(){
                play();
            },t2);
        }
    }
    /*鼠标经过ul时暂停滚动*/
    function pausePlay(){
        controls.rollWrapUl.on({
            "mouseenter":function(){
                clearInterval(si);
            },
            "mouseleave":function(){
                autoPlay();
            }
        });
    }
}
$(function(){
  new scrollTxt().init();
})