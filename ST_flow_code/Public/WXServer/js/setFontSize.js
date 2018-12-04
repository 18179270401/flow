//初始化rem
(function(){
    var getStyle = (function(){
        if(window.currentStyle){
            return function(obj, name){
                return obj.currentStyle[name];
            }
        }
        else{
            return function(obj, name){
                return getComputedStyle(obj, false)[name];
            }
        }
    })();

    // 给目标元素添加监听事件，兼容ie
    var addEvent = (function(){
        if(document.attachEvent){
            return function(obj, ev, fn){
                obj.attachEvent('on'+ev, fn);
            };
        }
        else{
            return function(obj, ev, fn){
                obj.addEventListener(ev, fn, false);
            };
        }
    })();

    var oHtml = document.querySelector('html');
    var fontSize = getStyle(oHtml, 'fontSize').split('px')[0];
    var originalWidth = 320;

    setFontSize();

    addEvent(window, 'resize', function(){
        setFontSize();
    });

    function setFontSize() {
        var size = size || 200;
        var _fontSize = (getSize()['width'] * fontSize / 320)>size?size:(getSize()['width'] * fontSize / 320);
        oHtml.style.fontSize = _fontSize + "px";
    }

    function getSize() {
        return {
            width :document.documentElement.clientWidth || document.body.clientWidth,
            height :document.documentElement.clientHeight || document.body.clientHeight
        }
    }
})();
