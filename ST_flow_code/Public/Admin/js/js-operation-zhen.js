$(function(){
//查看场景信息   
$(".marketing_show_btn").on('click',function(){
        var activity_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/Marketing/show?activity_id='+activity_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'场景详细',
                    area: ['600px', '380px'], //宽高
                    content: $('#marketing_show_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });

//增加场景

    $(".marketing_add_btn").on('click',function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/Marketing/add',function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'新增场景',
                    area: ['600px', '400px'], //宽高
                    content: $('#marketing_add_box',parent.document),
                    success:function(){
                        inputFocus("marketing_add_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(){
                         if(!checkform('marketing_add_form')){
                             return false;
                         }
                        var url = '/index.php/Admin/Marketing/insert';
                        var data = $("form[name='marketing_add_form']",parent.document).serialize();
                        var fun = function(data){
                            alertbox(data);
                            if(data.status == 'success') {
                                parent.layer.closeAll();
                                location.reload();
                            }
                        }
                        $.post(url,data,fun,'json');
                    }
                });
            }
        });
    });
//修改场景

 $(".marketing_edit_btn").on('click',function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/Marketing/edit?activity_id='+$(this).val(),function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'编辑场景',
                    area: ['600px', '400px'], //宽高
                    content: $('#marketing_edit_box',parent.document),  //用于获取编辑的弹框和页面
                    success:function(){
                       inputFocus("marketing_edit_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(){

                        if(!checkform('marketing_edit_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/Marketing/update';
                        var data = $("form[name='marketing_edit_form']",parent.document).serialize();
                        var fun = function(data){
                            alertbox(data);
                            if(data.status == 'success') {
                                parent.layer.closeAll();
                                location.reload();
                            }
                        }
                        $.post(url,data,fun,'json');
                    }
                });
            }
        });
    });

})
  
function province_city(){
    var all_citys = $("#select_all_citys",parent.document).html();
    var select_city_detail = $("#select_city_detail",parent.document).html();
    all_citys = jQuery.parseJSON(all_citys);
    var province_id = $("#province_id",parent.document).val();
    $("select[name='city_id'] option",top.document).remove();
    var title = '<option value="">请选择市</option>';
    $("select[name='city_id']",top.document).append(title) ;
    if(province_id!=""){
        var html = '';
        for(var i=0;i<all_citys.length;i++){
            val = all_citys[i];
            var the_pid = val['province_id'];
            var city_id = val['city_id'];
            var city_name = val['city_name'];
            if(province_id == the_pid){
                if(select_city_detail == city_id || select_city_detail == city_name){
                    html += '<option value="'+city_id+'" selected>'+city_name+'</option>';
                }else{
                    html += '<option value="'+city_id+'">'+city_name+'</option>';
                }
                
            }
        }

        $("select[name='city_id']",top.document).append(html) ;
    }
}