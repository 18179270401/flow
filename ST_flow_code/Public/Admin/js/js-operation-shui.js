$(function() {
    //运营商设置状态修改
    $(".operatorinfo_toggle_status_btn").on('click',function(){
        var operator_info_id = $(this).attr('value');
        var status=$(this).attr('data-original-title');
        var function_name = $(this).attr('data-value');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否'+status+'运营商设置【'+function_name+'】？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/OperatorInfo/toggle_status';
            var data ={'operator_info_id':operator_info_id};
            var fun = function(data){
                if(data.status == 'success') {
                    parent.layer.closeAll();
                    location.reload();
                }
                alertbox(data);
            }
            $.post(url,data,fun,'json');
        });
    });
    
    //号码黑名单添加
    $(".mobileblack_add_btn").on('click',function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/MobileBlack/add',function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                  type: 1,
                  title:'新增号码黑名单',
                  area: ['450px', '220px'], //宽高
                  content: $('#mobileblack_add_box',parent.document),
                  btn:['保存', '取消'],
                  yes: function(){
                    if(!checkform('mobileblack_add_form')){
                        return false;
                    }
                    var url = '/index.php/Admin/MobileBlack/insert';
                    var data = $("form[name='mobileblack_add_form']",parent.document).serialize();
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
        })
    });

    //查看号码黑名单
    $('.mobileblack_show_btn').click(function(){
        var mobile_id = $(this).attr('value');
        var load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/MobileBlack/show?mobile_id='+mobile_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'号码黑名单信息',
                    area: ['450px', '200px'], //宽高
                    content: $('#mobileblack_show_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });

    //删除号码黑名单
    $(".mobileblack_delete_btn").on('click',function() {
        var mobile_id = $(this).attr('value');
        var mobile = $(this).attr('data-name');
        parent.layer.confirm(
            '<i class="confirm_icon"></i>确定是否删除号码黑名单【'+mobile+'】？', 
            {btn: ['确定','取消']}, 
            function(){
                parent.layer.closeAll();
                var url = '/index.php/Admin/MobileBlack/delete';
                var data = {mobile_id:mobile_id};
                var fun = function(data){
                    if(data.status == 'success'){
                        location.reload();
                    }
                    alertbox(data);
                }
                $.post(url,data,fun,'json');
        });
    });

    //删除活动管理---企业端：流量场景>活动管理
    $(".scene_activity_delete_btn").on('click',function() {
        var user_activity_id = $(this).attr('value');
        var activity_name = $(this).attr('data-name');
        parent.layer.confirm(
            '<i class="confirm_icon"></i>确定是否删除活动名称【'+activity_name+'】？', 
            {btn: ['确定','取消']}, 
            function(){
                parent.layer.closeAll();
                var url = '/index.php/Admin/SceneActivity/delete';
                var data = {user_activity_id:user_activity_id};
                var fun = function(data){
                    if(data.status == 'success'){
                        location.reload();
                    }
                    alertbox(data);
                }
                $.post(url,data,fun,'json');
        });
    });

    //删除跟进记录管理
    $(".operatorrecord_delete_btn").on('click',function() {
        var record_id = $(this).attr('value');
        var operator_name = $(this).attr('data-name');
        parent.layer.confirm(
            '<i class="confirm_icon"></i>确定是否删除跟进记录【'+operator_name+'】？', 
            {btn: ['确定','取消']}, 
            function(){
                parent.layer.closeAll();
                var url = '/index.php/Admin/OperatorRecord/delete';
                var data = {record_id:record_id};
                var fun = function(data){
                    if(data.status == 'success'){
                        location.reload();
                    }
                    alertbox(data);
                }
                $.post(url,data,fun,'json');
        });
    });


    //代理商与企业名称联想返回信息
    $("div").delegate("#depart_name_shui","keyup",function(){
        objs_depart = this;
        var width = $(this).outerWidth();
        var top = $(this).offset().top+$(this).outerHeight()+1;
        var left = $(this).offset().left;

        var depart_manager_name = $(this).val();
        if(depart_manager_name != ""){
            $.post("/index.php/Admin/Depart/ajax_depart_manager_name",{depart_manager_name:depart_manager_name},function(data){
                $("#depart_manager_ids",top.document).attr("value","");  //此代码无法运行
                if(data.info){
                    var html="";
                    html+="<ul>";
                    for(var i=0; i < data.info.length;i++){
                        html+='<li onclick="depart_manager_all('+data.info[i].id+',\''+data.info[i].name+'\')">'+data.info[i].name+'</li>';
                    }
                    html+="</ul>";
                    $("#inputusernamelist").show().css("width",width+"px").css("top",top+"px").css('left',left+"px").html(html);
                }else{
                    $("#inputusernamelist").hide().html("");
                }
            },"json");
        }else{
            $("#inputusernamelist").hide().html("");
        }
    });

    //清空输入框联想内容
    $(document).click(function(){
        if ($("#depart_manager_ids",top.document).val()=="" || $("#depart_manager_ids",top.document).val()==undefined){
            $("#depart_name_shui",top.document).val("");
        }
        $("#inputusernamelist").hide();
    });
    //清空输入框联想内容
    $(document).click(function(){
        if ($("#think_one",top.document).val()=="" || $("#think_one",top.document).val()==undefined){
            $("#think_shui",top.document).val("");
        }
        $("#inputusernamelist").hide();
    });

    //清空输入框联想内容
    $(document).click(function(){
        if ($("#think_one",top.document).val()=="" || $("#think_one",top.document).val()==undefined){
            $("#think_shui2",top.document).val("");
        }
        $("#inputusernamelist").hide();
    });

})

//联想信息
var objs_think = "";
function think_all(id,name){
    var text = name;
    $(objs_think).val(text);
    $("#think_one").val(id);
}

function think_all2(id,name){
    var text = name;
    $(objs_think).val(text);
    $("#think_one").val(id);
    contact_list();
}

//获取代理商和企业选择出来名称并赋值
var objs_depart = "";
function depart_manager_all(id,name){
    var text = name;
    $(objs_depart).val(text);
    $("#depart_manager_ids").val(id);
}

function resources_type_change(){
    var resources_type= $("#resources_type").val();
    if(resources_type == "1"){
        $("#operator_info").hide();
        $("#operator_id_operate").attr("empty","true");
    }else{
        $("#operator_info").show();
        $("#operator_id_operate").attr("empty","false");
    }
}

function resources_type_change_operator_contact(){
    var resources_type = $("#resources_type",top.document).val();
    $(".operator_info_sel_all",top.document).hide();
    if(resources_type != ""){
        $(".operator_info_sel"+resources_type,top.document).show();
    }else{
        $(".operator_info_sel_all",top.document).show();
    }
}

function process_sure(content,da,applyurl){
    parent.layer.confirm('<i class="confirm_icon"></i>'+content, {
        title:"提示信息",
        btn: ['确定','取消'] //按钮
    },function(){
        var url = applyurl;
        var data = da;
        var fun = function(data){
            alertbox(data); 
            if(data.status == 'success') {
                location.reload();
            }
            channel_account_add_btn_ststus=1;
            parent.layer.closeAll();
        }
        if(channel_account_add_btn_ststus==1) {
            channel_account_add_btn_ststus = 2;
            $.post(url, data, fun, 'json');
        }
    });
}
var approve_change_data = '';
function approve_change(a){
    var obj = $('#approve_change').html();
    if(obj){
        approve_change_data = obj;
    }
    $('#approve_change').html("");
    if(a){
        $('#approve_change').html(approve_change_data);
    }
}

/****
function apply_confirm_new(content,id,applyurl){
    if(content==""){
        var contents = "现在要提交审核吗？";
    }else{
        var contents = content+"，现在要提交审核吗？";
    }
    parent.layer.confirm('<i class="confirm_icon"></i>'+contents, {
        title:"提示信息",
        btn: ['确定','取消'] //按钮
    },function(){
        var url = applyurl;
        var data ={id:id};
        var fun = function(data){
            alertbox(data);
            if(data.status == 'success') {
                parent.update_account();
                parent.layer.closeAll();
            }
            location.reload();
        }
        $.post(url,data,fun,'json');
    }, function() {
        location.reload();
    });
}
****/

function get_proxy_account(content){
    var info = eval('('+content+')');
    var id = $(objthis).attr("data-id-name");
    var proxy_id=$('#'+id).val();
    if(proxy_id){
        var url = '/index.php/Admin/ProxyTransfer/show';
        var data ={operate:'account',proxy_id:proxy_id} ;
        var fun = function(data){
            if(data.status == 'success') {
                $("#"+info.diva).css({'display':'block'});
                $("#"+info.divb).html(data.info.account_balance);
            }else{
                alertbox(data);
            }
        }
        $.post(url,data,fun,'json');
    }

}

$(function(){
    /*新增代理商资金划拨申请单*/
    $('.proxy_transfer_apply_add_btn').click(function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyTransfer/add ',function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'新增代理商资金划拨申请单',
                    area: ['405px', '380px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("proxy_transfer_apply_add_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(index, layero){
                        if(!checkform('proxy_transfer_apply_add_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/ProxyTransfer/insert';
                        var data = $("form[name='proxy_transfer_apply_add_form']",parent.document).serialize();
                        var fun = function(data){
                            if(data.status == 'success'){
                                parent.layer.closeAll();
                                apply_confirm(data.msg,data.info,"/index.php/Admin/ProxyTransfer/send");
                            }else{
                                alertbox(data);
                            }
                        }
                        $.post(url,data,fun,'json');
                    }

                });
            }
        });

    });


    /*代理商资金划拨申请单送审*/
    $('.proxy_transfer_apply_send_btn').click(function(){
        var apply_id=$(this).attr('value');
        apply_confirm('',apply_id,"/index.php/Admin/ProxyTransfer/send");
    });

    /*编辑代理商资金划拨*/
    $('.proxy_transfer_apply_edit_btn').click(function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        var  apply_id = $(this).attr('value');
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyTransfer/edit?apply_id='+apply_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'编辑代理商资金划拨申请单',
                    area: ['405px', '380px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("proxy_transfer_apply_edit_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(index, layero){
                        if(!checkform('proxy_transfer_apply_edit_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/ProxyTransfer/update';
                        var data = $("form[name='proxy_transfer_apply_edit_form']",parent.document).serialize();
                        var fun = function(data){
                            if(data.status == 'success'){
                                parent.layer.closeAll();
                                apply_confirm(data.msg,apply_id,"/index.php/Admin/ProxyTransfer/send");
                            }else{
                                alertbox(data);
                            }
                        }
                        $.post(url,data,fun,'json');
                    }

                });
            }
        });

    });

    /*代理商资金划拨初审*/
    $('.proxy_transfer_apply_approve_btn').click(function(){
        var apply_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyTransfer/approve?apply_id='+ apply_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'代理商资金划拨申请单初审',
                    area: ['680px', '440px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("proxy_transfer_apply_approve_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(){
                        if(!checkform('proxy_transfer_apply_approve_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/ProxyTransfer/approve?operate=approve';
                        var data = $("form[name='proxy_transfer_apply_approve_form']",parent.document).serialize();
                        var fun = function(data){
                            if(data.status == 'success'){
                                location.reload();
                                parent.layer.closeAll();
                            }
                            alertbox(data);
                        }
                        $.post(url,data,fun,'json');
                    }

                });
            }
        });

    });

    /*代理商资金划拨复审*/
    $('.proxy_transfer_apply_approve_t_btn').click(function(){
        var apply_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyTransfer/approve_t?apply_id='+apply_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'代理商资金划拨申请单复审',
                    area: ['680px', '440px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("proxy_transfer_apply_approve_form");
                    },
                    btn:['保存', '取消'],

                    yes: function(index, layero){
                        if(!checkform('proxy_transfer_apply_approve_form')){
                            return false;
                        }

                        var url = '/index.php/Admin/ProxyTransfer/approve_t?operate=approve&sure=sure';
                        var formData = new FormData($("form[name='proxy_transfer_apply_approve_form']",parent.document)[0]);
                        $.ajax({
                            url: url ,
                            type: 'POST',
                            data: formData,
                            async: false,
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function (data) {
                                if(data.status == 'success') {
                                    if(!data.info){
                                        alertbox(data);
                                        parent.layer.closeAll();
                                        location.reload();
                                    }else{
                                        parent.layer.closeAll();
                                        process_sure(data.msg,data.info,"/index.php/Admin/ProxyTransfer/approve_t?operate=approve");
                                    }
                                }else{
                                    alertbox(data);
                                }
                            },
                            error: function (data) {
                                alertbox(data);
                            }
                        });
                    }

                });
            }
        });

    });

    //删除代理商资金划拨
    $(".proxy_transfer_apply_delete_btn").on('click',function() {
        var apply_id = $(this).attr('value');
        var apply_code = $(this).attr('data-name');
        parent.layer.confirm(
            '<i class="confirm_icon"></i>确定是否删除代理商资金划拨【'+apply_code+'】？', 
            {btn: ['确定','取消']}, 
            function(){
                parent.layer.closeAll();
                var url = '/index.php/Admin/ProxyTransfer/delete';
                var data = {apply_id:apply_id};
                var fun = function(data){
                    if(data.status == 'success'){
                        location.reload();
                    }
                    alertbox(data);
                }
                $.post(url,data,fun,'json');
        });
    });

    //查看代理商资金划拨
    $('.proxy_transfer_apply_detailed_btn').click(function(){
        var apply_id = $(this).attr('value');
        var load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyTransfer/show?apply_id='+apply_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'代理商资金划拨信息',
                    area: ['550px', '370px'], //宽高
                    content: $('#proxy_transfer_apply_show_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });
    
    //清空输入框联想内容
    $(document).click(function(){        
        var the_one = $("#"+$(objthis).attr("data-id-name")).val();
        if (the_one=="" || the_one==undefined){
            $(objthis).val("");
        }
        $("#inputusernamelist").hide();
    });

    //通道联想返回信息
    $("div").delegate(".shui_channel_name","keyup",function(){
        objthis = this;
        var width = $(this).outerWidth();
        var top = $(this).offset().top+$(this).outerHeight()+1;
        var left = $(this).offset().left;
        var name = $(this).val();
        var have_discount = $(this).attr("have-discount");
        var is_role = $(this).attr("is-role");
        var is_status = $(this).attr("is-status"); //是否显示禁用的通道
        var is_role_define = 1;
        if(is_role==1){
            width = 230;
        }
        if(have_discount != 1){
            have_discount = 0;
        }
        $("#"+$(this).attr("data-id-name"),top.document).val("");
        if(name!=""){
            $.post("/index.php/Admin/Index/ajax_channel",{name:name,have_discount:have_discount,is_role:is_role,is_role_define:is_role_define,is_status:is_status},function(data){
                $("#channel_ids",top.document).attr("value","");    //此代码无法运行
                if(data.info){
                    var html="";
                    html+="<ul>";
                    for(var i=0; i < data.info.length;i++){
                        if(is_role == 1){
                            html+='<li onclick="shui_get_channel_code('+data.info[i].id+',\''+data.info[i].code+'\')">('+data.info[i].code+')'+data.info[i].name+'</li>';
                        }else{
                            html+='<li onclick="shui_get_channel_code('+data.info[i].id+',\''+data.info[i].code+'\')">'+data.info[i].code+'</li>';
                        }

                    }
                    html+="</ul>";
                    $("#inputusernamelist").show().css("width",width+"px").css("top",top+"px").css('left',left+"px").html(html);
                }else{
                    $("#inputusernamelist").hide().html("");
                }
            },"json");
        }else{
            $("#inputusernamelist").hide().html("");
        }
    });

    /*历史订单详情*/
    $(".order_view_history_btn").on('click',function(){
        var order_id = $(this).attr('value');
        var order_date = $(this).attr('o_date');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        //$("#layerdivid",parent.document).load('/index.php/Admin/RechargeRecord/show?order_id='+order_id,function(data){
         $("#layerdivid",parent.document).load('/index.php/Admin/RechargeRecord/show_history?order_id='+order_id+'&order_date='+order_date,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'订单信息',
                    area: ['660px', '450px'], //宽高
                    // area: ['680px', '546px'], //宽高
                    content: $('#order_view_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });

});

function shui_get_channel_code(id,code){
    var text = code;
    $(objthis).val(text);
    $("#"+$(objthis).attr("data-id-name")).val(id);
}

function get_set_channel_info_allot_user(){
    /***
     $('.radio.checked').removeClass('checked');
     $(this).children('td').children('label.radio').addClass('checked');
     var val = $(this).attr('value');
     ***/
    //单选改为多选
    var ids = '';
    var objlist = $("tbody.channel_list label.checked");

    var count = $("tbody.channel_list label.checked").length;
    for(var i = 0;i< count ;i++){
        ids += ','+$(objlist).eq(i).attr('value');
    }

    ids = ids.substr(1,(ids.length)-1);
    if(!ids){
        $("tbody.no_list").html('');
        $("tbody.have_list").html('');
        return false;
    }
    var val = ids;


    var url = '/index.php/Admin/ChannelUser/set_channel_info_allot_user_rights_list_ajax?channel_id='+val;
    var data = {};
    var fun = function(data){

        if(data.status == 'success'){

            if(data.msg != ''){
                //alertbox(data);
                layer.alert('<i class="confirm_icon"></i>'+data.msg,{title:'提示信息'});
            }
            var no_html = '';
            for(var i=0;i < data.data.no.length; i++ ){
                if(!data.data.no[i]["user_name"]){
                    data.data.no[i]["user_name"] = '';
                }
                var html = '<tr><td><label class="checkbox" value="'
                    +data.data.no[i]["user_code"]+
                    '"><em></em></label></td><td>'
                    +data.data.no[i]['sort_no']+
                    '</td><td>'
                    +data.data.no[i]["user_code"]+
                    '</td><td>'
                    +data.data.no[i]["user_name"]+
                    '</td><td>'
                    +data.data.no[i]["user_type"]+
                    '</td></tr>';

                no_html = no_html + html;
            }
            $("tbody.no_list").html('').append(no_html);

            var have_html = '';
            for(var i=0;i < data.data.have.length; i++ ){
                if(!data.data.have[i]["user_name"]){
                    data.data.have[i]["user_name"] = '';
                }
                var html = '<tr><td><label class="checkbox" value="'
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

/**
 * 通道管理分配
 */
$(function(){
    /**
     *  点击通道，获取通道用户的分配情况

    $('tr.set_channel_info_allot_user').on('click',function(){


    })
     */

    /**
     *  执行添加通道的用户
     */
    $('.set_channel_info_allot_user.rightarrow').on('click',function(){
        var ids = '';
        var objlist = $("tbody.no_list label.checked");
        var count = $("tbody.no_list label.checked").length;
        for(var i = 0;i< count ;i++){
            ids += ','+$(objlist).eq(i).attr('value');
        }
        ids = ids.substr(1,(ids.length)-1);
        if(!ids)return false;

        var c_ids = '';
        var c_objlist = $("tbody.channel_list label.checked");

        var c_count = $("tbody.channel_list label.checked").length;
        for(var i = 0;i< c_count ;i++){
            c_ids += ','+$(c_objlist).eq(i).attr('value');
        }

        c_ids = c_ids.substr(1,(c_ids.length)-1);


        var channel_id = c_ids;
        var url = '/index.php/Admin/ChannelUser/set_channel_info_allot_add_some_rights';
        var data = {channel_id:channel_id,user_codes:ids}
        var fun = function(data){
            if(data.status == 'success'){
                location.href="/index.php/Admin/ChannelUser/set_channel_info_allot_user_rights_list?channel_id="+channel_id;
            }
            alertbox(data);

        }
        $.post(url,data,fun,'json');

    })


    /**
     *  执行删除通道的用户
     */
    $('.set_channel_info_allot_user.leftarrow').on('click',function(){
        var ids = '';
        var objlist = $("tbody.have_list label.checked");
        var count = $("tbody.have_list label.checked").length;
        for(var i = 0;i< count ;i++){
            ids += ','+$(objlist).eq(i).attr('value');
        }
        ids = ids.substr(1,(ids.length)-1);
        if(!ids)return false;

        var c_ids = '';
        var c_objlist = $("tbody.channel_list label.checked");

        var c_count = $("tbody.channel_list label.checked").length;
        for(var i = 0;i< c_count ;i++){
            c_ids += ','+$(c_objlist).eq(i).attr('value');
        }

        c_ids = c_ids.substr(1,(c_ids.length)-1);


        var channel_id = c_ids;
        var url = '/index.php/Admin/ChannelUser/set_channel_info_allot_del_some_rights';
        var data = {channel_id:channel_id,user_codes:ids}
        var fun = function(data){
            if(data.status == 'success'){
                location.href="/index.php/Admin/ChannelUser/set_channel_info_allot_user_rights_list?channel_id="+channel_id;
            }
            alertbox(data);

        }
        $.post(url,data,fun,'json');

    });

    /**
     *  批量设置用户
     */
    $('.set_user_info_allot_channel.leftarrow').on('click',function(){
        var ids = '';
        var objlist = $("tbody.have_list label.checked");
        var count = $("tbody.have_list label.checked").length;
        for(var i = 0;i< count ;i++){
            ids += ','+$(objlist).eq(i).attr('value');
        }
        ids = ids.substr(1,(ids.length)-1);
        if(!ids)return false;

        var c_ids = '';
        var c_objlist = $("tbody.channel_list label.checked");

        var c_count = $("tbody.channel_list label.checked").length;
        for(var i = 0;i< c_count ;i++){
            c_ids += ','+$(c_objlist).eq(i).attr('value');
        }

        c_ids = c_ids.substr(1,(c_ids.length)-1);


        var channel_id = c_ids;
        var url = '/index.php/Admin/ChannelUser/set_channel_info_allot_del_some_rights';
        var data = {channel_id:channel_id,user_codes:ids}
        var fun = function(data){
            if(data.status == 'success'){
                location.href="/index.php/Admin/ChannelUser/set_user_info_channel_btn?channel_id="+channel_id;
            }
            alertbox(data);

        }
        $.post(url,data,fun,'json');

    })
    //批量设置用户
    $('.set_user_info_allot_channel.rightarrow').on('click',function(){
        var ids = '';
        var objlist = $("tbody.no_list label.checked");
        var count = $("tbody.no_list label.checked").length;
        for(var i = 0;i< count ;i++){
            ids += ','+$(objlist).eq(i).attr('value');
        }
        ids = ids.substr(1,(ids.length)-1);
        if(!ids)return false;

        var c_ids = '';
        var c_objlist = $("tbody.channel_list label.checked");

        var c_count = $("tbody.channel_list label.checked").length;
        for(var i = 0;i< c_count ;i++){
            c_ids += ','+$(c_objlist).eq(i).attr('value');
        }

        c_ids = c_ids.substr(1,(c_ids.length)-1);
        var channel_id = c_ids;
        var url = '/index.php/Admin/ChannelUser/set_channel_info_allot_add_some_rights';
        var data = {channel_id:channel_id,user_codes:ids};
        var fun = function(data){
            if(data.status == 'success'){
                location.href="/index.php/Admin/ChannelUser/set_user_info_channel_btn?channel_id="+channel_id;
            }
            alertbox(data);

        }
        $.post(url,data,fun,'json');

    });

    $('.seach_set_right2').on('click',function(){

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

    });

    //禁用/启用代理商退款状态
    $(".proxy_set_refund_status").on('click',function(){
        var proxy_id = $(this).attr('value');
        var status = $(this).attr('data');
        var proxy_name = $(this).parent().parent().children("td.name").html();

        if(status == '1'){
            var title = '确定是否禁用代理商【'+proxy_name+'】退款状态？';
        }else{
            var title = '确定是否启用代理商【'+proxy_name+'】退款状态？';
        }
        var data = {proxy_id:proxy_id};
        var post_url = '/index.php/Admin/Proxy/set_refund_status';
        confirm(title,data,post_url);

    })

    //禁用/启用企业退款状态
    $(".enterprise_set_refund_status").on('click',function(){
        var enterprise_id = $(this).attr('value');
        var status = $(this).attr('data');
        var enterprise_name = $(this).parent().parent().children("td.name").html();

        if(status == '1'){
            var title = '确定是否禁用企业【'+enterprise_name+'】退款状态？';
        }else{
            var title = '确定是否启用企业【'+enterprise_name+'】退款状态？';
        }
        var data = {enterprise_id:enterprise_id};
        var post_url = '/index.php/Admin/Enterprise/set_refund_status';
        confirm(title,data,post_url);

    })

    /*可选择导出*/
    $(".export_button_checked").on('click',function(){
        var  load = top.layer.load(0, {shade: [0.3,'#000']});
        var func=$(this).data('url');
        var type=$(this).data('type');
        var url='';
        if(type=='url'){
            url= '/index.php/Admin/'+func+"/mathran/"+Math.random();
        }else{
            url='/index.php/Admin/'+func+'/export_excel'+"/mathran/"+Math.random();
        }

        /*获取已选择的字段*/
        var field_str = '';
        $('div.tablelist_thead').find('label.checked').each(function(){
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

        var index_url = $("form[name='excel']").attr('action');
        $("form[name='excel']").attr('action',url);
        $("form[name='excel']").submit();
        $("form[name='excel']").attr('action',index_url);
        top.layer.close(load);
    });


    //已完成充值记录导出Excel
    $(".recharge_record_selected_excel_btn").on('click',function(){
        var channel_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/RechargeRecord/export_excel_selected',function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'Excel字段选择',
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

                        url='/index.php/Admin/RechargeRecord/export_excel'+"/mathran/"+Math.random();

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
});

  
