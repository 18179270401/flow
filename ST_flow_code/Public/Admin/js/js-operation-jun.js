$(function(){
  $("#searchbtn").click(function(){
    $("#myformid1").submit();
  })
        var loading= $(".loadingDiv");
        loading.hide();
  //部门添加
  $(".depart_add_btn").on('click',function(){
      var  load = parent.layer.load(0, {shade: [0.3,'#000']});
  $("#layerdivid",parent.document).load('/index.php/Admin/Depart/add',function(data){
      parent.layer.close(load);
      if(is_layer(data)) {
      parent.layer.open({
            type: 1,
            title:'新增部门',
            area: ['410px', '290px'], //宽高
            content: $('#depart_add_box',parent.document),
            success:function(){
                inputFocus("depart_add_form");
            },
            btn:['保存', '取消'],
            yes: function(){
              if(!checkform('depart_add_form')){
                return false;
              }
              var url = '/index.php/Admin/Depart/insert';
              var data = $("form[name='depart_add_form']",parent.document).serialize();
              var fun = function(data){
                  if(data.status == 'success') {
                    location.reload();
                    parent.layer.closeAll();
                  }
                  alertbox(data);
              }
              $.post(url,data,fun,'json');
            }
          });
    }
  })
  });

  //部门编辑
  $(".depart_edit_btn").on('click',function(){
  var depart_id = $(this).attr('value');
      var  load = parent.layer.load(0, {shade: [0.3,'#000']});
  $("#layerdivid",parent.document).load('/index.php/Admin/Depart/edit?depart_id='+depart_id,function(data){
      parent.layer.close(load);
      if(is_layer(data)) {
      parent.layer.open({
            type: 1,
            title:'编辑部门',
            area: ['410px', '250px'], //宽高
            content: $('#depart_edit_box',parent.document),
            success:function(){
                inputFocus("depart_edit_form");
            },
            btn:['确定', '取消'],
            yes: function(){
              if(!checkform('depart_edit_form')){
                return false;
              }
              var url = '/index.php/Admin/Depart/update';
              var data = $("form[name='depart_edit_form']",parent.document).serialize();
              var fun = function(data){
                  if(data.status == 'success') {
                    location.reload();
                    parent.layer.closeAll();
                  }
                  alertbox(data);
              }
              $.post(url,data,fun,'json');
            }
          });
    }
  })
  });
  //部门删除
  $(".depart_delete_btn").on('click',function(){
    var depart_id = $(this).attr('value');
    var depart_name = $(this).attr("data-value");
    parent.layer.confirm('<i class="confirm_icon"></i>确定是否删除部门【'+depart_name+'】？', {
        title:"提示信息",
        btn: ['确定','取消'] //按钮
      }, function(){
      var url ='/index.php/Admin/Depart/delete';
      var data ={'depart_id':depart_id};
      var fun = function(data){
        if(data.status == 'success') {
          parent.layer.closeAll();
          location.reload();
          alertbox(data);
        }else if(data.info){
          parent.layer.closeAll();
          enterprise_transfer(data.msg,data.info,"/index.php/Admin/Depart/delete?conf=confirm");
        }else{
          parent.layer.closeAll();
          alertbox(data);
        }
      }
      $.post(url,data,fun,'json');
    });
  });


  //代理商新增提现申请
  $(".proxywithdrawals_add_btn").on('click',function(){
      var  load = parent.layer.load(0, {shade: [0.3,'#000']});
    $("#layerdivid",parent.document).load('/index.php/Admin/ProxyWithdrawals/add',function(data){
        parent.layer.close(load);
        if(is_layer(data)) {
      parent.layer.open({
            type: 1,
            title:'新增提现申请',
            area: ['410px', '410px'], //宽高
            content: $('#proxywithdrawals_add_box',parent.document),
            success:function(){
                inputFocus("proxywithdrawals_add_form");
            },
            btn:['保存', '取消'],
            yes: function(){
              if(!checkform('proxywithdrawals_add_form')){
                return false;
              }
              var url = '/index.php/Admin/ProxyWithdrawals/insert';
              var data = $("form[name='proxywithdrawals_add_form']",parent.document).serialize();
              var fun = function(data){
                  if(data.status == 'success') {
                    parent.layer.closeAll();
                    apply_confirm(data.msg,data.info,"/index.php/Admin/ProxyWithdrawals/insert?operation=giveapply");
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
    /*修改代理商提现申请*/
    $(".ProxyWithdrawals_edit_function").on('click',function(){
        var apply_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyWithdrawals/edit?apply_id='+apply_id,function(data){
            parent.layer.close(load);
        if(is_layer(data)) {
          parent.layer.open({
                type: 1,
                title:'编辑提现申请',
                area: ['410px', '410px'], //宽高
                content: $('#proxywithdrawals_edit_box',parent.document),
                success:function(){
                    inputFocus("proxywithdrawals_edit_form");
                },
                btn:['确定', '取消'],
                yes: function(){
                  if(!checkform('proxywithdrawals_edit_form')){
                    return false;
                  }
                  var url = '/index.php/Admin/ProxyWithdrawals/update';
                  var data = $("form[name='proxywithdrawals_edit_form']",parent.document).serialize();
                  var fun = function(data){
                      if(data.status == 'success') {
                        parent.layer.closeAll();
                        apply_confirm(data.msg,apply_id,"/index.php/Admin/ProxyWithdrawals/insert?operation=giveapply");
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
    /*送审*/
    $(".ProxyWithdrawals_apply_function").on("click",function(){
        var apply_id = $(this).attr('value');
        parent.layer.confirm('<i class="confirm_icon"></i>现在要提交审核吗？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/ProxyWithdrawals/send_approve';
            var data ={'id':apply_id};
            var fun = function(data){
                if(data.status == 'success') {
                    parent.layer.closeAll();
                    parent.update_account();
                    location.reload();
                }
                alertbox(data);
            }
            $.post(url,data,fun,'json');
        });
    });
    /*删除代理商充值申请信息*/
    $(".ProxyWithdrawals_delete_function").on("click",function(){
        var apply_id = $(this).attr('value');
        var apply_code=$(this).data('deletemsg');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否删除该申请单？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/ProxyWithdrawals/delete';
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

  //提现申请审核
  $(".proxywithdrawals_approve_btn").on('click',function(){
    var apply_id= $(this).attr('value');
    var approve_f = $(this).attr('data-value');
      var  load = parent.layer.load(0, {shade: [0.3,'#000']});
      $("#layerdivid",parent.document).load('/index.php/Admin/ProxyWithdrawals/detailed?approve_f='+approve_f+'&apply_id='+apply_id,function(data){
        parent.layer.close(load);
    if(is_layer(data)) {
      parent.layer.open({
            type: 1,
            title:'代理商提现审核',
            area: ['680px', '430px'], //宽高
            content: $('#proxywithdrawals_approve_box',parent.document),
            success:function(){
                inputFocus("proxywithdrawals_approve_form");
            },
            btn:['确定', '取消'],
            yes: function(){
              if(!checkform('proxywithdrawals_approve_form')){
                return false;
              }
              var url = '/index.php/Admin/ProxyWithdrawals/'+approve_f+"?tran=trans";
              var data = $("form[name='proxywithdrawals_approve_form']",parent.document).serialize();
              var fun = function(data){
                if(data.status == 'success') {
                  if(!data.info){
                      alertbox(data);
                      parent.layer.closeAll();
                      location.reload();
                  }else{
                      parent.layer.closeAll();
                      enterprise_transfer(data.msg,data.info,'/index.php/Admin/ProxyWithdrawals/'+approve_f);
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

//代理商端 提现申请审核
  $(".proxywithdr_approve_btn").on('click',function(){
    var apply_id= $(this).attr('value');
    var approve_f = $(this).attr('data-value');
      var  load = parent.layer.load(0, {shade: [0.3,'#000']});
      $("#layerdivid",parent.document).load('/index.php/Admin/ProxyWithdr/detailed?approve_f='+approve_f+'&apply_id='+apply_id,function(data){
        parent.layer.close(load);
    if(is_layer(data)) {
      parent.layer.open({
            type: 1,
            title:'代理商提现审核',
            area: ['680px', '430px'], //宽高
            content: $('#proxywithdr_approve_box',parent.document),
            success:function(){
                inputFocus("proxywithdr_approve_form");
            },
            btn:['确定', '取消'],
            yes: function(){
              if(!checkform('proxywithdr_approve_form')){
                return false;
              }
              var url = '/index.php/Admin/ProxyWithdr/'+approve_f+"?tran=trans";
              var data = $("form[name='proxywithdr_approve_form']",parent.document).serialize();
              var fun = function(data){
                if(data.status == 'success') {
                  if(!data.info){
                      alertbox(data);
                      parent.layer.closeAll();
                      location.reload();
                  }else{
                      parent.layer.closeAll();
                      enterprise_transfer(data.msg,data.info,'/index.php/Admin/ProxyWithdr/'+approve_f);
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


  /*代理商提现申请 查看详细*/
    $(".proxywithdrawals_detailed_btn").on('click',function(){
        var apply_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyWithdrawals/show?apply_id='+apply_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'代理商提现信息',
                    area: ['600px', '340px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });

    /*代理商提现管理 查看详细*/
    $(".proxywithdr_detailed_btn").on('click',function(){
        var apply_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyWithdr/show?apply_id='+apply_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'代理商提现信息',
                    area: ['600px', '340px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });


    //企业新增提现申请
  $(".enterpriseapply_add_btn").on('click',function(){
      var  load = parent.layer.load(0, {shade: [0.3,'#000']});
  $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseApply/add',function(data){
      parent.layer.close(load);
      if(is_layer(data)) {
      parent.layer.open({
        type: 1,
        title:'新增提现申请',
        area: ['410px', '400px'], //宽高
        content: $('#enterpriseapply_add_box',parent.document),
        success:function(){
            inputFocus("enterpriseapply_add_form");
        },
        btn:['保存', '取消'],
        yes: function(){
          if(!checkform('enterpriseapply_add_form')){
            return false;
          }
          var url = '/index.php/Admin/EnterpriseApply/insert';
          var data = $("form[name='enterpriseapply_add_form']",parent.document).serialize();
          var fun = function(data){
              if(data.status == 'success'){
                  parent.layer.closeAll();
                  apply_confirm(data.msg,data.info,"/index.php/Admin/EnterpriseApply/edit?operates=update&operate=send");
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


    /*企业提现修改*/
    $('.enterpriseapply_edit_function').click(function(){
        var apply_id=$(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseApply/edit?apply_id='+apply_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'编辑提现申请',
                    area: ['400px', '400px'], //宽高
                    content: $('#enterpriseapply_add_box',parent.document),
                    success:function(){
                        inputFocus("enterpriseapply_add_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(index, layero){
                        if(!checkform('enterpriseapply_add_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/EnterpriseApply/edit?operates=update';
                        var data =$("form[name='enterpriseapply_add_form']",parent.document).serialize();
                        var fun = function(data){
                            if(data.status == 'success'){
                                parent.layer.closeAll();
                                apply_confirm(data.msg,apply_id,"/index.php/Admin/EnterpriseApply/edit?operates=update&operate=send");
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
    /*企业端提现申请送审*/
    $('.enterpriseapply_send_function').click(function(){
        var apply_id=$(this).attr('value');
        apply_confirm('',apply_id,"/index.php/Admin/EnterpriseApply/edit?operates=update&operate=send");
    });

    /*企业端提现申请删除*/
    $('.enterpriseapply_delete_function').click(function(){
        var apply_id=$(this).attr('value');
        var apply_code=$(this).data('deleteMsg');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否删除该申请单？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url = '/index.php/Admin/EnterpriseApply/delete';
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

  /*企业查看详细*/
    $(".enterpriseapply_detailed_btn").on('click',function(){
        var apply_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseApply/show?apply_id='+apply_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'企业提现信息',
                    area: ['600px', '400px'], //宽高
                    // area: ['680px', '546px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });

    //企业提现申请审核
  $(".enterprisewithdrawals_approve_btn").on('click',function(){
    var apply_code= $(this).attr('value');
    var func=$(this).attr('data-toggle');
      var  load = parent.layer.load(0, {shade: [0.3,'#000']});
  $("#layerdivid",parent.document).load('/index.php/Admin/'+func+'?apply_code='+apply_code,function(data){
      parent.layer.close(load);
    if(is_layer(data)) {
      parent.layer.open({
            type: 1,
            title:'企业提现审核',
            area: ['680px', '400px'], //宽高
            content: $('#enterprisewithdrawals_approve_box',parent.document),
            success:function(){
                inputFocus("enterprisewithdrawals_approve_form");
            },
            btn:['确定', '取消'],
            yes: function(){
              if(!checkform('enterprisewithdrawals_approve_form')){
                return false;
              }
              var url = '/index.php/Admin/EnterpriseWithdrawals/enterprise_approve?tran=trans';
              var data = $("form[name='enterprisewithdrawals_approve_form']",parent.document).serialize();
              var fun = function(data){
                  if(data.status == 'success') {
                    if(!data.info){
                      alertbox(data);
                      parent.layer.closeAll();
                      location.reload();
                    }else{
                      parent.layer.closeAll();
                      enterprise_transfer(data.msg,data.info,'/index.php/Admin/EnterpriseWithdrawals/enterprise_approve');
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
    /*企业查看详细*/
    $(".enterprisewithdrawals_detailed_btn").on('click',function(){
        var apply_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseWithdrawals/show?apply_id='+apply_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'企业提现信息',
                    area: ['600px', '350px'], //宽高
                    // area: ['680px', '546px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });

    /*企业详细*/
    $(".enterpriseaccount_eninfo_btn").on('click',function(){
        var enterprise_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseAccount/eninfo?enterprise_id='+enterprise_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'企业账户信息',
                    area: ['400px', '300px'], //宽高
                    // area: ['680px', '546px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });

      //代理商户划拨
  $(".enterprise_transfer_but").on('click',function(){
    var enterprise_id= $(this).attr('value');
      var  load = parent.layer.load(0, {shade: [0.3,'#000']});
      $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseAccount/recharg_money?enterprise_id='+enterprise_id,function(data){
      parent.layer.close(load);
      if(is_layer(data)) {
      parent.layer.open({
            type: 1,
            title:'企业账户充值',
            area: ['360px', '500'], //宽高
            content: $('#add_box',parent.document),
            success:function(){
                inputFocus("enterpriseAccount_transfer_form");
            },
            btn:['确定', '取消'],
            yes: function(){
              if(!checkform('enterpriseAccount_transfer_form')){
                return false;
              }
              var url = '/index.php/Admin/EnterpriseAccount/transfer?tran=qr';
              var data = $("form[name='enterpriseAccount_transfer_form']",parent.document).serialize();
              var fun = function(data){
                  if(data.status == 'success') {
                      parent.layer.closeAll();
                      enterprise_transfer(data.msg,data.info,"/index.php/Admin/EnterpriseAccount/transfer");
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

      //代理商收回
  $(".enterprise_back_but").on('click',function(){
    var enterprise_id= $(this).attr('value');
      var  load = parent.layer.load(0, {shade: [0.3,'#000']});
      $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseAccount/return_money?enterprise_id='+enterprise_id+'&type=back',function(data){
          parent.layer.close(load);
    if(is_layer(data)) {
      parent.layer.open({
            type: 1,
            title:'企业资金收回',
            area: ['360px', '510px'], //宽高
            content: $('#add_box',parent.document),
            success:function(){
                inputFocus("enterpriseAccount_transfer_form");
            },
            btn:['确定', '取消'],
            yes: function(){
              if(!checkform('enterpriseAccount_transfer_form')){
                return false;
              }
              var url = '/index.php/Admin/EnterpriseAccount/transfer?tran=qr';
              var data = $("form[name='enterpriseAccount_transfer_form']",parent.document).serialize();
              var fun = function(data){
                  if(data.status == 'success') {
                      parent.layer.closeAll();
                      enterprise_transfer(data.msg,data.info,"/index.php/Admin/EnterpriseAccount/transfer");
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
  //企业账户冻结
  $(".enterprise_freeze_but").on('click',function(){
    var enterprise_id= $(this).attr('value');
      var  load = parent.layer.load(0, {shade: [0.3,'#000']});
      $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseAccount/freeze_money?enterprise_id='+enterprise_id,function(data){
          parent.layer.close(load);
    if(is_layer(data)) {
      parent.layer.open({
            type: 1,
            title:'企业资金冻结',
            area: ['360px', '390px'], //宽高
            content: $('#add_box',parent.document),
            success:function(){
                inputFocus("enterpriseAccount_freeze_form");
            },
            btn:['确定', '取消'],
            yes: function(){
              if(!checkform('enterpriseAccount_freeze_form')){
                return false;
              }
              var url = '/index.php/Admin/EnterpriseAccount/transfer?tran=qr';
              var data = $("form[name='enterpriseAccount_freeze_form']",parent.document).serialize();
              var fun = function(data){
                  if(data.status == 'success') {
                      parent.layer.closeAll();
                      enterprise_transfer(data.msg,data.info,"/index.php/Admin/EnterpriseAccount/transfer");
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

  /*企业充值详细——尚通运营端*/
    $(".enterprisedetailed_btn").on('click',function(){
        var record_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseDetails/show?record_id='+record_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'企业充值明细信息',
                    area: ['600px', '350px'], //宽高
                    // area: ['680px', '546px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });

    /*企业提现详细——尚通运营端*/
    $(".enterprise_withdraw_detailed_btn").on('click',function(){
        var record_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseWithdrawDetails/show?record_id='+record_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'企业提现明细信息',
                    area: ['600px', '350px'], //宽高
                    // area: ['680px', '546px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });
    $(".export_button").on('click',function(){
        var  load = top.layer.load(0, {shade: [0.3,'#000']});
        var func=$(this).data('url');
        var type=$(this).data('type');
        var url='';
        if(type=='url'){
            url= '/index.php/Admin/'+func+"/mathran/"+Math.random();
        }else{
            url='/index.php/Admin/'+func+'/export_excel'+"/mathran/"+Math.random();
        }
        var index_url = $("form[name='excel']").attr('action');
        $("form[name='excel']").attr('action',url);
        $("form[name='excel']").submit();
        $("form[name='excel']").attr('action',index_url);
        top.layer.close(load);
    });
    $(".scene_activity_set_btn").on('click',function(){
        var user_activity_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load("/index.php/Admin/SceneActivity/set?user_activity_id="+user_activity_id+"&type=show",function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'配置流量包类型',
                    area: ['680px', '490px'], //宽高
                    // area: ['680px', '546px'], //宽高
                    content: $('#types_box',parent.document),
                    success:function(){
                        inputFocus("sceneactivity_set_form");
                        setTimeout(function(){
                          parent.document.getElementById('profile').setAttribute("class","tab-pane");
                          parent.document.getElementById('messages').setAttribute("class","tab-pane");
                        }, 100);
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform("sceneactivity_set_form")){
                            return false;
                        }
                        var url = '/index.php/Admin/SceneActivity/set?type=update';
                        var data = $("form[name='sceneactivity_set_form']",parent.document).serialize();
                        var fun = function(data){
                            if(data.status == 'success') {
                                alertbox(data);
                                parent.layer.closeAll();
                                location.reload();
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

    $(".scene_activity_lbs_btn").on('click',function(){
        var user_activity_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load("/index.php/Admin/SceneActivity/map_lbs?user_activity_id="+user_activity_id+"&type=show",function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'LBS定制',
                    area: ['680px', '490px'], //宽高
                    // area: ['680px', '546px'], //宽高
                    content: $('#types_box',parent.document),
                    success:function(){
                        inputFocus("sceneactivity_lbs_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform("sceneactivity_lbs_form")){
                            return false;
                        }
                        var url = '/index.php/Admin/SceneActivity/set?type=update';
                        var data = $("form[name='sceneactivity_set_form']",parent.document).serialize();
                        var fun = function(data){
                            if(data.status == 'success') {
                                alertbox(data);
                                parent.layer.closeAll();
                                location.reload();
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
    //企业权限配置
    $(".enterprise_role_btn").on('click',function(){
        var enterprise_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load("/index.php/Admin/CollectionSet/enterprise_role?enterprise_id="+enterprise_id+"&type=show",function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'权限设置',
                    area: ['550px', '370px'], //宽高
                    // area: ['680px', '546px'], //宽高
                    content: $('#enterprise_role_box',parent.document),
                    success:function(){
                        inputFocus("enterprise_role_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform("enterprise_role_form")){
                            return false;
                        }
                        var url = '/index.php/Admin/CollectionSet/enterprise_role?type=update';
                        var data = $("form[name='enterprise_role_form']",parent.document).serialize();
                        var fun = function(data){
                            if(data.status == 'success') {
                                alertbox(data);
                                parent.layer.closeAll();
                                location.reload();
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
    //企业编辑收款
    $(".edit_collection_btn").on('click',function(){
        var enterprise_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load("/index.php/Admin/CollectionSet/edit_collection?enterprise_id="+enterprise_id+"&type=show",function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'设置收款',
                    area: ['680px', '490px'], //宽高
                    // area: ['680px', '546px'], //宽高
                    content: $('#edit_collection_box',parent.document),
                    success:function(){
                        inputFocus("edit_collection_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform("edit_collection_form")){
                            return false;
                        }
                        var url = '/index.php/Admin/CollectionSet/edit_collection?type=update';
                        var data = $("form[name='edit_collection_form']",parent.document).serialize();
                        var fun = function(data){
                            if(data.status == 'success') {
                                alertbox(data);
                                parent.layer.closeAll();
                                location.reload();
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
    $(document).on('change',".file_edit_sence",function(){
        var id =$(this).attr("id");
        var val=$("#"+id).val();
        if(val!="" && val!=null){
            set_collection(id);
        }
    });

    $(document).on('click',"#menu_checkbox",function(){
        var menuid=$(this).data("menuid");
        var t=$("#role_"+menuid).val();
        if(t==1){
            $("#role_"+menuid).val(2);
        }else{
            $("#role_"+menuid).val(1);
        }
    });

    $(document).on('click',"#scene_checkbox",function(){
        var product_id=$(this).data("productid");
        var operator_id=$(this).data("operator_id");
        var probability=$("#probability_"+product_id).val();
        var t=$("#inp_"+product_id).val();
        if(t==1){
            $("#inp_"+product_id).val(2);
        }else{
            $("#inp_"+product_id).val(1);
        }
    });
    $(document).on('click',"#wxtype",function(){
        var type=$(this).data("value");
        $("#wx_type").val(type);
    });

    /*$(document).on('change',".probability",function(){
        var product_id=$(this).data("productid");
        var operator_id=$(this).data("operator_id");
        var probability=$(this).val();
        var t=$("#inp_"+product_id).val();
        if(t==2){
            $("#inp_"+product_id).val(2);
            var url="/index.php/Admin/SceneActivity/set/type/update";
            scene_probability(product_id,operator_id,probability,url);
        }
    });*/

    /*$(".operation_a").click(function(){
        var inputname = $(this).data("inputname");
        if(document.getElementById("span_"+inputname)) {
            var inputvalue = $("#span_" + inputname).html();
            var htmls = '<input type="text" class="inputtext" id="inputtextid' + inputname + '" name="' + inputname + '" value="' + inputvalue + '" onblur="editinpueall(\'' + inputname + '\')" style="display: block">';
            $(".show_" + inputname).html(htmls);
        }
    });*/
    /*$(".payment_type").click(function(){
        var payment_type= $(this).data("value");
        $("#payment_type").val(payment_type);
        //editpayment("payment_type",payment_type); //直接保存
    });*/
    /*$(".file_sence").change(function(){
        var id= $(this).attr("id");
        set_add_file(id);
    });*/

    /*删除场景*/
    $(".marketing_delete_btn").on("click",function(){
        var activity_id = $(this).attr('value');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否删除该场景？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/Marketing/delete';
            var data ={activity_id:activity_id};
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

    //查看券兑换记录
    $(".ticket_exchange_show_btn").on('click',function(){
        var id = $(this).attr('value');

        var load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/FlowTicketExchangeRecord/show?redeem_id='+id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'兑换记录信息',
                    area: ['360px', '360px'], //宽高
                    content: $('#ticket_exchange_show_box',parent.document),
                    success:function(){
                        inputFocus("ticket_exchange_show_box");
                    },
                    btn:['关闭']
                });
            }
        });
    });

    //查看购买记录
    $(".pay_order_show_btn").on('click',function(){
        var id = $(this).attr('value');
        var type=$(this).data("type");
        var h="";
        if(type==3){
            h="390px";
        }else{
            h="450px";
        }
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/PayOrderRecord/show?pay_order_id='+id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'购买记录信息',
                    area: ['360px', h], //宽高
                    content: $('#pay_order_show_box',parent.document),
                    success:function(){
                        inputFocus("pay_order_show_box");
                    },
                    btn:['关闭']
                });
            }
        });
    });

    //查看红包记录
    $(".pay_red_show_btn").on('click',function(){
        var id = $(this).attr('value');
        var type=$(this).data("type");
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/PayRedRecord/show?red_order_id='+id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'红包记录信息',
                    area: ['1000px','360px'], //宽高
                    content: $('#pay_red_show_box',parent.document),
                    success:function(){
                        inputFocus("pay_red_show_box");
                    },
                    btn:['关闭']
                });
            }
        });
    });
    
    //营销场景购买记录 再次送充
    $(".pay_order_repaybtn").on('click',function(){
        var pay_order_id= $(this).attr('value');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否再次送充该订单？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/PayOrderRecord/pay_repay';
            var data ={'pay_order_id':pay_order_id};
            var fun = function(data){
                if(data.status == 'success') {
                    parent.layer.closeAll();
                    location.reload();
                    alertbox(data);
                }else if(data.info){
                    parent.layer.closeAll();
                }else{
                    parent.layer.closeAll();
                    alertbox(data);
                }
            }
            $.post(url,data,fun,'json');
        });
    });


    //营销场景购买记录 充值失败退款
    $(".pay_order_refund_btn").on('click',function(){
        var pay_order_id= $(this).attr('value');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否对该订单退款？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/PayOrderRecord/pay_refund';
            var data ={'pay_order_id':pay_order_id};
            var fun = function(data){
                if(data.status == 'success') {
                    parent.layer.closeAll();
                    location.reload();
                    alertbox(data);
                }else if(data.info){
                    parent.layer.closeAll();
                }else{
                    parent.layer.closeAll();
                    alertbox(data);
                }
            }
            $.post(url,data,fun,'json');
        });
    });

    //红包记录 充值失败退款
    $(".pay_red_refund_btn").on('click',function(){
        var red_order_id= $(this).attr('value');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否对该订单退款？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/PayRedRecord/pay_refund';
            var data ={'red_order_id':red_order_id};
            var fun = function(data){
                if(data.status == 'success') {
                    parent.layer.closeAll();
                    location.reload();
                    alertbox(data);
                }else if(data.info){
                    parent.layer.closeAll();
                }else{
                    parent.layer.closeAll();
                    alertbox(data);
                }
            }
            $.post(url,data,fun,'json');
        });
    });

    //查看积分兑换记录
    $(".exchange_show_btn").on('click',function(){
        var id = $(this).attr('value');
        var type=$(this).data("type");
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ExchangeRecord/show?exchange_score_id='+id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'积分兑换信息',
                    area: ['400px','360px'], //宽高
                    content: $('#exchange_box',parent.document),
                    success:function(){
                        inputFocus("exchange_box");
                    },
                    btn:['关闭']
                });
            }
        });
    });
    //充值失败退积分
    $(".exchange_refund_btn").on('click',function(){
        var exchange_score_id= $(this).attr('value');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否对该订单退积分？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/ExchangeRecord/refund_score';
            var data ={'exchange_score_id':exchange_score_id};
            var fun = function(data){
                if(data.status == 'success') {
                    parent.layer.closeAll();
                    location.reload();
                    alertbox(data);
                }else if(data.info){
                    parent.layer.closeAll();
                }else{
                    parent.layer.closeAll();
                    alertbox(data);
                }
            }
            $.post(url,data,fun,'json');
        });
    });
    //活动详细信息
    $(".activity_detailed_btn").on('click',function(){
        var user_activity_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load("/index.php/Admin/SceneActivity/detailed?user_activity_id="+user_activity_id+"&type=show",function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'活动详情配置',
                    area: ['800px', '500px'], //宽高
                    // area: ['680px', '546px'], //宽高
                    content: $('#activity_detailed_box',parent.document),
                    success:function(){
                        inputFocus("activity_detailed_form");
                        activity_b();
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform("activity_detailed_form")){
                            return false;
                        }
                        var url = '/index.php/Admin/SceneActivity/detailed?type=operation';
                        var data = $("form[name='activity_detailed_form']",parent.document).serialize();
                        var fun = function(data){
                            if(data.status == 'success') {
                                alertbox(data);
                                parent.layer.closeAll();
                                location.reload();
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
    //活动场景上传图片相关
    $(document).on("change","#activity_upload1", function(e){ //宣传图
        var file = e.target.files[0];
        activity_preview(file,"upload_img1","upload_phone_img1");
        activity_detailed_add("upload1");
    });
    $(document).on("change","#activity_upload2", function(e){ //logo
        var file = e.target.files[0];
        activity_preview(file,"upload_img2","upload_phone_img2");
        activity_detailed_add("upload2");
    });
    $(document).on("change","#activity_upload3", function(e){
        var file = e.target.files[0];
        activity_preview(file,"upload_img3","phone_inner");
        activity_detailed_add("upload3");
    });
    $(document).on("change","#activity_upload4", function(e){
        var file = e.target.files[0];
        activity_preview(file,"upload_img4","upload_phone_img4");
        activity_detailed_add("upload4");
    });

    //新增自定义回复
    $(".customreply_add_btn").on('click',function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/Customreply/add',function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'新增自定义回复',
                    area: ['400px', '400px'], //宽高
                    content: $('#customreply_add_box',parent.document),
                    success:function(){
                        inputFocus("customreply_add_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(){
                        if(!checkform('customreply_add_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/Customreply/insert';
                        var formData = new FormData($("form[name='customreply_add_form']",parent.document)[0]);
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
                                    alertbox(data);
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
        })
    });
    /*删除自定义回复*/
    $(".customreply_delete_btn").on("click",function(){
        var reply_id = $(this).attr('value');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否删除该自定义回复？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/Customreply/delete';
            var data ={'reply_id':reply_id};
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

    //编辑自定义回复
    $(".customreply_edit_btn").on('click',function(){
        var reply_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/Customreply/edit?reply_id='+reply_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'编辑自定义回复',
                    area: ['400px', '400px'], //宽高
                    content: $('#customreply_edit_box',parent.document),
                    success:function(){
                        inputFocus("customreply_edit_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform('customreply_edit_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/Customreply/update';
                        var formData = new FormData($("form[name='customreply_edit_form']",parent.document)[0]);
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
                                    alertbox(data);
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
        })
    });
    //点击查询弹出load层
    $(".search_btncon .add_btn").on('click',function(){
        add_load();
    });
    //点击分页弹出load层
    $(".page_nav ul li").on('click',function(){
        var cls=$(this).hasClass("active");
        var val=$(this).children("a").html();
        if(val!="..." && !cls){
            add_load();
        }
    });
    $(".get_newsence_url").on('click',function(){
        var account_id = $(this).attr('value');
        var sources=$(this).data('sources');
        var s='';
        if(sources==1){
            s = '流量充值';
        }else{
            s ='网页端充值链接'
        }
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/SceneAccount/show?account_id='+account_id+'&type=add&sources='+sources,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:s,
                    area: ['410px', '150px'], //宽高
                    content: $('#get_scene_url',parent.document),
                    success:function(){
                        inputFocus("get_scene_form");
                    },
                    btn:['生成', '取消'],
                    yes: function(){
                        if(!checkform('get_scene_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/SceneAccount/show';
                        var data = $("form[name='get_scene_form']",parent.document).serialize();
                        var fun = function(data){
                            if(data.status == 'success'){
                                location.reload();
                                top.layer.closeAll();
                                if(data.data != ''){
                                    top.layer.alert(data.data,{title:'提示信息'});
                                }
                            }
                            alertbox(data);
                        }
                        $.post(url,data,fun,'json');
                    }
                });
            }
        })
    });
    //查看充值来源信息
    $(".pay_sources_show_btn").on('click',function(){
        var id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/PaySourcesRecord/show?sources_id='+id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'充值来源信息',
                    area: ['460px','250px'], //宽高
                    content: $('#pay_sources_show_box',parent.document),
                    success:function(){
                        inputFocus("pay_sources_show_box");
                    },
                    btn:['关闭']
                });
            }
        });
    });
    $(".pay_sources_delete_btn").on('click',function(){
        var id = $(this).attr('value');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否删除该充值来源！', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/PaySourcesRecord/delete';
            var data ={'sources_id':id};
            var fun = function(data){
                if(data.status == 'success') {
                    parent.layer.closeAll();
                    location.reload();
                    alertbox(data);
                }else{
                    parent.layer.closeAll();
                    alertbox(data);
                }
            }
            $.post(url,data,fun,'json');
        });
    });

    /*代理商账户提醒额度*/
    $('.set_quota_remind_function').click(function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        var  account_id = $(this).attr('value');
        $("#layerdivid",parent.document).load('/index.php/Admin/ProxyAccount/set_quota_remind?account_id='+account_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'设置代理商账户提醒额度',
                    area: ['405px', '300px'], //宽高
                    content: $('#set_quota_remind_box',parent.document),
                    success:function(){
                        inputFocus("set_quota_remind_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(index, layero){
                        if(!checkform('set_quota_remind_form')){
                            return false;
                        }
                        var data = $("form[name='set_quota_remind_form']",parent.document).serialize();
                        var url= '/index.php/Admin/ProxyAccount/set_quota_remind?operate=ask';
                        var fun = function(data){
                            if(data.status == 'success') {
                                if(!data.info){
                                    alertbox(data);
                                    parent.layer.closeAll();
                                    location.reload();
                                }else{
                                    parent.layer.closeAll();
                                    enterprise_transfer(data.msg,data.info,'/index.php/Admin/ProxyAccount/set_quota_remind?operate=run');
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
    /*企业账户提醒额度*/
    $('.set_e_quota_remind_function').click(function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        var  enterprise_id = $(this).attr('value');
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseAccount/set_quota_remind?enterprise_id='+enterprise_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'设置企业账户提醒额度',
                    area: ['405px', '300px'], //宽高
                    content: $('#set_quota_remind_box',parent.document),
                    success:function(){
                        inputFocus("set_quota_remind_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(index, layero){
                        if(!checkform('set_quota_remind_form')){
                            return false;
                        }
                        var data = $("form[name='set_quota_remind_form']",parent.document).serialize();
                        var url= '/index.php/Admin/EnterpriseAccount/set_quota_remind?operate=ask';
                        var fun = function(data){
                            if(data.status == 'success') {
                                if(!data.info){
                                    alertbox(data);
                                    parent.layer.closeAll();
                                    location.reload();
                                }else{
                                    parent.layer.closeAll();
                                    enterprise_transfer(data.msg,data.info,'/index.php/Admin/EnterpriseAccount/set_quota_remind?operate=run');
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
    //流量码新增
    $(".flowcode_add_btn").on('click',function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load("/index.php/Admin/Flowcode/add",function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'新增流量码',
                    area: ['400px', '300px'], //宽高
                    content: $('#add_flowcode_box',parent.document),
                    success:function(){
                        inputFocus("add_flowcode_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform("add_flowcode_form")){
                            return false;
                        }
                        var url = '/index.php/Admin/Flowcode/insert';
                        var data = $("form[name='add_flowcode_form']",parent.document).serialize();
                        var fun = function(data){
                            if(data.status == 'success') {
                                alertbox(data);
                                parent.layer.closeAll();
                                location.reload();
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
    $(".flowcode_activated").on('click',function(){
        var id = $(this).attr('value');
        var code = $(this).attr("data-name");
        parent.layer.confirm('<i class="confirm_icon"></i>确定激活流量码【'+code+'】！', {
            title:"激活流量码",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/Flowcode/activated';
            var data ={'flowcode_id':id};
            var fun = function(data){
                if(data.status == 'success') {
                    parent.layer.closeAll();
                    location.reload();
                    alertbox(data);
                }else{
                    parent.layer.closeAll();
                    alertbox(data);
                }
            }
            $.post(url,data,fun,'json');
        });
    });
    $(".flowcode_invalid").on('click',function(){
        var id = $(this).attr('value');
        var code = $(this).attr("data-name");
        parent.layer.confirm('<i class="confirm_icon"></i>确定作废流量码【'+code+'】！', {
            title:"作废流量码",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/Flowcode/invalid';
            var data ={'flowcode_id':id};
            var fun = function(data){
                if(data.status == 'success') {
                    parent.layer.closeAll();
                    location.reload();
                    alertbox(data);
                }else{
                    parent.layer.closeAll();
                    alertbox(data);
                }
            }
            $.post(url,data,fun,'json');
        });
    });
    //流量码详情
    $(".show_flowcoderecord__btn").on('click',function(){
        var flowcode_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/FlowcodeRecord/show?flowcode_id='+flowcode_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'流量码兑换信息',
                    area: ['400px', '360px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });
    //代理商、企业结算单导出
    $(".enterprisestatements_excel_btn").on('click',function(){
        var user_id = $(this).val();
        var type=$(this).attr("data-type");
        var stat_month= $(this).attr("data-value");
        location.href='/index.php/Admin/'+type+'/export_excel/user_id/'+user_id+'/stat_month/'+stat_month;
        //top.layer.close(load);
    });
});

function add_load() {
    parent.all_parent_load=parent.layer.load(0, {shade: [0.3,'#000']});
}

function activity_b(){
    var background_img_phone = $("#background_img_phone",parent.document).val();
    $('.phone_inner',parent.document).css("background-image", "url(" + background_img_phone + ")");
}


function activity_preview(file, idadd, bg_idadd) {
    var reader = new FileReader();
    reader.onload = function(e) {
        $('#'+idadd).attr("src", e.target.result);
        if(bg_idadd == "phone_inner"){
            $('.'+bg_idadd).css("background-image", "url("+e.target.result+")");
        } else {
            $('#'+bg_idadd).attr("src", e.target.result);
        }
    }
    reader.readAsDataURL(file);
}

function activity_detailed_add(sid) {
    var formData = new FormData($("form[name='activity_detailed_form']")[0]);
    $.ajax({
        url :"/index.php/Admin/SceneActivity/detailed/type/operation",
        type : 'post',
        data : formData,
        async: false,
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) {
            if(data.status == 'success') {
                //alertbox(data);
                //location.reload();
                $("#"+sid).val("");
            } else {
                alertbox(data);
            }
        },
        error: function (data) {
            alertbox(data);
        }
    });
}

function set_collection(id){
    var formData = new FormData($("form[name='edit_collection_form']")[0]);
    $.ajax({
        url :"/index.php/Admin/CollectionSet/edit_collection/type/update",
        type : 'post',
        data : formData,
        async: false,
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) {
            if(data.status == 'success') {
                //alertbox(data);
                $("#"+id).val("");
                var da=data.data;
                for(var key in da){
                    $("#xs_"+key).html(da[key]);
                }
            } else {
                alertbox(data);
            }
        },
        error: function (data) {
            alertbox(data);
        }
    });
}

function set_add_file(id) {
    var formData = new FormData($("form[name='sceneaccount_add_form1']")[0]);
    $.ajax({
        url :"/index.php/Admin/SceneAccount/index/type/operation",
        type : 'post',
        data : formData,
        async: false,
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) {
            if(data.status == 'success') {
                alertbox(data);
                location.reload();
                $("#"+id).val("");
            } else {
                alertbox(data);
            }
        },
        error: function (data) {
            alertbox(data);
        }
    });
}

function scene_probability(product_id,operator_id,probability,url){
    var user_activity_id=$("#user_activity_id").val();
    $.post(url,{product_id:product_id,operator_id:operator_id,probability:probability,user_activity_id:user_activity_id},function(data){
        if(data.status=="success"){
        }
        alertbox(data);
    },"json");
}

/*function editpayment(name,value){
    var account_id=$("#account_id").val();
    $.post("/index.php/Admin/SceneAccount/index/type/save",{name:name,value:value,account_id:account_id},function(data){
        if(data.status=="success"){
            $("#payment_type").val(value);
        }
        alertbox(data);
    },"json");
}*/

function editinpueall(name){
    var value = $("#inputtextid"+name).val();
    var account_id=$("#account_id").val();
    $.post("/index.php/Admin/SceneAccount/index/type/save",{name:name,value:value,account_id:account_id},function(data){
        if(data.status=="success"){
            var htmls = "<span id='span_"+name+"'>"+value+"</span>";
            $(".show_"+name).html(htmls);
        }
        alertbox(data);
    },"json");
}
function apply_confirm(content,id,applyurl){
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
            channel_account_add_btn_ststus=1;
            if(data.status == 'success') {
                parent.update_account();
                parent.layer.closeAll();
            }
            location.reload();
        }

        if(channel_account_add_btn_ststus==1) {
            channel_account_add_btn_ststus = 2;
            $.post(url, data, fun, 'json');
        }
    }, function() {
        location.reload();
    });
}

function enterprise_transfer(content,da,applyurl){
    parent.layer.confirm('<i class="confirm_icon"></i>'+js_toThousands(content), {
        title:"提示信息",
        btn: ['确定','取消'] //按钮
    },function(){
        var url = applyurl;
        var data = da;
        var fun = function(data){
            alertbox(data);
            channel_account_add_btn_ststus=1;
            if(data.status == 'success') {
                parent.update_account();
                location.reload();
            }
            parent.layer.closeAll();
        }
        if(channel_account_add_btn_ststus==1) {
            channel_account_add_btn_ststus = 2;
            $.post(url, data, fun, 'json');
        }
    }, function() {
        location.reload();
    });
}
//用于下载链接添加随机数
function down_file(url){
    ss = url.substr(0,url.length-5);
    location.href=ss+"/mathran/"+Math.random();
}

function get_img(obj){
    var mb=$("#mb").find("option:selected").attr("emoney");
    var background_img_phone=obj.value;
    $('.phone_inner',parent.document).css("background-image", "url("+background_img_phone+")");
    $("#bak",parent.document).val(mb);
    $("#mb_b",parent.document).val(mb);
    $("#upload_img3",parent.document).attr('src',background_img_phone);
}