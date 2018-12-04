/*-----------------------------------------------------------
代理商授信功能使用的js
------------------------------------------------------------
 */

$(function(){

    /*-----------------------------------------------------------
     新增代理商授信
     ------------------------------------------------------------
     */
    /*代理商授信申请新增功能*/
    $('.proxyBorrow_add_btn').click(function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyBorrow/add ',function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'新增代理商授信申请单',
                    area: ['405px', '340px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("proxyBorrow_add_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(index, layero){
                        if(!checkform('proxyBorrow_add_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/ProxyBorrow/insert';
                        var data = $("form[name='proxyBorrow_add_form']",parent.document).serialize();
                        var fun = function(data){
                            if(data.status == 'success') {
                                parent.layer.closeAll();
                                apply_confirm(data.msg,data.info,"/index.php/Admin/ProxyBorrow/insert?operate=send");
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

    /*代理商授信申请新增功能*/
    $('.proxyBorrow_edit_function').click(function(){
        var loan_id=$(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyBorrow/edit?loan_id='+loan_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'编辑授信申请单',
                    area: ['405px', '340px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("proxyBorrow_edit_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(index, layero){
                        if(!checkform('proxyBorrow_edit_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/ProxyBorrow/update';
                        var data = $("form[name='proxyBorrow_edit_form']",parent.document).serialize();
                        var fun = function(data){
                            if(data.status == 'success') {
                                parent.layer.closeAll();
                                apply_confirm(data.msg,loan_id,"/index.php/Admin/ProxyBorrow/update?operate=send");
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

    /*代理商授信申请送审*/
    $('.proxyBorrow_send_function').click(function(){
        var loan_id=$(this).attr('value');
        apply_confirm('',loan_id,"/index.php/Admin/ProxyBorrow/send_approve");
    });

    /*代理商授信申请复审和初审*/

    $('.proxyBorrow_approve_function').click(function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        var loan_id = $(this).attr('value');
        var approve_c = $(this).data('approve');
        var title='';
        if(approve_c=='approve_t'){
            title='代理商授信申请单复审';
        }
        if(approve_c=='approve'){
            title='代理商授信申请单初审';
        }
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyBorrow/'+approve_c+'?loan_id='+loan_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:title,
                    area: ['680px', '430px'], //宽高
                    content: $('#character_box',parent.document),
                    success:function(){
                        inputFocus("proxyBorrow_approve_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(index, layero){
                        if(!checkform('proxyBorrow_approve_form')){
                            return false;
                        }
                        var url='';
                       // var formData = $("form[name='proxyBorrow_approve_form']",parent.document).serialize();
                        if(approve_c=='approve_t' ){
                            url= '/index.php/Admin/ProxyBorrow/'+approve_c+'?operate=approve&tran=trans';
                        }else{
                            url = '/index.php/Admin/ProxyBorrow/'+approve_c+'?operate=approve';
                        }
                        var data = $("form[name='proxyBorrow_approve_form']",parent.document).serialize();

                        var fun = function(data){
                            if(data.status == 'success') {
                                if(approve_c=='approve_t'){
                                    if(!data.info){
                                        alertbox(data);
                                        parent.layer.closeAll();
                                        location.reload();
                                    }else{
                                        parent.layer.closeAll();
                                        enterprise_transfer(data.msg,data.info,'/index.php/Admin/ProxyBorrow/'+approve_c+'?operate=approve');
                                    }
                                }else{
                                    alertbox(data);
                                    parent.layer.closeAll();
                                    location.reload();
                                }
                            }else{
                                alertbox(data);
                            }
                        };
                        $.post(url,data,fun,'json');
                    }

                });
            }
        });

    });


    /*代理商授信申请删除*/
    $('.proxyBorrow_delete_function').click(function(){
        var loan_id=$(this).attr('value');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否删除该授信申请单？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url = '/index.php/Admin/ProxyBorrow/delete';
            var data ={loan_id:loan_id};
            var fun = function(data){
                alertbox(data);
                if(data.status == 'success') {
                    parent.layer.closeAll();
                }
                location.reload();
            }
            $.post(url,data,fun,'json');
        });
    });
    /*代理商授信申请信息*/
    $('.proxyBorrow_detailed_function').click(function(){
        var loan_id=$(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyBorrow/show?loan_id='+loan_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'授信申请单信息',
                    area: ['600px', '400px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });

} );




/*-----------------------------------------------------------
 代理商还款功能使用的js
 ------------------------------------------------------------
 */
$(function(){
    /*代理商还款*/
    $('.proxyBorrow_payBack_function').click(function(){
        var loan_id=$(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyPayBack/add?loan_id='+loan_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'新增还款申请单',
                    area: ['480px', '510px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("proxyPayBack_add_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(index, layero){
                        if(!checkform('proxyPayBack_add_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/ProxyPayBack/insert';
                        var formData = new FormData($("form[name='proxyPayBack_add_form']",parent.document)[0]);
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
                                    parent.layer.closeAll();
                                    apply_confirm(data.msg,data.info,"/index.php/Admin/ProxyPayBack/insert?operate=send");
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
    /*代理商还款编辑*/
    $('.proxyPayBack_edit_function').click(function(){
        var repaymen_id=$(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyPayBack/edit?repaymen_id='+repaymen_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'编辑还款申请单',
                    area: ['480px', '520px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("proxyPayBack_voucher_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(index, layero){
                        if(!checkform('proxyPayBack_voucher_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/ProxyPayBack/update';
                        var formData = new FormData($("form[name='proxyPayBack_voucher_form']",parent.document)[0]);
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
                                    parent.layer.closeAll();
                                    apply_confirm(data.msg,repaymen_id,"/index.php/Admin/ProxyPayBack/update?operate=send");
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

    /*代理商还款申请送审*/
    $('.proxyPayBack_send_function').click(function(){
        var repaymen_id=$(this).attr('value');
        apply_confirm('',repaymen_id,"/index.php/Admin/ProxyPayBack/send_approve");
    });
    /*代理商还款申请删除*/
    $('.proxyPayBack_delete_function').click(function(){
        var repaymen_id=$(this).attr('value');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否删除该还款申请单？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url = '/index.php/Admin/ProxyPayBack/delete';
            var data ={repaymen_id:repaymen_id};
            var fun = function(data){
                alertbox(data);
                if(data.status == 'success') {
                    parent.layer.closeAll();
                }
                location.reload();
            }
            $.post(url,data,fun,'json');
        });
    });

    /*代理商还款申请信息*/
    $('.proxyPayBack_detailed_function').click(function(){
        var repaymen_id=$(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyPayBack/show?repaymen_id='+repaymen_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'还款申请单信息',
                    area: ['600px', '400px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });


    /*代理商还款审核*/
    $(".proxyPayBack_approve_function").on('click',function(){
        var repaymen_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyPayBack/approve?repaymen_id='+repaymen_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'代理商还款申请审核',
                    area: ['680px', '510px'], //宽高
                    content: $('#character_box',parent.document),
                    success:function(){
                        inputFocus("proxyPayBack_approve_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform('proxyPayBack_approve_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/ProxyPayBack/approve?operate=approve&tran=trans';
                        var data = $("form[name='proxyPayBack_approve_form']",parent.document).serialize();
                        var fun = function(data){
                            if(data.status == 'success') {
                                if(!data.info){
                                    alertbox(data);
                                    parent.layer.closeAll();
                                    location.reload();
                                }else{
                                    parent.layer.closeAll();
                                    enterprise_transfer(data.msg,data.info,'/index.php/Admin/ProxyPayBack/approve?operate=approve');
                                }
                            }else{
                                alertbox(data);
                            }
                        }
                        $.post(url,data,fun,'json');
                    }
                });
            }else{
                parent.layer.close(load);
            }
        })
    });



} );