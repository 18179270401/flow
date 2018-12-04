function get_users_by_channel(id){
    var url = '/index.php/Admin/ChannelUser/ajax_get_users_by_channel?channel_id='+id;
    var data = {};
    var fun = function(data){

        if(data.status == 'success'){

            if(data.msg != ''){
                //alertbox(data);
                layer.alert('<i class="confirm_icon"></i>'+data.msg,{title:'提示信息'});
            }

            var have_html = '';
            for(var i=0;i < data.data.have.length; i++ ){
                if(!data.data.have[i]["user_name"]){
                    data.data.have[i]["user_name"] = '';
                }
                var html = '<tr><td><label class="checkbox checked" value="'
                    +data.data.have[i]["user_code"]+
                    '"><em></em></label></td><td>'
                    +data.data.have[i]['sort_no']+
                    '</td><td>'
                    +data.data.have[i]["user_code"]+
                    '</td><td>'
                    +data.data.have[i]["user_name"]+
                    '</td><td>'
                    +data.data.have[i]["user_type"]+
                    '</td></tr>';

                have_html = have_html + html;
            }

            $("tbody.have_list").html('').append(have_html);
            if(!have_html){
                $("tbody.have_list").parents('table').find('.allcheck').removeClass('checked');
            }else{
                $("tbody.have_list").parents('table').find('.allcheck').addClass('checked');
            }

            if(data.data.have_all == 1){
                $(".set_channel_info_allot_user.delete").show();
                $(".set_channel_info_allot_user.add").hide();
            }else{
                $(".set_channel_info_allot_user.delete").hide();
                $(".set_channel_info_allot_user.add").show();
            }

        }else{
            alertbox(data);
        }


    }
    $.get(url,data,fun,'json');
}

$(document).on('click','.seach_relate_checkbox',function(){
    var name = $(this).attr('data');
    var listname = $("input[name='"+name+"']").attr('data');
    var value = $("input[name='"+name+"']").val();
    if(!value){
        $("tbody."+listname+' tr').show();
    }else{
        $("tbody."+listname+' tr').hide();
        var num = $(this).attr('value');
        value = value.split(',');

        for(var i = 0; i< num.length;i++){
            var x = parseInt(num[i]);
            if(x){
                $("tbody."+listname+' tr').each(function(i,e){
                    $(e).children('td').eq(0).children('label').removeClass('checked');
                    for(var j=0 ; j< value.length ; j++){
                        var tem_value = value[j];
                        var val = $(e).children('td').eq(x).html().indexOf(tem_value);
                        if( val >= 0){
                            $(e).show();
                            break;
                        }
                    }
                })
            }
        }
    }
})


//已完成充值记录导出Excel
$(document).on('click','.recharge_record_selected_txt_btn',function(){
    var channel_id = $(this).attr('value');
    var  load = parent.layer.load(0, {shade: [0.3,'#000']});
    $("#layerdivid",parent.document).load('/index.php/Admin/RechargeRecord/export_excel_selected',function(data){
        parent.layer.close(load);
        if(is_layer(data)) {
            parent.layer.open({
                type: 1,
                title:'TXT字段选择',
                area: ['500px', '300px'], //宽高
                content: $('#recharge_record_selected_excel_box',parent.document),
                success:function(){
                    inputFocus("recharge_record_selected_excel_form");
                },
                btn:['保存', '取消'],
                yes: function(){
                    if(!checkform('recharge_record_selected_excel_form')){
                        return false;
                    }

                    /*获取已选择的字段*/
                    var field_str = '';
                    $('.explode_excel_add_selected',parent.document).find('label.checked').each(function(){
                        field_str += $(this).attr('value') + ',';
                    });
                    field_str = field_str.substring(0,field_str.length-1);

                    if(!field_str){
                        var data=new Array()
                        data.status = 0;
                        data.info = '请选择字段！';

                        alertbox(data);
                        return　false;
                    }

                    url='/index.php/Admin/RechargeRecord/export_txt'+"/mathran/"+Math.random();

                    var index_url = $("form[name='excel']").attr('action');
                    $("form[name='excel']").attr('action',url);
                    /*添加选择的元素*/
                    var input_str = '<input type="hidden" id="field_ids" name="field_ids" value="'+field_str+'"/>';
                    $("form[name='excel']").append(input_str);
                    $("form[name='excel']").submit();
                    $('#field_ids').remove();
                    $("form[name='excel']").attr('action',index_url);
                    top.layer.close(load);
                    parent.layer.closeAll();

                }
            });
        }
    });
});

/*提交中订单信息*/
$(document).on('click','.OrderSubmitting_view_btn',function(){
    var order_id = $(this).attr('value');
    var  load = parent.layer.load(0, {shade: [0.3,'#000']});
    var enterprise_id = $(this).attr('value');
    var title = '提交中订单信息';
    var area =  ['600px', '450px'];
    var view_name = 'order_view_box';
    var view_url = '/index.php/Admin/OrderSubmitting/show?order_id='+order_id;
    view_order(title,area,view_name,view_url);
});