$(function(){


    /*订单详情*/
    $(".order_view_btn").on('click',function(){
        var order_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/RechargeRecord/show?order_id='+order_id,function(data){
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

    /*退款详情*/
    $(".refundid_detailed_function").on('click',function(){
        var refund_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/OrderRefund/show?refund_id='+refund_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'退款信息',
                    area: ['660px', '460px'], //宽高
                    // area: ['750px', '546px'], //宽高
                    content: $('#orderrefund_view_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });



    //审核退款
    $(".orderrefund_approve_function").on('click',function(){
        var refund_id = $(this).attr('value');
        var approve_f = $(this).attr('data-value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/OrderRefund/approve?refund_id='+refund_id+'&approve_f='+approve_f,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'退款审核',
                    area: ['680px', '430px'], //宽高
                    // area: ['680px', '546px'], //宽高
                    content: $('#orderrefund_box',parent.document),
                    success:function(){
                        inputFocus("orderrefund_approve_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform('orderrefund_approve_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/OrderRefund/'+approve_f+'?tran=trans';
                        var data = $("form[name='orderrefund_approve_form']",parent.document).serialize();
                        var fun = function(data){
                            if(data.status == 'success') {
                                if(!data.info){
                                    alertbox(data);
                                    parent.layer.closeAll();
                                    location.reload();
                                }else{
                                    parent.layer.closeAll();
                                    enterprise_transfer(data.msg,data.info,'/index.php/Admin/OrderRefund/'+approve_f);
                                }
                            }else{
                                alertbox(data);
                            }
                        }
                        $.post(url,data,fun,'json');
                    }
                });
            }
        })
    });

    /*删除退款信息*/
    $(".orderrefund_delete_function").on("click",function(){
        var refund_id = $(this).attr('value');
        var apply_code=$(this).data('deletemsg');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否删除该退款申请单？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/OrderRefund/delete';
            var data ={'refund_id':refund_id};
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



    /*更新回调地址和ip鉴权*/
    $(".apiinstructions_btncon").on("click",function(){
        var api_id = $('[name="api_id"]').val();
        var api_callback_address = $('[name="api_callback_address"]').val();
        var api_callback_ip = $('[name="api_callback_ip"]').val();
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否保存当前数据？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/ApiConfiguration/update';
            var data ={'api_id':api_id,'api_callback_address':api_callback_address,'api_callback_ip':api_callback_ip};
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



    /*推送回调信息*/
    $(".order_callback_btn").on("click",function(){
        var order_id =  $(this).attr('value');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否推送回调信息？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/RechargeRecord/callback';
            var data ={'order_id':order_id};
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



    /**
     *  订单申请退款
     */
    /**
     *  新增申请退款
     */
    $('.order_orderrefund_btn').on('click',function(){
        var val = $(this).attr('value');
        $("#layerdivid",top.document).load('/index.php/Admin/RechargeRecord/orderrefund?order_id='+val,function(data){
            if(is_layer(data)){
                top.layer.open({
                    type: 1,
                    title:'新增退款申请',
                    area: ['400px', '430px'], //宽高
                    content: $("#order_refund_box_2",top.document),
                    success:function(){
                        inputFocus("order_refund2_form");
                    },
                    btn:['保存','取消'],
                    yes: function(){
                        if(!checkform('order_refund2_form')){
                            return false;
                        }
                        var formData = new FormData($("form[name='order_refund2_form']",top.document)[0]);
                        $.ajax({
                            url: '/index.php/Admin/RechargeRecord/insert' ,
                            type: 'POST',
                            data: formData,
                            async: false,
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function (data) {
                                alertbox(data);
                                if(data.status == 'success'){
                                    top.layer.closeAll();
                                    location.reload();
                                }
                            },
                            error: function (data) {
                                alertbox(data);
                            }
                        });
                    }
                });
            }
        })
    })


    /**
     *  新增申请退款
     */
    $('.order_refund_add_btn').on('click',function(){
        var val = $(this).attr('value');
        $("#layerdivid",top.document).load('/index.php/Admin/OrderRefund/add?order_id='+val,function(data){
            if(is_layer(data)){
                top.layer.open({
                    type: 1,
                    title:'新增退款申请',
                    area: ['400px', '430px'], //宽高
                    content: $("#order_refund_box",top.document),
                    success:function(){
                        inputFocus("order_refund_form");
                    },
                    btn:['保存','取消'],
                    yes: function(){
                        if(!checkform('order_refund_form')){
                            return false;
                        }
                        var formData = new FormData($("form[name='order_refund_form']",top.document)[0]);
                        $.ajax({
                            url: '/index.php/Admin/OrderRefund/insert' ,
                            type: 'POST',
                            data: formData,
                            async: false,
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function (data) {
                                alertbox(data);
                                if(data.status == 'success'){
                                    top.layer.closeAll();
                                    location.reload();
                                }
                            },
                            error: function (data) {
                                alertbox(data);
                            }
                        });
                    }
                });
            }
        })
    })




})
