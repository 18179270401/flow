function scrollTxt(){
    var controls={},
        values={},
        t1=500, /*���Ŷ�����ʱ��*/
        t2=3000, /*����ʱ����*/
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
    /*����*/
    function play(){
        controls.rollWrapUl.stop(true,true).animate({"margin-top" : "-"+values.liHeight}, t1, function(){
            $(this).css("margin-top" , "0").children().eq(0).appendTo($(this));
        });
    }
    /*�Զ�����*/
    function autoPlay(){
        /*�������li��ǩ�ĸ߶Ⱥʹ���.roll-wrap�ĸ߶������*/
        if(values.liHeight*values.liNums > values.ulHeight){
            si=setInterval(function(){
                play();
            },t2);
        }
    }
    /*��꾭��ulʱ��ͣ����*/
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