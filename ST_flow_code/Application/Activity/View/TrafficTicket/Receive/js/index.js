/**
 * Created by yyq on 16/7/18.
 */

var award = $("#mui-table-view");
htmlFontSize();
window.onresize = function() {
    htmlFontSize();
}
function htmlFontSize() {
    var w = document.documentElement.clientWidth;
    var i = (parseInt(w)-15.0/375.0*parseInt(w)) / 768.0;
    document.documentElement.style.fontSize = (i) * 40.0 +"px";
}

// 获取获奖用户
var aid=$("#aid").val();
var user_type=$("#user_type").val();
var user_id=$("#user_id").val();
$.ajax({
    type: "POST",
    url: '/index.php/Activity/Public/flowvalue_users/',
    data:{aid:aid,user_type:user_type,user_id:user_id},
    dataType: 'json',
    contentType: "application/x-www-form-urlencoded",
    success: function(repData) {
        // alert(JSON.stringify(repData));

        $.each(repData.data, function(index, element) {
            createTableItem(element);
            //console.log(index + "" + element.mobile);
        });
    },
    error:function(){
    }
});

//轮播
function loop() {
    var items = award.find('.mui-table-view-cell');
    if (items.length > 3) {
        items.eq(0).addClass('move');
        setTimeout(function() {
            items.eq(0).appendTo(award).removeClass('move');
            loop();
        }, 2000);
    }
}

//用户名称过长，缩略显示
function fixWx_name(wx_name) {
    if(wx_name==null)
    {
        return '';
    }
    if (wx_name.length > 6){
        return wx_name.substring(0,4)+"...";
    }else{
        return wx_name;
    }
}

//电话号码模糊处理
function formatPhone(phone) {
    return phone.replace(/(\d{3})\d{4}(\d{4})/, "$1····$2");
}

//动态生成table_cell
function createTableItem(element) {

    var str = element.order_date;
    var wx_photo = element.wx_photo; 
    var product_name =element.product_name;

    if(wx_photo==null)
    {
       wx_photo = ''; 
    }
    else
    {
       wx_photo = element.wx_photo;
    }
    if(product_name==null)
    {
        product_name = '';
    }
    else{
        product_name ='抢到' +element.product_name;
    }
    var Name = element.wx_name;
    if(Name ==null)
    {
        Name = '';
    }
    else{
        Name = fixWx_name(element.wx_name);
    }

        //  alert(str);
      $('#mui-table-view').append(
        '<li class="mui-table-view-cell mui-media" style="height: 3rem;padding: 0;margin: 0rem 1rem;background:none">' +
        '<img class="mui-media-object mui-pull-left" id="ImgHide" src=\"'+wx_photo+'\" style="width: 2rem !important;height: 2rem !important;max-width:2rem !important;padding: 0rem;margin: 0.5rem;">' +
        '<div class="mui-media-body" style="color:#ba3732;display:inline-block;font-size:0.7rem;margin-top:0.5rem;">' +
        Name +
        ' <p class="mui-ellipsis" style="font-size:0.7rem;margin-top:0.3rem;color: #860600">' 
        + formatPhone(element.mobile) +
        '</p>' +
        '</div>' +
        ' <div class="mui-media-right" style="font-size:0.7rem;margin-top:0.5rem;color: #ba3732">' +
        ' ' + product_name+
        '<p class="mui-ellipsis" style="text-align:right;font-size:0.7rem;margin-top:0.3rem;color: #860600">' +
         str.substring(5,str.length-3) +
        '</p>' +
        '</div>' +
        '</li>'
    );
    loop();
}