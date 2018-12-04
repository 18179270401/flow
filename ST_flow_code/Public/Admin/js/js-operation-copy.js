function copyabc(){
     $(".btn_copy2",parent.document).zclip({
         path:"/Public/Admin/js/ZeroClipboard.swf", //记得把ZeroClipboard.swf引入到项目中
         copy:function(){
         return $(this).attr("data-addr");
         }
     });
}
copyabc();