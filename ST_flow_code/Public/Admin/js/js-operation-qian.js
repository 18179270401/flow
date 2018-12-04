
$(function(){
    /*代理商添加账户*/
    $('.proxyAccount_add_btn').on('click',function(){
        var apply_code = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyAccount/add',function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'新增代理商账户',
                    area: ['400px', '200px'], //宽高
                    // area: ['680px', '546px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("proxyAccount_voucher_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(){
                        if(!checkform('proxyAccount_voucher_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/ProxyAccount/insert';
                        var data = $("form[name='proxyAccount_voucher_form']",parent.document).serialize();
                        var fun = function(data){
                            alertbox(data);
                            if(data.status == 'success') {
                                location.reload();
                                parent.layer.closeAll();
                            }

                        }
                        $.post(url,data,fun,'json');
                    }
                });
            }
        })
    });


    /*代理商查看详细*/
    $(".detailed_function").on('click',function(){
        var account_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyAccount/show?account_id='+account_id+'&operate=detailed',function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'代理商账户信息',
                    area: ['420px', '350px'], //宽高
                    // area: ['680px', '546px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });
    
    /*代理商充值查看详细*/
    $(".proxyRecharge_detailed_function").on('click',function(){
        var apply_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyRecharge/show?apply_id='+apply_id+'&operate=detailed',function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'代理商充值信息',
                    area: ['630px', '350px'], //宽高
                    //area: ['680px', '546px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });

     /*代理商充值管理 查看详细*/
    $(".proxyRec_detailed_function").on('click',function(){
        var apply_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyRec/show?apply_id='+apply_id+'&operate=detailed',function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'代理商充值信息',
                    area: ['600px', '330px'], //宽高
                    // area: ['680px', '546px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });

/*待处理订单信息*/
    $('.orderPending_view_btn').on('click',function(){
        var order_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        var enterprise_id = $(this).attr('value');
        var title = '待处理订单信息';
        var area =  ['600px', '450px'];
        var view_name = 'order_view_box';
        var view_url = '/index.php/Admin/OrderPending/show?order_id='+order_id;
        view_order(title,area,view_name,view_url);
    });

    //代理商充值申请
    $(".recharge_function").on('click',function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyRecharge/voucher',function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
    			parent.layer.open({
		          type: 1,
                   title:'新增充值申请',
                   area: ['430px', '470px'], //宽高
                   content: $('#add_box',parent.document),
                   success:function(){
                        inputFocus("ProxyRecharge_voucher_form");
                    },
                   btn:['保存', '取消'],
                   yes: function(index, layero){
                        if(!checkform('ProxyRecharge_voucher_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/ProxyRecharge/insert';
                        var formData = new FormData($("form[name='ProxyRecharge_voucher_form']",parent.document)[0]);
                        $.ajax({
                            url: url ,
                            type: 'POST',
                            data: formData,
                            async: false,
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function (data) {
                                if(data.status == 'success'){
                                    parent.layer.closeAll();
                                    apply_confirm(data.msg,data.info,"/index.php/Admin/ProxyRecharge/insert?operation=giveapply");
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
    /*尚通端给代理商的充值申请*/
    $('.st_voucher_add_function').on('click',function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyRecharge/st_voucher',function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'新增充值申请',
                    area: ['430px', '470px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("ProxyRecharge_voucher_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(index, layero){
                        if(!checkform('ProxyRecharge_voucher_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/ProxyRecharge/insert';
                        var formData = new FormData($("form[name='ProxyRecharge_voucher_form']",parent.document)[0]);
                        $.ajax({
                            url: url ,
                            type: 'POST',
                            data: formData,
                            async: false,
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function (data) {
                                alertbox(data);
                                if(data.status == 'success') {
                                    location.reload();
                                    parent.layer.closeAll();
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
    /*尚通端给代理商的充值申请编辑*/
    $(".proxyRecharge_strecharge_edit_function").on("click",function(){
        var apply_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyRecharge/st_edit?apply_id='+apply_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'编辑充值申请',
                    area: ['430px', '490px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("ProxyRecharge_edit_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform('ProxyRecharge_edit_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/ProxyRecharge/st_edit?operation=update';
                        var formData = new FormData($("form[name='ProxyRecharge_edit_form']",parent.document)[0]);
                        $.ajax({
                            url: url ,
                            type: 'POST',
                            data: formData,
                            async: false,
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function (data) {
                                alertbox(data);
                                if(data.status == 'success') {
                                    location.reload();
                                    parent.layer.closeAll();
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
    });

    /*修改充值申请*/
    $(".proxyRecharge_edit_function").on("click",function(){
        var apply_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyRecharge/edit?apply_id='+apply_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'编辑充值申请',
                    area: ['430px', '490px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("ProxyRecharge_edit_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform('ProxyRecharge_edit_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/ProxyRecharge/update';
                        var formData = new FormData($("form[name='ProxyRecharge_edit_form']",parent.document)[0]);
                        $.ajax({
                            url: url ,
                            type: 'POST',
                            data: formData,
                            async: false,
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function (data) {
                                if(data.status == 'success'){
                                    parent.layer.closeAll();
                                    apply_confirm(data.msg,apply_id,"/index.php/Admin/ProxyRecharge/insert?operation=giveapply");
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
        })
    });
    /*送审*/
    $(".proxyRecharge_apply_function").on("click",function(){
        var apply_id = $(this).attr('value');
        parent.layer.confirm('<i class="confirm_icon"></i>现在要提交审核吗？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/ProxyRecharge/send_approve';
            var data ={'id':apply_id};
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
    /*删除代理商充值申请信息*/
    $(".proxyRecharge_delete_function").on("click",function(){
        var apply_id = $(this).attr('value');
        var apply_code=$(this).data('deletemsg');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否删除该申请单？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/ProxyRecharge/delete';
            var data ={'apply_id':apply_id};
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



    //审核代理商
    $(".proxyRecharge_approve_function").on('click',function(){
        var apply_id = $(this).attr('value');
        var approve_f = $(this).attr('data-value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyRecharge/show?apply_id='+apply_id+'&operate=approve&approve_f='+approve_f,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'代理商充值审核',
                    area: ['680px', '430px'], //宽高
                   // area: ['680px', '546px'], //宽高
                    content: $('#character_box',parent.document),
                    success:function(){
                        inputFocus("proxyAccount_approve_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform('proxyAccount_approve_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/ProxyRecharge/'+approve_f+'?tran=trans';
                        var data = $("form[name='proxyAccount_approve_form']",parent.document).serialize();
                        var fun = function(data){
                            if(data.status == 'success') {
                                if(!data.info){
                                    alertbox(data);
                                    parent.layer.closeAll();
                                    location.reload();
                                }else{
                                    parent.layer.closeAll();
                                    enterprise_transfer(data.msg,data.info,'/index.php/Admin/ProxyRecharge/'+approve_f);    
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
    
    //审核代理商充值管理
    $(".proxyRec_approve_function").on('click',function(){
        var apply_id = $(this).attr('value');
        var approve_f = $(this).attr('data-value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyRec/show?apply_id='+apply_id+'&operate=approve&approve_f='+approve_f,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'代理商充值审核',
                    area: ['680px', '430px'], //宽高
                   // area: ['680px', '546px'], //宽高
                    content: $('#character_box',parent.document),
                    success:function(){
                        inputFocus("proxyAccount_approve_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform('proxyAccount_approve_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/ProxyRec/'+approve_f+'?tran=trans';
                        var data = $("form[name='proxyAccount_approve_form']",parent.document).serialize();
                        var fun = function(data){
                            if(data.status == 'success') {
                                if(!data.info){
                                    alertbox(data);
                                    parent.layer.closeAll();
                                    location.reload();
                                }else{
                                    parent.layer.closeAll();
                                    enterprise_transfer(data.msg,data.info,'/index.php/Admin/ProxyRec/'+approve_f);    
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
    
    /*代理商划拨*/
    $('.transfer_function').on('click',function(){
        var account_id=$(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        var status=$(this).attr('data-status');
        var h="550px";
        if(status=="1"){
            h="390px";
        }
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyAccount/transfer?operate=show&account_id='+account_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'代理商账户充值',
                    area: ['350px', h], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("proxyAccount_transfer_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform('proxyAccount_transfer_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/ProxyAccount/transfer?tran=trans';
                        var formData = new FormData($("form[name='proxyAccount_transfer_form']",parent.document)[0]);
                        $.ajax({
                            url: url ,
                            type: 'POST',
                            data: formData,
                            async: false,
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function (data) {
                                if(data.status == 'success'){
                                    parent.layer.closeAll();
                                    enterprise_transfer(data.msg,data.info,"/index.php/Admin/ProxyAccount/transfer?operate=transfer");
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
    /*收回代理商金额*/
    $('.return_function').on('click',function(){
        var account_id = $(this).attr('value');
        var status=$(this).attr('data-status');
        var h="510px";
        if(status=="1"){
            h="350px";
        }
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyAccount/return_money?account_id='+account_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'代理商资金收回',
                    area: ['350px', h], //宽高
                    content: $('#return_box',parent.document),
                    success:function(){
                        inputFocus("proxyAccount_return_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform('proxyAccount_return_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/ProxyAccount/return_money?tran=trans';
                        var data = $("form[name='proxyAccount_return_form']",parent.document).serialize();
                        var fun = function(data){
                            if(data.status == 'success'){
                                    parent.layer.closeAll();
                                    enterprise_transfer(data.msg,data.info,"/index.php/Admin/ProxyAccount/return_money");
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

    $('.proxy_freeze_function').on('click',function(){
        var account_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyAccount/freeze_money?account_id='+account_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'代理商资金冻结',
                    area: ['350px', "390px"], //宽高
                    content: $('#return_box',parent.document),
                    success:function(){
                        inputFocus("proxyAccount_freeze_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform('proxyAccount_freeze_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/ProxyAccount/freeze_money?tran=trans';
                        var data = $("form[name='proxyAccount_freeze_form']",parent.document).serialize();
                        var fun = function(data){
                            if(data.status == 'success'){
                                    parent.layer.closeAll();
                                    enterprise_transfer(data.msg,data.info,"/index.php/Admin/ProxyAccount/freeze_money");
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

    /*企业账户划拨操作*/
    $('.enterprise_transfer_function').click(function(){
        var enterprise_id=$(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseAccount/transfer?operate=show&enterprise_id='+enterprise_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'代理商账户划拨',
                    area: ['430px', '230px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("enterpriseAccount_transfer_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform('enterpriseAccount_transfer_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/EnterpriseAccount/transfer?operate=transfer';
                        var formData = new FormData($("form[name='enterpriseAccount_transfer_form']",parent.document)[0]);
                        $.ajax({
                            url: url ,
                            type: 'POST',
                            data: formData,
                            async: false,
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function (data) {
                                alertbox(data);
                                if(data.status == 'success'){
                                    parent.layer.closeAll();
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
        });
    });
    
    /*企业充值查看详细*/
    $(".enterpriseRecharge_detailed_function").on('click',function(){
        var apply_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseRecharge/show?apply_id='+apply_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'企业充值单信息',
                    area: ['600px', '320px'], //宽高
                    // area: ['680px', '546px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });

    //审核企业
    $(".enterpriseRecharge_approve_function").on('click',function(){
        var apply_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseRecharge/approve_c?apply_id='+apply_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'企业充值审核',
                    area: ['680px', '425px'], //宽高
                    // area: ['680px', '546px'], //宽高
                    content: $('#character_box',parent.document),
                    success:function(){
                        inputFocus("enterpriseRecharge_approve_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform('enterpriseRecharge_approve_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/EnterpriseRecharge/enterprise_approve';
                        var data = $("form[name='enterpriseRecharge_approve_form']",parent.document).serialize();
                        var fun = function(data){
                            alertbox(data);
                            if(data.status == 'success') {
                                location.reload();
                                parent.layer.closeAll();
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

    //审核企业
    $(".enterpriseRecharge_approve_t_function").on('click',function(){
        var apply_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseRecharge/approve_t?apply_id='+apply_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'审核',
                    area: ['680px', '425x'], //宽高
                    // area: ['680px', '546px'], //宽高
                    content: $('#character_box',parent.document),
                    success:function(){
                        inputFocus("enterpriseRecharge_approve_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform('enterpriseRecharge_approve_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/EnterpriseRecharge/enterprise_approve?tran=trans';
                        var data = $("form[name='enterpriseRecharge_approve_form']",parent.document).serialize();
                        var fun = function(data){
                            if(data.status == 'success') {
                                if(!data.info){
                                    alertbox(data);
                                    parent.layer.closeAll();
                                    location.reload();
                                }else{
                                    parent.layer.closeAll();
                                    enterprise_transfer(data.msg,data.info,"/index.php/Admin/EnterpriseRecharge/enterprise_approve");    
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
/*代理商充值明细详情*/
    $('.proxyRecord_detailed_function').click(function(){
        var record_id = $(this).attr('value');
        var record_type=$(this).data('record');
        var title='';
        if(record_type=='recharge'){
            title='代理商充值明细表信息';
        }
        if(record_type=='withdraw'){
            title='代理商提现明细表信息';
        }
        if(record_type=='allRecharge'){
            title='代理商收支明细表信息';
        }
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyDetails/show?record_id='+record_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:title,
                    area: ['600px', '350px'], //宽高
                    // area: ['680px', '546px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });
/*企业充值申请*/
   $('.enterpriseRecharge_add_btn').click(function(){
       var  load = parent.layer.load(0, {shade: [0.3,'#000']});
       $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseRecharge/voucher ',function(data){
           parent.layer.close(load);
           if(is_layer(data)) {
               parent.layer.open({
                   type: 1,
                   title:'新增企业充值申请',
                   area: ['405px', '470px'], //宽高
                   content: $('#add_box',parent.document),
                   success:function(){
                        inputFocus("enterpriseRecharge_voucher_form");
                    },
                   btn:['保存', '取消'],
                   yes: function(index, layero){
                       if(!checkform('enterpriseRecharge_voucher_form')){
                           return false;
                       }
                       var url = '/index.php/Admin/EnterpriseRecharge/insert';
                       var formData = new FormData($("form[name='enterpriseRecharge_voucher_form']",parent.document)[0]);

                       $.ajax({
                           url: url ,
                           type: 'POST',
                           data: formData,
                           async: false,
                           cache: false,
                           contentType: false,
                           processData: false,
                           success: function (data) {
                               if(data.status == 'success'){
                                   parent.layer.closeAll();
                                   apply_confirm(data.msg,data.info,"/index.php/Admin/EnterpriseRecharge/edit?operate=sentHear&operates=update");
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

    /*代理商给企业填写充值申请*/
    $('.enterpriseRecharge_new_add_btn').click(function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseRecharge/voucher_proxy ',function(data){
            if(is_layer(data)) {
                parent.layer.close(load);
                parent.layer.open({
                    type: 1,
                    title:'新增企业充值申请',
                    area: ['405px', '430px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("enterpriseRecharge_voucher_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(index, layero){
                        if(!checkform('enterpriseRecharge_voucher_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/EnterpriseRecharge/voucher_proxy?operate=insert';
                        var formData = new FormData($("form[name='enterpriseRecharge_voucher_form']",parent.document)[0]);

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
                                    alertbox(data);
                                    parent.layer.closeAll();
                                    location.reload();
                                }else{
                                    alertbox(data);
                                }

                               /* if(data.status == 'success'){
                                    parent.layer.closeAll();
                                    apply_confirm(data.msg,data.info,"/index.php/Admin/EnterpriseRecharge/add?operate=sentHear&operates=update");
                                }else{
                                    alertbox(data);
                                }*/
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
    /*代理商给企业充值后申请*/
    $('.enterpriseRecharge_edit_proxy_function').click(function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        var apply_id=$(this).attr('value');
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseRecharge/edit_proxy?apply_id='+apply_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'编辑企业充值申请',
                    area: ['405px', '460px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("enterpriseRecharge_voucher_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(index, layero){
                        if(!checkform('enterpriseRecharge_voucher_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/EnterpriseRecharge/edit_proxy?operate=update';
                        var formData = new FormData($("form[name='enterpriseRecharge_voucher_form']",parent.document)[0]);
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
                                    alertbox(data);
                                    parent.layer.closeAll();
                                    location.reload();
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
    /*企业端修改*/
    $('.enterpriseRecharge_edit_function').click(function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        var apply_id=$(this).attr('value');
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseRecharge/edit?apply_id='+apply_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'编辑企业充值申请',
                    area: ['405px', '460px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("enterpriseRecharge_voucher_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(index, layero){
                        if(!checkform('enterpriseRecharge_voucher_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/EnterpriseRecharge/edit?operates=update';
                        var formData = new FormData($("form[name='enterpriseRecharge_voucher_form']",parent.document)[0]);
                        $.ajax({
                            url: url ,
                            type: 'POST',
                            data: formData,
                            async: false,
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function (data) {
                                if(data.status == 'success'){
                                    parent.layer.closeAll();
                                    apply_confirm(data.msg,apply_id,"/index.php/Admin/EnterpriseRecharge/edit?operate=sentHear&operates=update");
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

    /*企业端充值申请送审*/
    $('.enterpriseRecharge_send_function').click(function(){
        var apply_id=$(this).attr('value');
        apply_confirm('',apply_id,"/index.php/Admin/EnterpriseRecharge/edit?operate=sentHear&operates=update");
    });
    /*企业端充值申请删除*/
    $('.enterpriseRecharge_delete_function').click(function(){
        var apply_id=$(this).attr('value');
        var apply_code=$(this).data('deletemsg');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否删除该申请单？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url = '/index.php/Admin/EnterpriseRecharge/edit?operate=delete&operates=update';
            var data ={id:apply_id};
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
    /*代理商端充值申请删除*/
    $('.enterpriseRecharge_delete_proxy_function').click(function(){
        var apply_id=$(this).attr('value');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否删除该申请单？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url = '/index.php/Admin/EnterpriseRecharge/proxy_delete';
            var data ={id:apply_id};
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



    /*订单详情*/
    $('.orders_detailed_btn').click(function(){
        var order_id = $(this).attr('value');
        var record_type=$(this).data('record');
        var title='';
        if(record_type=='unpaid'){
            title='未支付订单信息';
        }
        if(record_type=='completed'){
            title='已完成订单信息';
        }
        if(record_type=='canceled'){
            title='已取消订单信息';
        }
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});  //0.1透明度的白色背景
        $("#layerdivid",parent.document).load('/index.php/Admin/Orders/show?order_id='+order_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:title,
                    area: ['600px', '400px'], //宽高
                    // area: ['680px', '546px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });


    /*现金收入支出详细信息*/
    $('.cashRecord_detailed_function').click(function(){
        var record_id = $(this).attr('value');
        var record_type=$(this).data('record');
        var title='';
        if(record_type=='income'){
            title='现金收入记录信息';
        }
        if(record_type=='payout'){
            title='现金支出记录信息';
        }
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/CashRecord/detailed?record_id='+record_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:title,
                    area: ['600px', '300px'], //宽高
                    // area: ['680px', '546px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });

    /*通道详细*/

    $('.channel_show_btn').click(function(){
        var channel_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/Channel/show?channel_id='+channel_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'通道配置信息',
                    area: ['400px', '480px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });

    /*通道产品详细*/
    $('.channelproduct_show_btn').click(function(){
        var product_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ChannelProduct/show?product_id='+product_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'通道产品信息',
                    area: ['500px', '300px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });
    /*流量包详细*/
    $('.flow_show_btn').click(function(){
        var product_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/Flow/show?product_id='+product_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'流量包信息',
                    area: ['400px', '340px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });

    /*角色详细*/
    $('.role_show_edit').click(function(){
        var role_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/Role/show?role_id='+role_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'角色信息',
                    area: ['500px', '250px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });

    /*部门详细*/
    $('.depart_show_btn').click(function(){
        var depart_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/Depart/show?depart_id='+depart_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'部门信息',
                    area: ['400px', '250px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });

    /*菜单详细*/
    $('.menu_show_btn').click(function(){
        var menu_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/Menu/show?menu_id='+menu_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'菜单信息',
                    area: ['500px', '280px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });

    /*功能详细*/
    $('.right_show_btn').click(function(){
        var function_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/Right/show?function_id='+function_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'功能信息',
                    area: ['500px', '250px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });

    /*企业授信申请新增功能*/
    $('.enterpriseBorrow_add_btn').click(function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseBorrow/add ',function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'新增授信申请单',
                    area: ['405px', '340px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("enterpriseBorrow_add_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(index, layero){
                        if(!checkform('enterpriseBorrow_add_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/EnterpriseBorrow/insert';
                        var data = $("form[name='enterpriseBorrow_add_form']",parent.document).serialize();
                        var fun = function(data){
                            if(data.status == 'success') {
                                parent.layer.closeAll();
                                apply_confirm(data.msg,data.info,"/index.php/Admin/EnterpriseBorrow/insert?operate=send");
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

    /*企业授信申请新增功能*/
    $('.enterpriseBorrow_edit_function').click(function(){
        var loan_id=$(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseBorrow/edit?loan_id='+loan_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'编辑授信申请单',
                    area: ['405px', '340px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("enterpriseBorrow_edit_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(index, layero){
                        if(!checkform('enterpriseBorrow_edit_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/EnterpriseBorrow/update';
                        var data = $("form[name='enterpriseBorrow_edit_form']",parent.document).serialize();
                        var fun = function(data){
                            if(data.status == 'success') {
                                parent.layer.closeAll();
                                apply_confirm(data.msg,loan_id,"/index.php/Admin/EnterpriseBorrow/update?operate=send");
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

    /*企业授信申请送审*/
    $('.enterpriseBorrow_send_function').click(function(){
        var loan_id=$(this).attr('value');
        apply_confirm('',loan_id,"/index.php/Admin/EnterpriseBorrow/send_approve");
    });

/*初审*/

    $('.enterpriseBorrowManagement_approve_function').click(function(){
        var loan_id=$(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseBorrow/approve?loan_id='+loan_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'企业授信申请初审',
                    area: ['680px', '480px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("enterpriseBorrowManagement_approve_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(index, layero){
                        if(!checkform('enterpriseBorrowManagement_approve_form')){
                            return false;
                        }
                        var approve_status = $("#approve_status",parent.document).val();
                        var remark = $("#approve_remark",parent.document).val();
                        if(approve_status==2 && remark==""){
                            alertbox({'msg':'请填写审核驳回原因','status':'error'});
                            return false;
                        }
                        var url = '/index.php/Admin/EnterpriseBorrow/approve?operate=approve';
                        var formData = new FormData($("form[name='enterpriseBorrowManagement_approve_form']",parent.document)[0]);
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
                                    alertbox(data);
                                    parent.layer.closeAll();
                                    location.reload();
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
            }else{
                parent.layer.close(load);
            }
        });

    });

    /*企业授信复审*/
    $(".enterpriseBorrowManagement_approve_t_function").on('click',function(){
        var loan_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseBorrow/approve_t?loan_id='+loan_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'企业授信申请复审',
                    area: ['680px', '430px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("enterpriseBorrowManagement_approve_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform('enterpriseBorrowManagement_approve_form')){
                            return false;
                        }
                       /* var approve_status = $("#approve_status",parent.document).val();
                        var remark = $("#approve_remark",parent.document).val();
                        if(approve_status==2 && remark==""){
                            alertbox({'msg':'请填写审核驳回原因','status':'error'});
                            return false;
                        }*/

                        var url = '/index.php/Admin/EnterpriseBorrow/approve_t?operate=approve&tran=trans';
                        var data = $("form[name='enterpriseBorrowManagement_approve_form']",parent.document).serialize();
                        var fun = function(data){
                            if(data.status == 'success') {

                                if(!data.info){
                                    alertbox(data);
                                    parent.layer.closeAll();
                                    location.reload();
                                }else{
                                    parent.layer.closeAll();
                                    enterprise_transfer(data.msg,data.info,'/index.php/Admin/EnterpriseBorrow/approve_t?operate=approve');
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

    /*企业授信申请删除*/
    $('.enterpriseBorrow_delete_function').click(function(){
        var loan_id=$(this).attr('value');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否删除该授信申请单？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url = '/index.php/Admin/EnterpriseBorrow/delete';
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
    /*企业授信申请信息*/
    $('.enterpriseBorrow_detailed_function').click(function(){
        var loan_id=$(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseBorrow/show?loan_id='+loan_id,function(data){
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

    /*企业管理授信申请信息*/
    $('.enterpriseBorrowManagement_detailed_function').click(function(){
        var loan_id=$(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseBorrowManagement/show?loan_id='+loan_id,function(data){
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

    /*企业还款*/
    $('.enterpriseBorrow_payBack_function').click(function(){
        var loan_id=$(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterprisePayBack/add?loan_id='+loan_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'新增还款申请单',
                    area: ['480px', '510px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("enterprisePayBack_add_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(index, layero){
                        if(!checkform('enterprisePayBack_add_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/EnterprisePayBack/insert';
                        var formData = new FormData($("form[name='enterprisePayBack_add_form']",parent.document)[0]);
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
                                    apply_confirm(data.msg,data.info,"/index.php/Admin/EnterprisePayBack/insert?operate=send");
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
    /*企业还款编辑*/
    $('.enterprisePayBack_edit_function').click(function(){
        var repaymen_id=$(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterprisePayBack/edit?repaymen_id='+repaymen_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'编辑还款申请单',
                    area: ['480px', '520px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("enterprisePayBack_voucher_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(index, layero){
                        if(!checkform('enterprisePayBack_voucher_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/EnterprisePayBack/update';
                        var formData = new FormData($("form[name='enterprisePayBack_voucher_form']",parent.document)[0]);
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
                                    apply_confirm(data.msg,repaymen_id,"/index.php/Admin/EnterprisePayBack/update?operate=send");
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

    /*企业还款申请送审*/
    $('.enterprisePayBack_send_function').click(function(){
        var repaymen_id=$(this).attr('value');
        apply_confirm('',repaymen_id,"/index.php/Admin/EnterprisePayBack/send_approve");
    });
    /*企业还款申请删除*/
    $('.enterprisePayBack_delete_function').click(function(){
        var repaymen_id=$(this).attr('value');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否删除该还款申请单？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url = '/index.php/Admin/EnterprisePayBack/delete';
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

    /*企业还款申请信息*/
    $('.enterprisePayBack_detailed_function').click(function(){
        var repaymen_id=$(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterprisePayBack/show?repaymen_id='+repaymen_id,function(data){
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


    /*企业还款审核*/
    $(".enterprisePayBack_approve_function").on('click',function(){
        var repaymen_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterprisePayBack/approve?repaymen_id='+repaymen_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'企业还款申请审核',
                    area: ['680px', '510px'], //宽高
                    content: $('#character_box',parent.document),
                    success:function(){
                        inputFocus("enterprisePayBack_approve_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform('enterprisePayBack_approve_form')){
                            return false;
                        }
                        /*var approve_status = $("#approve_status",parent.document).val();
                        var remark = $("#remark",parent.document).val();
                        if(approve_status==2 && remark==""){
                            alertbox({'msg':'请填写审核驳回原因','status':'error'});
                            return false;
                        }*/
                        var url = '/index.php/Admin/EnterprisePayBack/approve?operate=approve&tran=trans';
                        var data = $("form[name='enterprisePayBack_approve_form']",parent.document).serialize();
                        var fun = function(data){
                            if(data.status == 'success') {
                                if(!data.info){
                                    alertbox(data);
                                    parent.layer.closeAll();
                                    location.reload();
                                }else{
                                    parent.layer.closeAll();
                                    enterprise_transfer(data.msg,data.info,'/index.php/Admin/EnterprisePayBack/approve?operate=approve');
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

    /*代理商账户冻结信息*/
    $('.proxyAccountOperate_detailed_function').click(function(){
        var apply_id=$(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyAccountOperate/show?apply_id='+apply_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'代理商账户冻结信息',
                    area: ['600px', '400px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });

    /*添加代理商账户冻结*/
    $('.proxy_freeze_add_btn').click(function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyAccountOperate/add ',function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'新增代理商账户冻结',
                    area: ['405px', '340px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("proxyAccountOperate_add_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(index, layero){
                        if(!checkform('proxyAccountOperate_add_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/ProxyAccountOperate/insert';
                        var data = $("form[name='proxyAccountOperate_add_form']",parent.document).serialize();
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

    /*编辑代理商账户冻结*/
    $('.proxy_freeze_edit_function').click(function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        var apply_id = $(this).attr('value');
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyAccountOperate/edit?apply_id='+apply_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'编辑代理商账户冻结',
                    area: ['405px', '340px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("proxyAccountOperate_edit_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(index, layero){
                        if(!checkform('proxyAccountOperate_edit_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/ProxyAccountOperate/update';
                        var data = $("form[name='proxyAccountOperate_edit_form']",parent.document).serialize();
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

    /*代理商账户冻结复审和初审*/

    $('.freeze_approve_function').click(function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        var apply_id = $(this).attr('value');
        var approve_c = $(this).data('approve');
        var title='';
        if(approve_c=='freeze_approve_c'){
            title='代理商账户冻结金额复审';
        }
        if(approve_c=='freeze_approve'){
            title='代理商账户冻结金额初审';
        }
        if(approve_c=='relieve_approve_c'){
            title='代理商账户解冻申请复审';
        }
        if(approve_c=='relieve_approve'){
            title='代理商账户解冻申请初审';
        }
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyAccountOperate/'+approve_c+'?apply_id='+apply_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:title,
                    area: ['680px', '430px'], //宽高
                    content: $('#character_box',parent.document),
                    success:function(){
                        inputFocus("proxyAccountOperate_approve_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(index, layero){
                        if(!checkform('proxyAccountOperate_approve_form')){
                            return false;
                        }
                        var url='';
                        var data = $("form[name='proxyAccountOperate_approve_form']",parent.document).serialize();
                        if(approve_c=='freeze_approve_c' || approve_c=='relieve_approve_c'){
                            url= '/index.php/Admin/ProxyAccountOperate/'+approve_c+'?operate=approve&tran=trans';
                        }else{
                            url = '/index.php/Admin/ProxyAccountOperate/'+approve_c+'?operate=approve';
                        }
                        var fun = function(data){
                            if(data.status == 'success') {
                                if(approve_c=='freeze_approve_c'|| approve_c=='relieve_approve_c'){
                                    if(!data.info){
                                        alertbox(data);
                                        parent.layer.closeAll();
                                        location.reload();
                                    }else{
                                        parent.layer.closeAll();
                                        enterprise_transfer(data.msg,data.info,'/index.php/Admin/ProxyAccountOperate/'+approve_c+'?operate=approve');
                                    }
                                }else{
                                    alertbox(data);
                                    parent.layer.closeAll();
                                    location.reload();
                                }
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
    /*删除代理商冻结*/
    $(".proxy_freeze_delete_function").on("click",function(){
        var apply_id = $(this).attr('value');
        var deletemsg=$(this).data('deletemsg');
        var money=$(this).data('money');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否删除代理商【'+deletemsg+'】账户冻结金额【'+money+'】元？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/ProxyAccountOperate/delete';
            var data ={'apply_id':apply_id};
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

    /*代理商解冻*/

    $('.relieve_add_function').click(function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        var apply_id = $(this).attr('value');
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyAccountOperate/relieve?apply_id='+apply_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'代理商账户解冻申请',
                    area: ['405px', '340px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("proxyAccountOperate_relieve_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(index, layero){
                        if(!checkform('proxyAccountOperate_relieve_form')){
                            return false;
                        }
                        var data = $("form[name='proxyAccountOperate_relieve_form']",parent.document).serialize();
                        var url= '/index.php/Admin/ProxyAccountOperate/relieve?operate=approve&tran=trans';
                        var fun = function(data){
                            if(data.status == 'success') {
                                    if(!data.info){
                                        alertbox(data);
                                        parent.layer.closeAll();
                                        location.reload();
                                    }else{
                                        parent.layer.closeAll();
                                        enterprise_transfer(data.msg,data.info,'/index.php/Admin/ProxyAccountOperate/relieve?operate=approve');
                                    }
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
    /*账户管理绑定通道*/
    $(".channel_link_btn").on('click',function() {
        var account_id = $(this).attr('value');
        var show_name = $(this).attr('show-name');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ChannelAccount/add_channel?account_id='+account_id+'&approve=show',function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                var url1 = '/index.php/Admin/ChannelAccount/show';
                var data1 = {account_id:account_id,approve:'show'};
                var fun1 = function(data1){
                    //console.log(data1.funids);
                    if(data1.status == 'success') {
                        $(".funidlist",parent.document).each(function (i,n){
                            var fid = $(this).attr('data-fid');
                            if(in_array(fid, data1.funids)) {
                                $(this).addClass("checked");
                                $("#fids"+fid,parent.document).attr("checked","checked");
                                var data_acid = $(this).attr("data-acid");
                                if(!$(".rca"+data_acid,parent.document).hasClass("checked")) {
                                    $("#acmenu"+data_acid,parent.document).attr("checked","checked");
                                    $(".rca"+data_acid,parent.document).addClass("checked");
                                }
                            } else {
                                $(this).removeClass("checked");
                            }
                        });
                    }
                }
                $.post(url1,data1,fun1,'json');
                parent.layer.open({
                    type: 1,
                    title:'绑定通道【上游账户名称：'+show_name+'】',
                    area: ['680px', '500px'], //宽高
                    content: $('#channel_set_box',parent.document),
                    btn:['确定', '取消'],
                    yes: function(){
                        var url = '/index.php/Admin/ChannelAccount/add_channel';
                        var data=$("form[name = 'channel_set_form']",parent.document).serialize() + '&account_id='+account_id;
                        var fun = function(data){
                            if(data.status == 'success') {
                                parent.layer.closeAll();
                                location.reload();
                            }
                            alertbox(data);
                        }
                        $.post(url,data,fun,'json');
                    }
                });
            }
        });
    });

    /*新增企业资金划拨申请单*/
    $('.enterpriseTransfer_add_btn').click(function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseTransfer/add ',function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'新增企业资金划拨申请单',
                    area: ['405px', '400px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("enterpriseTransfer_add_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(index, layero){
                        if(!checkform('enterpriseTransfer_add_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/EnterpriseTransfer/insert';
                        var data = $("form[name='enterpriseTransfer_add_form']",parent.document).serialize();
                        var fun = function(data){
                            if(data.status == 'success') {
                                parent.layer.closeAll();
                                apply_confirm(data.msg,data.info,"/index.php/Admin/EnterpriseTransfer/insert?operate=send");
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

    /*企业资金划拨申请单送审*/
    $('.enterpriseTransfer_send_function').click(function(){
        var apply_id=$(this).attr('value');
        apply_confirm('',apply_id,"/index.php/Admin/EnterpriseTransfer/send");
    });


    /*编辑企业资金划拨申请单*/
    $('.enterpriseTransfer_edit_function').click(function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        var  apply_id = $(this).attr('value');
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseTransfer/edit?apply_id='+apply_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'编辑企业资金划拨申请单',
                    area: ['405px', '400px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("enterprise_transfer_edit_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(index, layero){
                        if(!checkform('enterprise_transfer_edit_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/EnterpriseTransfer/update';
                        var data = $("form[name='enterprise_transfer_edit_form']",parent.document).serialize();
                        var fun = function(data){
                            if(data.status == 'success') {
                                parent.layer.closeAll();
                                apply_confirm(data.msg,apply_id,"/index.php/Admin/EnterpriseTransfer/update?operate=send");
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

    /*企业资金划拨复审和初审*/

    $('.enterpriseTransfer_approve_function').click(function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        var apply_id = $(this).attr('value');
        var approve_c = $(this).data('approve');
        var title='';
        if(approve_c=='approve_c'){
            title='企业资金划拨申请复审';
        }
        if(approve_c=='approve'){
            title='企业资金划拨申请初审';
        }
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseTransfer/'+approve_c+'?apply_id='+apply_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:title,
                    area: ['680px', '430px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("enterpriseTransfer_approve_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(index, layero){
                        if(!checkform('enterpriseTransfer_approve_form')){
                            return false;
                        }
                        var url='';
                        var data = $("form[name='enterpriseTransfer_approve_form']",parent.document).serialize();
                        if(approve_c=='approve_c' ){
                            url= '/index.php/Admin/EnterpriseTransfer/'+approve_c+'?operate=approve&tran=trans';
                        }else{
                            url = '/index.php/Admin/EnterpriseTransfer/'+approve_c+'?operate=approve';
                        }
                        var fun = function(data){
                            if(data.status == 'success') {
                                if(approve_c=='approve_c'){
                                    if(!data.info){
                                        alertbox(data);
                                        parent.layer.closeAll();
                                        location.reload();
                                    }else{
                                        parent.layer.closeAll();
                                        enterprise_transfer(data.msg,data.info,'/index.php/Admin/EnterpriseTransfer/'+approve_c+'?operate=approve');
                                    }
                                }else{
                                    alertbox(data);
                                    parent.layer.closeAll();
                                    location.reload();
                                }
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
    /*企业账户资金划拨申请单信息*/
    $('.enterpriseTransfer_detailed_function').on('click',function(){
        var apply_id = $(this).attr('value');
        var title = '企业资金划拨申请信息';
        var area =  ['600px', '450px'];
        var view_name = 'detail_box';
        var view_url = '/index.php/Admin/EnterpriseTransfer/show?apply_id='+apply_id;
        view_order(title,area,view_name,view_url);
    });

    /*企业资金划拨申请单送审*/
    /*$('.enterpriseTransfer_send_function').click(function(){
        var apply_id=$(this).attr('value');
        apply_confirm('',apply_id,"/index.php/Admin/EnterpriseTransfer/send");
    });*/
    /*企业账户资金划拨申请单删除*/
    $('.enterpriseTransfer_delete_function').click(function(){
        var apply_id=$(this).attr('value');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否删除该划拨申请单？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url = '/index.php/Admin/EnterpriseTransfer/delete';
            var data ={apply_id:apply_id};
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



});
//用于给用户分配通道权限
$(function(){
    /**
     *  点击管理员获取当前管理员的资源方
     */
    $('tr.set_channel_info_user').on('click',function(){
        $('.radio.checked').removeClass('checked');
        $(this).children('td').children('label.radio').addClass('checked');
        var val = $(this).attr('value');
        var url = '/index.php/Admin/ChannelRole/set_channel_info_user_rights_list_ajax?user_id='+val;
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
                        +data.data.no[i]["channel_id"]+
                        '"><em></em></label></td><td>'
                        +data.data.no[i]['sort_no']+
                        '</td><td>'
                        +data.data.no[i]["channel_code"]+
                        '</td><td>'
                        +data.data.no[i]["channel_name"]+
                        '</td><td>'
                        +data.data.no[i]["province_name"]+
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
                        +data.data.have[i]["channel_id"]+
                        '"><em></em></label></td><td>'
                        +data.data.have[i]['sort_no']+
                        '</td><td>'
                        +data.data.have[i]["channel_code"]+
                        '</td><td>'
                        +data.data.have[i]["channel_name"]+
                        '</td><td>'
                        +data.data.have[i]["province_name"]+
                        '</td></tr>';

                    have_html = have_html + html;
                }
                $("tbody.have_list").html('').append(have_html);
                if(data.data.have_all == 1){
                    $(".set_channel_info_user.delete").show();
                    $(".set_channel_info_user.add").hide();
                }else{
                    $(".set_channel_info_user.delete").hide();
                    $(".set_channel_info_user.add").show();
                }

            }else{
                alertbox(data);
            }


        }
        $.get(url,data,fun,'json');

    })


    /**
     *  执行添加通道权限
     */
    $('.set_channel_info_user.rightarrow').on('click',function(){
        var ids = '';
        var objlist = $("tbody.no_list label.checked");
        var count = $("tbody.no_list label.checked").length;
        for(var i = 0;i< count ;i++){
            ids += ','+$(objlist).eq(i).attr('value');
        }
        ids = ids.substr(1,(ids.length)-1);
        if(!ids)return false;
        var user_id = $("tbody.user_list .checked").attr('value');
        var url = '/index.php/Admin/ChannelRole/set_channel_info_add_some_rights';
        var data = {user_id:user_id,channel_ids:ids};
        var fun = function(data){
            if(data.status == 'success'){
                location.href="/index.php/Admin/ChannelRole/set_channel_info_user_rights_list?user_id="+user_id;
            }
            alertbox(data);

        }
        $.post(url,data,fun,'json');

    })


    /**
     *  执行删除通道的权限
     */
    $('.set_channel_info_user.leftarrow').on('click',function(){
        var ids = '';
        var objlist = $("tbody.have_list label.checked");
        var count = $("tbody.have_list label.checked").length;
        for(var i = 0;i< count ;i++){
            ids += ','+$(objlist).eq(i).attr('value');
        }
        ids = ids.substr(1,(ids.length)-1);
        if(!ids)return false;
        var user_id = $("tbody.user_list .checked").attr('value');
        var url = '/index.php/Admin/ChannelRole/set_channel_info_del_some_rights';
        var data = {user_id:user_id,channel_ids:ids};
        var fun = function(data){
            if(data.status == 'success'){
                location.href="/index.php/Admin/ChannelRole/set_channel_info_user_rights_list?user_id="+user_id;
            }
            alertbox(data);

        }
        $.post(url,data,fun,'json');

    })



    /**
     *  执行删除用户对所有通道的权限
     */

    $('.set_channel_info_user.delete').on('click',function(){
        var user_id = $("tbody.user_list .checked").attr('value');
        if(!user_id){return false;}
        var url = '/index.php/Admin/ChannelRole/set_channel_info_del_all_rights';
        var data = {user_id:user_id}
        var fun = function(data){


            if(data.status == 'success'){
                layer.alert('<i class="confirm_icon"></i>'+data.msg,{title:'提示信息'});
                var url = '/index.php/Admin/ChannelRole/set_channel_info_user_rights_list_ajax?user_id='+user_id;
                var data = {};
                var fun = function(data){

                    if(data.status == 'success'){
                        var no_html = '';
                        for(var i=0;i < data.data.no.length; i++ ){
                            if(!data.data.no[i]["user_name"]){
                                data.data.no[i]["user_name"] = '';
                            }
                            var html = '<tr><td><label class="checkbox" value="'
                                +data.data.no[i]["channel_id"]+
                                '"><em></em></label></td><td>'
                                +data.data.no[i]['sort_no']+
                                '</td><td>'
                                +data.data.no[i]["channel_code"]+
                                '</td><td>'
                                +data.data.no[i]["channel_name"]+
                                '</td><td>'
                                +data.data.no[i]["province_name"]+
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
                                +data.data.have[i]["channel_id"]+
                                '"><em></em></label></td><td>'
                                +data.data.have[i]['sort_no']+
                                '</td><td >'
                                +data.data.have[i]["channel_code"]+
                                '</td><td>'
                                +data.data.have[i]["channel_name"]+
                                '</td><td>'
                                +data.data.have[i]["province_name"]+
                                '</td></tr>';

                            have_html = have_html + html;
                        }
                        $("tbody.have_list").html('').append(have_html);

                        $(".set_channel_info_user.delete").hide();
                        $(".set_channel_info_user.add").show();

                    }
                }
                $.get(url,data,fun,'json');
            }else{
                alertbox(data);
            }
        }
        $.post(url,data,fun,'json');

    })


    /**
     *  执行添加用户对所有通道的权限
     */

    $('.set_channel_info_user.add').on('click',function(){
        var user_id = $("tbody.user_list .checked").attr('value');
        if(!user_id){return false;}
        var url = '/index.php/Admin/ChannelRole/set_channel_info_add_all_rights';
        var data = {user_id:user_id}
        var fun = function(data){
            if(data.status == 'success'){
                $("tbody.have_list").html('');
                $("tbody.no_list").html('');
                $(".set_channel_info_user.add").hide();
                $(".set_channel_info_user.delete").show();
                layer.alert('<i class="confirm_icon"></i>'+data.msg,{title:'提示信息'});
            }else{
                alertbox(data);
            }

        }
        $.post(url,data,fun,'json');

    });

    //查询通道编码和通道名称
    $('.seach_set_right_code').on('click',function(){
        var name = $(this).attr('data');
        var listname = $("input[name='"+name+"']").attr('data');
        var value = $("input[name='"+name+"']").val();
        if(!value){
            $("tbody."+listname+' tr').show();
        }else{
            $("tbody."+listname+' tr').hide(); //循环tr里面的td
            var num = $(this).attr('value');  //指定通道数据第几个td查询
            for(var i = 0; i< num.length;i++){
                var x = parseInt(num[i]);
                if(x){
                    $("tbody."+listname+' tr').each(function(i,e){
                        var val = $(e).children('td').eq(x).html().indexOf(value);  //查询第二个td的数据，通道编码
                         var val_name = $(e).children('td').eq(x+1).html().indexOf(value);//查询第二个td的数据，通道名称
                        if( val >= 0 || val_name>=0){
                            $(e).show();
                        }
                    })
                }
            }
        }

    });

});




/*搜索通道*/
function seach_set_channel(){
    var seach_input = $("#seach_input").val();
    if(!seach_input){
        $(".seach_channelall").each(function(){
                $(this).show();
        });
    }else{
        $(".seach_channelall",parent.document).each(function(){
           var newval = $(this).html();
           if(newval.indexOf(seach_input) > -1){
              $(this).show();
           }else{
             $(this).hide();
           }
        });

    }
}
/*重置内容*/
function seach_reset_channel(){
    $("#seach_input").val('');
    $(".seach_channelall").each(function(){$(this).show();});
}



/*
* url 方法地址
* formData 数据
* */
function to_url(url,formData,checkforms){
    if(!checkform(checkforms)){
        return false;
    }
    $.ajax({
        url: url ,
        type: 'POST',
        data: formData,
        async: false,
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) {
            if(data.status == 'success'){
                location.reload();
                parent.layer.closeAll();
            }
        },
        error: function (data) {
            alertbox(data);
        }
    });
}

/*function apply_confirm(content,id,applyurl){
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
}*/


function view_order(title,area,view_name,view_url){
    var  load = parent.layer.load(0, {shade: [0.3,'#000']});
    $("#layerdivid",top.document).load(view_url,function(data){
        top.layer.close(load);
        if(is_layer(data)){
            top.layer.open({
                type: 1,
                title:title,
                area: area , //宽高
                content: $("#"+view_name,top.document),
                btn:['关闭'],
                yes: function(){
                    top.layer.closeAll();
                }
            });
        }
    })
}

function enterprise_account(content){
    var info = eval('('+content+')');
    var id=$(objthis).attr("data-id");
    var e_id=$('#'+id).val();
    if(e_id){
        var url = '/index.php/Admin/EnterpriseTransfer/show?operate=account';
        var data ={enterprise_id:e_id} ;
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

function enterprise_ticket(content){
    var info = eval('('+content+')');
    var id=$(objthis).attr("data-id");
    var e_id=$('#'+id).val();
    if(e_id){
        var url = '/index.php/Admin/EnterpriseTicket/show?operate=ticket';
        var data ={enterprise_id:e_id} ;
        var fun = function(data){
            if(data.status == 'success') {
                $("#"+info.div1).css({'display':'block'});
                $("#"+info.div2).html(data.info.can_ticket_money); //可开票金额
                $("#"+info.div3).val(data.info.contact_name);   //联系人
                $("#"+info.div4).val(data.info.contact_tel);   //联系电话
                $("#"+info.div5).val(data.info.contact_province_id);  //省id
                $("#"+info.div6).html(data.info.contact_city_id);   //市id
                $("#"+info.div7).val(data.info.contact_address);   //详细地址
                province_city();
            }else{
                alertbox(data);
            }
        }
        $.post(url,data,fun,'json');
    }

}





