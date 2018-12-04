/**
 * ***************************
 *  登录使用
 *****************************
 */

//更换验证码
function update_verify(){

    var num = Math.floor(Math.random()*1000);
    var verify_url = "/index.php/Admin/Public/create_verify?rand="+num;
    $('#login_verify_img').attr('src',verify_url);
}


$(function(){
    var num = Math.floor(Math.random()*1000);
    var verify_url = "/index.php/Admin/Public/create_verify?rand="+num;
    $('#login_verify_img').attr('src',verify_url);

    //点击验证码更换验证码
    $("#login_verify_img").on('click',function(){
        update_verify();
    });


    //登录功能
    $('#login_loginbtn').on('click',function(){
        var username = $("input[name='username']").val();
        var password = $("input[name='password']").val();
        var remember_pass = $("input[name='remember_pass']").val();
        var verify = $("input[name='verify']").val();
        var url = "/index.php/Admin/Public/login";
        var data= {username:username,password:password,verify:verify,remember_pass:remember_pass};
        var fun = function(data){
            if(data.status == 'success'){
              location.href="/index.php/Admin/Index/index";
            }else{
                $('#error_msg').html(data.msg);
                update_verify();
                if(data.msg != '验证码错误'){
                    //$("input[name='password'],input[name='verify']").val('');
                    $("input[name='verify']").val('');
                }else{
                    $("input[name='verify']").val('');
                }
            }
        }
      $.post(url,data,fun,'json');
    })
})


/**
 * ***************************
 *  管理员页面使用
 *****************************
 */
$(function(){


    //新增管理员
    $("#user_add_btn").on('click',function(){
        $("#layerdivid",top.document).load('/index.php/Admin/User/add',function(data){
          if(is_layer(data)){
            top.layer.open({
              type: 1,
              title:'新增用户',
              area: ['450px', '370px'], //宽高
              content: $("#user_add_box",top.document),

              success:function(){
                inputFocus("user_add_form");
              },
              btn:['保存', '取消'],

              yes: function(){
                if(!checkform('user_add_form')){
                  return false;
                }
                var url = '/index.php/Admin/User/insert';
                var data = $("form[name='user_add_form']",top.document).serialize();
                var fun = function(data){
                  alertbox(data);
                  if(data.status == 'success'){
                    top.layer.closeAll();
                    location.reload();
                  }
                  
                }
                $.post(url,data,fun,'json');
              }
            });
          }
        })
    })
    

    //设置管理员角色
    $(".user_set_role").on('click',function(){
       var user_id = $(this).attr('value');
        $("#layerdivid",top.document).load('/index.php/Admin/Role/set_role?user_id='+user_id,function(data){
            if(is_layer(data)){
                 top.layer.open({
                  type: 1,
                  title:'设置角色',
                  area: ['600px', '300px'], //宽高
                  content: $("#user_set_role_box",top.document),
                  success:function(){
                    inputFocus("set_role_form");
                  },
                  btn:['确定', '取消'],
                  yes: function(){
                    var url = '/index.php/Admin/Role/set_role';
                    var data=$("form[name = 'set_role_form']",top.document).serialize();
                    var fun = function(data){
                      if(data.status == 'success'){
                        top.layer.closeAll();
                        location.reload();
                      }
                      alertbox(data);
                    }
                    $.post(url,data,fun,'json');
                  }
              });
            } 
          })
        })

    
    //删除管理员
    $(".user_delete").on('click',function(){
     var user_id = $(this).attr('value');
     var user_name = $(this).parent().parent().children("td.name").html();
      top.layer.confirm('<i class="confirm_icon"></i>确定是否删除用户【'+user_name+'】？', {
          title:'提示信息',
          btn: ['确定','取消'] //按钮
      }, function(){
          top.layer.closeAll();
          var url = '/index.php/Admin/User/delete';
          var data = {user_id:user_id};
          var fun = function(data){
            if(data.status == 'success'){
              location.reload();
            }
            alertbox(data);
          }

          $.post(url,data,fun,'json');
          
      }, function(){
         
      });


    })



    //修改管理员
    $(".user_edit").on('click',function(){
       var user_id = $(this).attr('value');
        $('#layerdivid',top.document).load('/index.php/Admin/User/edit?user_id='+user_id,function(data){
          if(is_layer(data)){
            top.layer.open({
              type: 1,
              title:'编辑用户',
              area: ['450px', '370px'], //宽高
              content: $('#user_edit_box',top.document),
              success:function(){
                    inputFocus("user_edit_form");
                  },
              btn:['确定', '取消'],
              yes: function(){
                if(!checkform('user_edit_form')){
                  return false;
                }
                var url = '/index.php/Admin/User/update';
                var data = $("form[name='user_edit_form']",top.document).serialize();
                var fun = function(data){
                  if(data.status == 'success'){
                    location.reload();
                    top.layer.closeAll();
                  }
                  alertbox(data);
                }
                $.post(url,data,fun,'json');
              }
            });
          }

        })
    })

    
    //禁用/启用用户
    $(".user_toggle_status").on('click',function(){
        var user_id = $(this).attr('value');
        var status = $(this).attr('data');
        var user_name = $(this).parent().parent().children("td.name").html();

        if(status == '1'){
          var title = '确定是否禁用用户【'+user_name+'】？';
        }else{
          var title = '确定是否启用用户【'+user_name+'】？';
        }
        top.layer.confirm('<i class="confirm_icon"></i>'+title, {
          title:'提示信息',
          btn: ['确定','取消'] //按钮
      }, function(){
          top.layer.closeAll();
          var url = '/index.php/Admin/User/toggle_status';
          var data = {user_id:user_id}
          var fun = function(data){
            if(data.status == 'success'){
                  location.reload();
            }
                alertbox(data);
            }
        $.post(url,data,fun,'json');
          
      }, function(){
         
      });
       
        })


      //用户重置密码
    $(".user_reset_password").on('click',function(){
          var user_id = $(this).attr('value');
          var data = {user_id:user_id};
          var post_url = '/index.php/Admin/User/reset_password';
          var msg = '确定是否重置用户密码？';
          confirm_alert(msg,data,post_url);

        })



   /** 
     *   管理员详情
     */
    $(".user_show").on('click',function(){

        var user_id = $(this).attr('value');
        $("#layerdivid",top.document).load('/index.php/Admin/User/show?user_id='+user_id,function(data){
            if(is_layer(data)){
              top.layer.open({
                type: 1,
                title:'用户信息',
                area: ['450px', '330px'], //宽高
                content: $("#user_show_box",top.document),
                btn:['关闭'],
                yes: function(){
                  top.layer.closeAll();
                }
              });
            }
          })
    })




    })
  

    





/**
 * ***************************
 *  代理商页面使用
 *****************************
 */

 $(function(){

    //新增代理商
    $("#proxy_add_btn").on('click',function(){
      /*
      var title = '新增代理商';
      var area = ['420px', '410px'];
      var view_name = 'proxy_add_box';
      var form_name = 'proxy_add_form';
      var view_url = '/index.php/Admin/Proxy/add';
      var post_url = '/index.php/Admin/Proxy/insert';
       view_form_add(title,area,view_name,form_name,view_url,post_url,true);
      */
          $("#layerdivid",top.document).load('/index.php/Admin/Proxy/add',function(data){
            if(is_layer(data)){
              top.layer.open({
                type: 1,
                title: '新增代理商',
                area: ['420px', '410px'], //宽高
                content: $("#proxy_add_box",top.document),
                success:function(){
                      inputFocus('proxy_add_form');
                    },
                btn:['保存', '取消'],
                yes: function(){

                  if(!checkform('proxy_add_form')){
                    return false;
                  }

                  var formData = new FormData($("form[name='proxy_add_form']",top.document)[0]);

                  $.ajax({  
                        url: '/index.php/Admin/Proxy/insert' ,  
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
                             /* if(data.layer.is_layer_status == 'success'){
                                  top.layer.closeAll();
                                  top.layer.alert(data.layer.is_layer_msg,{title:'提示信息'});
                              }*/

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
     *   代理商信息详情
     */
    $(".proxy_show").on('click',function(){
      var proxy_id = $(this).attr('value');
      var title = '代理商信息';
      var area =  ['700px', '560px']; //宽高
      var view_name = 'proxy_show_box';
      var view_url = '/index.php/Admin/Proxy/show?proxy_id='+proxy_id;
      view_show(title,area,view_name,view_url);
    })


    
    /**
     *  审核代理商
     */
    $(".proxy_approve_status").on('click',function(){
        var val = $(this).attr('value');
        $("#layerdivid",top.document).load('/index.php/Admin/Proxy/approve?proxy_id='+val,function(data){
          if(is_layer(data)){
            top.layer.open({
                  type: 1,
                  title:'代理商审核',
                  area: ['700px', '510px'], //宽高
                  content: $("#proxy_approve_box",top.document),
                  success:function(){
                    inputFocus("proxy_approve_form");
                  },
                  btn:['确定','取消'],
                  yes: function(){
                        var status = $("form[name='proxy_approve_form'] input[name='approve_status']",top.document).val();
                        var approve_remark=$("form[name='proxy_approve_form'] textarea[name='approve_remark']",top.document).val();
                      if(status == 2 && approve_remark=='' ){
                          alertbox({msg:'请填写审核驳回原因！',status:'error'});
                          return;
                      }
                        if(status == 1){
                            var title = '通过';
                        }else{
                            var title = '驳回';
                        }
                        top.layer.confirm('<i class="confirm_icon"></i>确定是否审核'+title+'该代理商？', {
                            title:'提示信息',
                            btn: ['确定','取消'] //按钮
                        }, function(){
                            var url = '/index.php/Admin/Proxy/approve';
                            var data = $("form[name='proxy_approve_form']",top.document).serialize();
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
                              
                        }, function(){
                             
                        });
                  }
                });

          }

        })
    })
    


    /**
     *  审核通过前代理商证件管理
     */
     $('.proxy_approve_credentials_edit').on('click',function(){
      var val = $(this).attr('value'); 
      var view_url = '/index.php/Admin/Proxy/approve_credentials_edit?proxy_id='+val;
      var post_url =  '/index.php/Admin/Proxy/approve_credentials_update';
      credentials(1,view_url,post_url);

     })

    

    /**
     *  代理商证件管理
     */
     $('.proxy_credentials_edit').on('click',function(){
        var val = $(this).attr('value');
        var view_url = '/index.php/Admin/Proxy/credentials_edit?proxy_id='+val;
        var post_url = '/index.php/Admin/Proxy/credentials_update';
        credentials(1,view_url,post_url);
       
     })



    /**
     *  审核通过前企业证件管理
     */
     $('.enterprise_approve_credentials_edit').on('click',function(){
        var val = $(this).attr('value');
        var view_url = '/index.php/Admin/Enterprise/approve_credentials_edit?enterprise_id='+val;
        var post_url = '/index.php/Admin/Enterprise/approve_credentials_update';
        credentials(2,view_url,post_url);
       
     })


    /**
     *  企业证件管理
     */
     $('.enterprise_credentials_edit').on('click',function(){
        var val = $(this).attr('value');
        var view_url = '/index.php/Admin/Enterprise/credentials_edit?enterprise_id='+val;
        var post_url = '/index.php/Admin/Enterprise/credentials_update';
        credentials(2,view_url,post_url);
       
     })




    /**
     *  代理商设置客户经理
     */
    $('.proxy_set_sale').on('click',function(){
      var val = $(this).attr('value');
        $("#layerdivid",top.document).load('/index.php/Admin/Proxy/set_sale?proxy_id='+val,function(data){
          if(is_layer(data)){
            top.layer.open({
              type: 1,
              title:'设置客户经理',
              area: ['400px', '270px'], //宽高
              content: $("#proxy_set_sale_box",top.document),
              success:function(){
                    inputFocus("proxy_set_sale_form");
                  },
              btn:['确定','取消'],
              yes: function(){
                var url = '/index.php/Admin/Proxy/set_sale';
                var data = $("form[name='proxy_set_sale_form']",top.document).serialize();
                var fun = function(data){
                  if(data.status == 'success'){
                    location.reload();
                    top.layer.closeAll();
                  }
                  alertbox(data);
                }
                $.post(url,data,fun,'json');
              }
            });
          }
        })
    })


    //修改代理商
    $(".proxy_edit").on('click',function(){
      var val = $(this).attr('value');
      var title = '编辑代理商';
      var area = ['430px', '450px'];
      var view_name = 'proxy_edit_box';
      var form_name = 'proxy_edit_form';
      var view_url = '/index.php/Admin/Proxy/edit?proxy_id='+val;
      var post_url = '/index.php/Admin/Proxy/update';
      view_form(title,area,view_name,form_name,view_url,post_url,true);

    })


    //禁用/启用代理商
    $(".proxy_toggle_status").on('click',function(){
        var proxy_id = $(this).attr('value');
        var status = $(this).attr('data');
        var proxy_name = $(this).parent().parent().children("td.name").html();

        if(status == '1'){
          var title = '确定是否禁用代理商【'+proxy_name+'】？';
        }else{
          var title = '确定是否启用代理商【'+proxy_name+'】？';
        }
        var data = {proxy_id:proxy_id};
        var post_url = '/index.php/Admin/Proxy/toggle_status';
        confirm(title,data,post_url);
        
    })


    //重置代理商超级管理员的密码
    $(".proxy_reset_password").on('click',function(){
        var proxy_id = $(this).attr('value');
        var data = {proxy_id:proxy_id};
        var post_url = '/index.php/Admin/Proxy/reset_password';
        var msg = '确定是否重置代理商密码？';
        confirm_alert(msg,data,post_url);
       
    })



    /** 
     *   设置代理商信息
     */
    $("#set_proxy").on('click',function(){
      var title = '企业设置';
      var area = ['420px', '410px'];
      var view_name = 'set_proxy_box';
      var form_name = 'set_proxy_form';
      var view_url = '/index.php/Admin/Proxy/set_proxy';
      var post_url = '/index.php/Admin/Proxy/set_proxy';
      view_form(title,area,view_name,form_name,view_url,post_url);

    })

    /**
     *  代理商审核编辑
     */
    $(".proxy_approve_edit").on('click',function(){

       var proxy_id = $(this).attr('value');
        var title = '编辑代理商';
        var area = ['450px', '410px'];
        var view_name = 'proxy_approve_edit_box';
        var form_name = 'proxy_approve_edit_form';
        var view_url = '/index.php/Admin/Proxy/approve_edit?proxy_id='+proxy_id;
        var post_url = '/index.php/Admin/Proxy/approve_update';
        view_form(title,area,view_name,form_name,view_url,post_url,true);

    })
    
    /**
     *  代理商被驳回后重新送审
     */
    $(".proxy_approve_again").on('click',function(){

      var proxy_id = $(this).attr('value');
      var msg = '确定重新提交申请吗？';
      var data = {proxy_id:proxy_id};
      var post_url = '/index.php/Admin/Proxy/approve_again';
      confirm(msg,data,post_url);

    })


    /** 
     *   代理商审核信息详情
     */
    $(".proxy_approve_show").on('click',function(){
      var proxy_id = $(this).attr('value');
      var title = '代理商信息';
      var area =  ['700px', '490px']; //宽高
      var view_name = 'proxy_approve_show_box';
      var view_url = '/index.php/Admin/Proxy/approve_show?proxy_id='+proxy_id;
      view_show(title,area,view_name,view_url);

    })


    //删除驳回的代理商
    $(".proxy_delete").on('click',function(){
        var proxy_id = $(this).attr('value');
        var proxy_name = $(this).parent().parent().children("td.name").html();
        var msg = '确定是否删除代理商【'+proxy_name+'】？';
        var data = {proxy_id:proxy_id};
        var post_url = '/index.php/Admin/Proxy/delete';
        confirm(msg,data,post_url);
    })

     //删除驳回的代理商
    $(".proxy_approve_delete").on('click',function(){
        var proxy_id = $(this).attr('value');
        var proxy_name = $(this).parent().parent().children("td.name").html();
        var msg = '确定是否删除代理商【'+proxy_name+'】？';
        var data = {proxy_id:proxy_id};
        var post_url = '/index.php/Admin/Proxy/approve_delete';
        confirm(msg,data,post_url);
    })

 })


/**
 * ***************************
 *  企业页面使用
 *****************************
 */
 $(function(){


        //新增企业
        $("#enterprise_add_btn").on('click',function(){
            $("#layerdivid",top.document).load('/index.php/Admin/Enterprise/add',function(data){
              if(is_layer(data)){
                top.layer.open({
                  type: 1,
                  title:'新增企业',
                  area: ['450px', '410px'], //宽高
                  content: $("#enterprise_add_box",top.document),
                  success:function(){
                    inputFocus("enterprise_add_form");
                  },
                  btn:['保存', '取消'],
                  yes: function(){

                    if(!checkform('enterprise_add_form')){
                      return false;
                    }

                    var formData = new FormData($("form[name='enterprise_add_form']",top.document)[0]);

                    $.ajax({  
                          url: '/index.php/Admin/Enterprise/insert' , 
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
         *  审核企业
         */
        $(".enterprise_approve_status").on('click',function(){
            var val = $(this).attr('value');
            $("#layerdivid",top.document).load('/index.php/Admin/Enterprise/approve?enterprise_id='+val,function(data){
              if(is_layer(data)){
                top.layer.open({
                      type: 1,
                      title:'企业审核',
                      area: ['700px', '510px'], //宽高
                      content: $("#enterprise_approve_box",top.document),
                      success:function(){
                        inputFocus("enterprise_approve_form");
                      },
                      btn:['确定', '取消'],
                      yes: function(){
                        var status = $("form[name='enterprise_approve_form'] input[name='approve_status']",top.document).val();
                        var approve_remark=$("form[name='enterprise_approve_form'] textarea[name='approve_remark']",top.document).val();
                          if(status == 2 && approve_remark=='' ){
                              alertbox({msg:'请填写审核驳回原因！',status:'error'});
                              return;
                          }
                        if(status == 1){
                          var title = '通过';
                        }else{
                          var title = '驳回';
                        }
                        top.layer.confirm('<i class="confirm_icon"></i>确定是否审核'+title+'该企业？', {
                            title:'提示信息',
                            btn: ['确定','取消'] //按钮
                        }, function(){
                            var url = '/index.php/Admin/Enterprise/approve';
                            var data = $("form[name='enterprise_approve_form']",top.document).serialize();
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
                              
                        }, function(){
                             
                        });
                          
                      }
                    });

              }

            })
        })


        /**
         *  审核信息
         */
        $(".enterprise_info").on('click',function(){
            var val = $(this).attr('value');
            $("#layerdivid",top.document).load('/index.php/Admin/Enterprise/approve?enterprise_id='+val,function(data){
              if(is_layer(data)){
                top.layer.open({
                  type: 1,
                  title:'企业信息',
                  area: ['500px', '450px'], //宽高
                  content: $("#enterprise_approve_box",top.document),
                  btn:['关闭'],
                  yes: function(){
                    top.layer.closeAll();
                  }
                });
              }

            })
        })



        /**
         *  企业设置客户经理
         */
        $('.enterprise_set_sale').on('click',function(){
          var val = $(this).attr('value');
            $("#layerdivid",top.document).load('/index.php/Admin/Enterprise/set_sale?enterprise_id='+val,function(data){
              if(is_layer(data)){
                top.layer.open({
                  type: 1,
                  title:'设置客户经理',
                  area: ['400px', '270px'], //宽高
                  content: $("#enterprise_set_sale_box",top.document),

              success:function(){
                    inputFocus("enterprise_set_sale_form");
                  },
                  btn:['确定','取消'],
                  yes: function(){
                    var url = '/index.php/Admin/Enterprise/set_sale';
                    var data = $("form[name='enterprise_set_sale_form']",top.document).serialize();
                    var fun = function(data){
                      if(data.status == 'success'){
                        location.reload();
                        top.layer.closeAll();
                      }
                      alertbox(data);
                    }
                    $.post(url,data,fun,'json');
                  }
                });
              }
            })
        })




        //禁用/启用企业
        $(".enterprise_toggle_status").on('click',function(){
            var enterprise_id = $(this).attr('value');
            var status = $(this).attr('data');
            var enterprise_name = $(this).parent().parent().children("td.name").html();

            if(status == '1'){
              var title = '确定是否禁用企业【'+enterprise_name+'】？';
            }else{
              var title = '确定是否启用企业【'+enterprise_name+'】？';
            }
            top.layer.confirm('<i class="confirm_icon"></i>'+title, {
              title:'提示信息',
              btn: ['确定','取消'] //按钮
          }, function(){
              top.layer.closeAll();
              var url = '/index.php/Admin/Enterprise/toggle_status';
              var data = {enterprise_id:enterprise_id}
              var fun = function(data){
                if(data.status == 'success'){
                      location.reload();
                }
                    alertbox(data);
                }
            $.post(url,data,fun,'json');
              
          }, function(){
             
          });
       
        })


        /** 
         *   设置企业
         */
        $("#set_enterprise").on('click',function(){

          var title = '企业设置';
          var area = ['420px', '410px'];
          var view_name = 'set_enterprise_box';
          var form_name = 'set_enterprise_form';
          var view_url = '/index.php/Admin/Enterprise/set_enterprise';
          var post_url = '/index.php/Admin/Enterprise/set_enterprise';
          view_form(title,area,view_name,form_name,view_url,post_url);

        })
        

        /** 
         *   重置企业密码
         */
        $(".enterprise_reset_password").on('click',function(){
          var enterprise_id = $(this).attr('value');
          var data = {enterprise_id:enterprise_id};
          var post_url = '/index.php/Admin/Enterprise/reset_password';
          var msg = '确定是否重置企业密码？';
          confirm_alert(msg,data,post_url);

        })    


    /**
     *  审核通过前信息修改
     */
    $(".enterprise_approve_edit").on('click',function(){
      var enterprise_id = $(this).attr('value');
        var title = '编辑企业';
        var area = ['450px', '410px'];
        var view_name = 'enterprise_approve_edit_box';
        var form_name = 'enterprise_approve_edit_form';
        var view_url = '/index.php/Admin/Enterprise/approve_edit?enterprise_id='+enterprise_id;
        var post_url = '/index.php/Admin/Enterprise/approve_update';
        view_form(title,area,view_name,form_name,view_url,post_url,true);

    })


    /**
     *  企业被驳回后重新送审
     */
    $(".enterprise_approve_again").on('click',function(){

        var enterprise_id = $(this).attr('value');
        var msg = '确定重新提交申请吗？';
        var data = {enterprise_id:enterprise_id};
        var post_url = '/index.php/Admin/Enterprise/approve_again';
        confirm(msg,data,post_url);

    })

    /** 
     *   企业审核信息详情
     */
    $(".enterprise_approve_show").on('click',function(){
        var enterprise_id = $(this).attr('value');
        var title = '企业信息';
        var area = ['700px', '490px'];
        var view_name = 'enterprise_approve_show_box';
        var view_url = '/index.php/Admin/Enterprise/approve_show?enterprise_id='+enterprise_id;
        view_show(title,area,view_name,view_url);

    })


    
    /** 
     *   企业信息详情
     */
    $(".enterprise_show").on('click',function(){
        var enterprise_id = $(this).attr('value');
        var title = '企业信息';
        var area = ['700px', '490px'];
        var view_name = 'enterprise_show_box';
        var view_url = '/index.php/Admin/Enterprise/show?enterprise_id='+enterprise_id;
        view_show(title,area,view_name,view_url);
    })


    //修改企业
    $(".enterprise_edit").on('click',function(){
        var enterprise_id = $(this).attr('value');
        $("#layerdivid",top.document).load('/index.php/Admin/Enterprise/edit?enterprise_id='+enterprise_id,function(data){
          if(is_layer(data)){
            top.layer.open({
              type: 1,
              title:'编辑企业',
              area: ['430px', '450px'], //宽高
              content: $("#enterprise_edit_box",top.document),

              success:function(){
                    inputFocus("enterprise_edit_form");
                  },
              btn:['保存', '取消'],
              yes: function(){

                if(!checkform('enterprise_edit_form',2)){
                  return false;
                }

                var formData = new FormData($("form[name='enterprise_edit_form']",top.document)[0]);

                $.ajax({  
                      url: '/index.php/Admin/Enterprise/update' ,  
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


        //删除驳回的企业
    $(".enterprise_delete").on('click',function(){
        var enterprise_id = $(this).attr('value');
        var enterprise_name = $(this).parent().parent().children("td.name").html();
        var msg = '确定是否删除企业【'+enterprise_name+'】？';
        var data = {enterprise_id:enterprise_id};
        var post_url = '/index.php/Admin/Enterprise/delete';
        confirm(msg,data,post_url);
    })


        //删除驳回的企业
    $(".enterprise_approve_delete").on('click',function(){
        var enterprise_id = $(this).attr('value');
        var enterprise_name = $(this).parent().parent().children("td.name").html();
        var msg = '确定是否删除企业【'+enterprise_name+'】？';
        var data = {enterprise_id:enterprise_id};
        var post_url = '/index.php/Admin/Enterprise/approve_delete';
        confirm(msg,data,post_url);
    })


 })




/**
 * ***************************
 *  主页面使用
 *****************************
 */
$(function(){
        /**
         *  个人设置
         */
        $('#user_set').on('click',function(){
          var title = '个人设置';
          var area =  ['420px', '370px']; //宽高
          var view_name = 'user_set_box';
          var form_name = 'user_set_form';
          var view_url = '/index.php/Admin/User/set';
          var post_url = '/index.php/Admin/User/set';
          view_form(title,area,view_name,form_name,view_url,post_url);

        })

        /**
         *  修改密码
         */
        $('#set_password').on('click',function(){
          var title = '修改密码';
          var area =  ['420px', '230px']; //宽高
          var view_name = 'user_set_password_box';
          var form_name = 'user_set_password_form';
          var view_url = '/index.php/Admin/User/set_password';
          var post_url = '/index.php/Admin/User/set_password';
          view_form(title,area,view_name,form_name,view_url,post_url);

        })

      
        /**
         *  退出系统
         */
        $('#logout').on('click',function(){
            top.layer.confirm('<i class="confirm_icon"></i>确定退出当前系统吗？', {
                title:'提示信息',
                btn: ['确定','取消'] //按钮
            }, function(){
                location.href = '/index.php/Admin/Public/logout';
            }, function(){
               
            });
        })

})



/**
 * ***************************
 *  代理商权限分配页面
 *****************************
 */
$(function(){

    /**
     *  点击管理员获取当前管理员的代理商
     */
    $('tr.set_proxy_user').on('click',function(){

      $('.radio.checked').removeClass('checked');
      $(this).children('td').children('label.radio').addClass('checked');
      var val = $(this).attr('value');
      var url = '/index.php/Admin/Proxy/set_proxy_user?user_id='+val+'&type=getlist';
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
              var html = '<tr><td width="20%"><label class="checkbox" value="'
                   +data.data.no[i]["proxy_id"]+
                   '"><em></em></label></td><td>'
                   +data.data.no[i]['sort_no']+
                   '</td><td width="30%">'
                   +data.data.no[i]["proxy_code"]+
                   '</td><td width="50%">'
                   +data.data.no[i]["proxy_name"]+
                   '</td></tr>';
                   
              no_html = no_html + html;
          }
          $("tbody.no_list").html('').append(no_html);
          
          var have_html = '';
          for(var i=0;i < data.data.have.length; i++ ){
            if(!data.data.have[i]["user_name"]){
                data.data.have[i]["user_name"] = '';
            }
             var html = '<tr><td width="20%"><label class="checkbox" value="'
                   +data.data.have[i]["proxy_id"]+
                   '"><em></em></label></td><td>'
                   +data.data.have[i]['sort_no']+
                   '</td><td width="30%" >'
                   +data.data.have[i]["proxy_code"]+
                   '</td><td width="50%">'
                   +data.data.have[i]["proxy_name"]+
                   '</td></tr>';

              have_html = have_html + html;
          }
          $("tbody.have_list").html('').append(have_html);
          if(data.data.is_all_proxy == 1){
            $(".set_proxy_user.delete").show();
            $(".set_proxy_user.add").hide();
          }else{
            $(".set_proxy_user.delete").hide();
            $(".set_proxy_user.add").show();
          }
          
        }else{
          alertbox(data);
        }
          

      }
      $.get(url,data,fun,'json');

    })

    
    /**
     *  执行添加用户对代理商的权限
     */
    $('.set_proxy_user.rightarrow').on('click',function(){
      var ids = '';
      var objlist = $("tbody.no_list label.checked");
      var count = $("tbody.no_list label.checked").length;
      for(var i = 0;i< count ;i++){
          ids += ','+$(objlist).eq(i).attr('value');
      }
        ids = ids.substr(1,(ids.length)-1);
      if(!ids)return false;
      var user_id = $("tbody.user_list .checked").attr('value');
      var url = '/index.php/Admin/Proxy/set_proxy_user';
      var data = {user_id:user_id,proxy_ids:ids,type:'add'}
      var fun = function(data){
        if(data.status == 'success'){
          location.href="/index.php/Admin/Proxy/set_proxy_user?user_id="+user_id;
          /*
          for(var i = 0;i < count ;i++){
            $(objlist).removeClass('checked');
            $("tbody.have_list ").append($(objlist).eq(i).tops("tr"));
          }
          */
        }
          alertbox(data);

      }
      $.post(url,data,fun,'json');

    })


    /**
     *  执行删除用户对代理商的权限
     */
    $('.set_proxy_user.leftarrow').on('click',function(){
      var ids = '';
      var objlist = $("tbody.have_list label.checked");
      var count = $("tbody.have_list label.checked").length;
      for(var i = 0;i< count ;i++){
          ids += ','+$(objlist).eq(i).attr('value');
      }
        ids = ids.substr(1,(ids.length)-1);
      if(!ids)return false;
      var user_id = $("tbody.user_list .checked").attr('value');
      var url = '/index.php/Admin/Proxy/set_proxy_user';
      var data = {user_id:user_id,proxy_ids:ids,type:'delete'}
      var fun = function(data){
        if(data.status == 'success'){
          /*
          for(var i = 0;i < count ;i++){
            $(objlist).removeClass('checked');
            $("tbody.no_list ").append($(objlist).eq(i).tops("tr"));
          }
          */
          location.href="/index.php/Admin/Proxy/set_proxy_user?user_id="+user_id;
        }
          alertbox(data);

      }
      $.post(url,data,fun,'json');
      
    })



    /**
     *  执行删除用户对所有代理商的权限
     */

    $('.set_proxy_user.delete').on('click',function(){
      var user_id = $("tbody.user_list .checked").attr('value');
      if(!user_id){return false;}
      var url = '/index.php/Admin/Proxy/set_proxy_user';
      var data = {user_id:user_id,type:'alldelete'}
      var fun = function(data){
        

        if(data.status == 'success'){
            layer.alert('<i class="confirm_icon"></i>'+data.msg,{title:'提示信息'});
            var url = '/index.php/Admin/Proxy/set_proxy_user?user_id='+user_id+'&type=getlist';
            var data = {};
            var fun = function(data){

              if(data.status == 'success'){
                var no_html = '';
                for(var i=0;i < data.data.no.length; i++ ){
                  if(!data.data.no[i]["user_name"]){
                      data.data.no[i]["user_name"] = '';
                  }
                    var html = '<tr><td width="20%"><label class="checkbox" value="'
                         +data.data.no[i]["proxy_id"]+
                         '"><em></em></label></td><td>'
                         +data.data.no[i]['sort_no']+
                         '</td><td width="30%">'
                         +data.data.no[i]["proxy_code"]+
                         '</td><td width="50%">'
                         +data.data.no[i]["proxy_name"]+
                         '</td></tr>';
                         
                    no_html = no_html + html;
                }
                $("tbody.no_list").html('').append(no_html);
                
                var have_html = '';
                for(var i=0;i < data.data.have.length; i++ ){
                  if(!data.data.have[i]["user_name"]){
                      data.data.have[i]["user_name"] = '';
                  }
                   var html = '<tr><td width="20%"><label class="checkbox" value="'
                         +data.data.have[i]["proxy_id"]+
                         '"><em></em></label></td><td>'
                         +data.data.have[i]['sort_no']+
                         '</td><td width="30%" >'
                         +data.data.have[i]["proxy_code"]+
                         '</td><td width="50%">'
                         +data.data.have[i]["proxy_name"]+
                         '</td></tr>';

                    have_html = have_html + html;
                }
                $("tbody.have_list").html('').append(have_html);

                  $(".set_proxy_user.delete").hide();
                  $(".set_proxy_user.add").show();

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
     *  执行添加用户对所有代理商的权限
     */

    $('.set_proxy_user.add').on('click',function(){
      var user_id = $("tbody.user_list .checked").attr('value');
      if(!user_id){return false;}
      var url = '/index.php/Admin/Proxy/set_proxy_user';
      var data = {user_id:user_id,type:'alladd'}
      var fun = function(data){
        if(data.status == 'success'){
            $("tbody.have_list").html('');
            $("tbody.no_list").html('');
            $(".set_proxy_user.add").hide();
            $(".set_proxy_user.delete").show();
            layer.alert('<i class="confirm_icon"></i>'+data.msg,{title:'提示信息'});
        }else{
          alertbox(data);
        }
          
      }
      $.post(url,data,fun,'json');
      
    })



    $('.seach_set_right').on('click',function(){

      var name = $(this).attr('data');
      var listname = $("input[name='"+name+"']").attr('data');
      var value = $("input[name='"+name+"']").val();

      if(!value){
          $("tbody."+listname+' tr').show();
      }else{
          $("tbody."+listname+' tr').hide();
          var num = $(this).attr('value');

          for(var i = 0; i< num.length;i++){
            var x = parseInt(num[i]);
              if(x){
                  $("tbody."+listname+' tr').each(function(i,e){
                    var val = $(e).children('td').eq(x).html().indexOf(value);
                      if( val >= 0){
                          $(e).show();
                      }
                  })
              }
          }
      }

    });


})


/**
 * ***************************
 *  企业权限分配
 ****************************/

 $(function(){

    /**
     *  点击管理员获取当前管理员的企业
     */
    $('tr.set_enterprise_user').on('click',function(){
      /**
      var val = $(this).attr('value');
      location.href="/index.php/Admin/Enterprise/set_enterprise_user?user_id="+val;
      */

      $('.radio.checked').removeClass('checked');
      $(this).children('td').children('label.radio').addClass('checked');
      var val = $(this).attr('value');
      var url = '/index.php/Admin/Enterprise/set_enterprise_user?user_id='+val+'&type=getlist';
      var data = {};
      var fun = function(data){

        if(data.status == 'success'){

          if(data.msg != ''){
            //alertbox(data);
            layer.alert('<i class="confirm_icon"></i>'+data.msg,{title:'提示信息'});
          }

          var no_html = '';
          for(var i=0;i < data.data.no.length; i++ ){

              var html = '<tr><td><label class="checkbox" value="'
                   +data.data.no[i]["enterprise_id"]+
                   '"><em></em></label></td><td>'
                   +data.data.no[i]["sort_no"]+
                   '</td><td >'
                   +data.data.no[i]["enterprise_code"]+
                   '</td><td>'
                   +data.data.no[i]["enterprise_name"]+
                   '</td></tr>';
                   
              no_html = no_html + html;
          }
          $("tbody.no_list").html('').append(no_html);
          
          var have_html = '';
          for(var i=0;i < data.data.have.length; i++ ){
              var html = '<tr><td ><label class="checkbox" value="'
                   +data.data.have[i]["enterprise_id"]+
                   '"><em></em></label></td><td>'
                   +data.data.have[i]["sort_no"]+
                   '</td><td>'
                   +data.data.have[i]["enterprise_code"]+
                   '</td><td>'
                   +data.data.have[i]["enterprise_name"]+
                   '</td></tr>';

              have_html = have_html + html;
          }
          $("tbody.have_list").html('').append(have_html);
          if(data.data.is_all_enterprise == 1){
            $(".set_enterprise_user.delete").show();
            $(".set_enterprise_user.add").hide();
          }else{
            $(".set_enterprise_user.delete").hide();
            $(".set_enterprise_user.add").show();
          }
        }else{
          alertbox(data);
        }
      }
      $.get(url,data,fun,'json');
 
    })

    

    /**
     *  执行添加用户对企业的权限
     */
    $('.set_enterprise_user.rightarrow').on('click',function(){
      var ids = '';
      var objlist = $("tbody.no_list label.checked");
      var count = $("tbody.no_list label.checked").length;
      for(var i = 0;i< count ;i++){
          ids += ','+$(objlist).eq(i).attr('value');
      }
        ids = ids.substr(1,(ids.length)-1);
      if(!ids)return false;
      var user_id = $("tbody.user_list .checked").attr('value');
      var url = '/index.php/Admin/Enterprise/set_enterprise_user';
      var data = {user_id:user_id,enterprise_ids:ids,type:'add'}
      var fun = function(data){
        if(data.status == 'success'){
          /*
          for(var i = 0;i < count ;i++){
            $(objlist).removeClass('checked');
            $("tbody.have_list ").append($(objlist).eq(i).tops("tr"));
          }
          */
          location.href="/index.php/Admin/Enterprise/set_enterprise_user?user_id="+user_id;
        }
          alertbox(data);
        
      }
      $.post(url,data,fun,'json');
      
    })


    /**
     *  执行删除用户对企业的权限
     */
    $('.set_enterprise_user.leftarrow').on('click',function(){
      var ids = '';
      var objlist = $("tbody.have_list label.checked");
      var count = $("tbody.have_list label.checked").length;
      for(var i = 0;i< count ;i++){
          ids += ','+$(objlist).eq(i).attr('value');
      }
        ids = ids.substr(1,(ids.length)-1);
      if(!ids)return false;
      var user_id = $("tbody.user_list .checked").attr('value');
      var url = '/index.php/Admin/Enterprise/set_enterprise_user';
      var data = {user_id:user_id,enterprise_ids:ids,type:'delete'}
      var fun = function(data){
        if(data.status == 'success'){
          /*
          for(var i = 0;i < count ;i++){
            $(objlist).removeClass('checked');
            $("tbody.no_list ").append($(objlist).eq(i).tops("tr"));
          }
          */
          location.href="/index.php/Admin/Enterprise/set_enterprise_user?user_id="+user_id;
        }
          alertbox(data);

      }
      $.post(url,data,fun,'json');
      
    })



    /**
     *  执行删除用户对所有企业的权限
     */

    $('.set_enterprise_user.delete').on('click',function(){
      var user_id = $("tbody.user_list .checked").attr('value');
      if(!user_id){return false;}
      var url = '/index.php/Admin/Enterprise/set_enterprise_user';
      var data = {user_id:user_id,type:'alldelete'}
      var fun = function(data){
       
        if(data.status == 'success'){
          layer.alert('<i class="confirm_icon"></i>'+data.msg,{title:'提示信息'});
            var url = '/index.php/Admin/Enterprise/set_enterprise_user?user_id='+user_id+'&type=getlist';
            var data = {};
            var fun = function(data){
              if(data.status == 'success'){
                var no_html = '';
                for(var i=0;i < data.data.no.length; i++ ){
                  if(!data.data.no[i]["user_name"]){
                      data.data.no[i]["user_name"] = '';
                  }
                    var html = '<tr><td><label class="checkbox" value="'
                         +data.data.no[i]["enterprise_id"]+
                         '"><em></em></label></td><td>'
                         +data.data.no[i]["sort_no"]+
                         '</td><td>'
                         +data.data.no[i]["enterprise_code"]+
                         '</td><td>'
                         +data.data.no[i]["enterprise_name"]+
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
                         +data.data.have[i]["enterprise_id"]+
                         '"><em></em></label></td><td>'
                         +data.data.have[i]["sort_no"]+
                         '</td><td>'
                         +data.data.have[i]["enterprise_code"]+
                         '</td><td>'
                         +data.data.have[i]["enterprise_name"]+
                         '</td></tr>';

                    have_html = have_html + html;
                }
                $("tbody.have_list").html('').append(have_html);

                  $(".set_enterprise_user.delete").hide();
                  $(".set_enterprise_user.add").show();

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
     *  执行添加用户对所有代理商的权限
     */

    $('.set_enterprise_user.add').on('click',function(){
      var user_id = $("tbody.user_list .checked").attr('value');
      if(!user_id){return false;}
      var url = '/index.php/Admin/Enterprise/set_enterprise_user';
      var data = {user_id:user_id,type:'alladd'}
      var fun = function(data){
        if(data.status == 'success'){
            $("tbody.have_list").html('');
            $("tbody.no_list").html('');
            $(".set_enterprise_user.add").hide();
            $(".set_enterprise_user.delete").show();
            layer.alert('<i class="confirm_icon"></i>'+data.msg,{title:'提示信息'});
        }else{
          alertbox(data);
        }
          
      }
      $.post(url,data,fun,'json');
      
    })


   

 })

/**
 *  角色新增
 */
$(function(){
    //公告添加
      $(".role_add_btn").on('click',function(){
        var title = '新增角色';
        var area = ['410px', '270px'];
        var view_name = 'role_add_box';
        var form_name = 'role_add_form';
        var view_url = '/index.php/Admin/Role/add';
        var post_url = '/index.php/Admin/Role/insert';
          view_form_add(title,area,view_name,form_name,view_url,post_url,true);

      });


      $(".role_edit").on('click',function(){
        var role_id = $(this).attr('value');
        var title = '编辑角色';
        var area = ['410px', '270px'];
        var view_name = 'role_edit_box';
        var form_name = 'role_edit_form';
        var view_url = '/index.php/Admin/role/edit/role_id/'+role_id;
        var post_url = '/index.php/admin/role/update';
        view_form(title,area,view_name,form_name,view_url,post_url,true); 

      });

    /*全部阅读功能*/
    $('.all_read_remind').on('click',function(){
        var receive_id=[];
        $(".all_remind_list  li a").each(function(){
            receive_id.push($(this).attr('value'));
        });
        var post_url='/index.php/Admin/ObjectRemind/handle_read_all';
        var data = {receive_id:receive_id};
        var fun = function(data){
            if(data.status == 'success'){
                $('span.tips_num.receive_count',top.document).html(data.data.receive_count);
                $('ul.receive_list li.delete',top.document).remove();
                var html = '';
                for(var i=0;i< data.data.receive_list.length ;i++ ){
                    html +='<li class="delete" ><a href="javascript:void(0);"onclick="objectremind_open(this);" url="'
                        +data.data.receive_list[i]['page_url']+
                        '" menu_id="'
                        +data.data.receive_list[i]['menu_id']+
                        '" menu_name="'
                        +data.data.receive_list[i]['menu_name']+
                        '" htype="0" value="'
                        +data.data.receive_list[i]['receive_id']+
                        '">'
                        +data.data.receive_list[i]['remind_content']+
                        '</a><span>'
                        +data.data.receive_list[i]['create_date']+
                        '</span></li>';
                }
                $('ul.receive_list',top.document).prepend(html);
            }
        }
        $.post(post_url,data,fun,'json');

    });

});


function objectremind_open(obj){
      //打开新页面
      var url = $(obj).attr('url');
      var menu_id = $(obj).attr('menu_id');
      var menu_name = $(obj).attr('menu_name');
      var htype = $(obj).attr('htype');
      var receive_id = $(obj).attr('value');
      top.openMenu(menu_id,menu_name,url);

      //改为已读
      var data = {receive_id:receive_id};
      var post_url = '/index.php/Admin/ObjectRemind/handle';
      var fun = function(data){
          if(data.status == 'success'){
            $('span.tips_num.receive_count',top.document).html(data.data.receive_count);
            $('ul.receive_list li.delete',top.document).remove();
            var html = '';
            for(var i=0;i< data.data.receive_list.length ;i++ ){
              html +='<li class="delete" ><a href="javascript:void(0);"onclick="objectremind_open(this);" url="'
              +data.data.receive_list[i]['page_url']+
              '" menu_id="'
              +data.data.receive_list[i]['menu_id']+
              '" menu_name="'
              +data.data.receive_list[i]['menu_name']+
              '" htype="0" value="'
              +data.data.receive_list[i]['receive_id']+
              '">'
              +data.data.receive_list[i]['remind_content']+
              '</a><span>'
              +data.data.receive_list[i]['create_date']+
              '</span></li>';
            }


            $('ul.receive_list',top.document).prepend(html);
          }
          if(htype == '1'){
            location.reload();
          }
      }
      $.post(post_url,data,fun,'json');
}

function credentials(type,view_url,post_url){
        if(type == 1){
          var msg  = '代理商';
        }else{
          var msg = '企业';
        }
        $("#layerdivid",top.document).load(view_url,function(data){
          if(is_layer(data)){
            top.layer.open({
              type: 1,
              title: msg+'证件管理',
              area: ['500px', '330px'], //宽高
              content: $("#credentials_box",top.document),
              success:function(){
                    inputFocus("credentials_form");
                  },
              btn:['保存','取消'],
              yes: function(){
                if(!checkform('credentials_form')){
                  return false;
                }
                var formData = new FormData($("form[name='credentials_form']",top.document)[0]);
                $.ajax({
                      url: post_url ,  
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
}

function view_form_add(title,area,view_name,form_name,view_url,post_url,reload){
    $("#layerdivid",top.document).load(view_url,function(data){
        if(is_layer(data)){
            top.layer.open({
                type: 1,
                title: title,
                area: area, //宽高
                content: $("#"+view_name,top.document),
                success:function(){
                    inputFocus(form_name);
                },
                btn:['保存', '取消'],
                yes: function(){

                    if(!checkform(form_name)){
                        return false;
                    }

                    var formData = new FormData($("form[name='"+form_name+"']",top.document)[0]);

                    $.ajax({
                        url: post_url ,
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
                                if(reload){
                                    location.reload();
                                }

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
}

function view_form(title,area,view_name,form_name,view_url,post_url,reload){
    $("#layerdivid",top.document).load(view_url,function(data){
            if(is_layer(data)){
              top.layer.open({
                type: 1,
                title: title,
                area: area, //宽高
                content: $("#"+view_name,top.document),
                success:function(){
                      inputFocus(form_name);
                    },
                btn:['确定', '取消'],
                yes: function(){

                  if(!checkform(form_name)){
                    return false;
                  }

                  var formData = new FormData($("form[name='"+form_name+"']",top.document)[0]);

                  $.ajax({  
                        url: post_url ,  
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
                            if(reload){
                              location.reload();
                            }
                            
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
}


function view_show(title,area,view_name,view_url){
    $("#layerdivid",top.document).load(view_url,function(data){
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

function toggle_status(field,post_url,data,obj){

        var id = $(obj).attr('value');
        var status = $(obj).attr('data');
        var msg = $(obj).parent().parent().children("td.name").html();

        if(status == '1'){
          var title = '确定是否禁用'+field+'【'+msg+'】？';
        }else{
          var title = '确定是否启用'+field+'【'+msg+'】？';
        }

        top.layer.confirm('<i class="confirm_icon"></i>'+title, {
          title:'提示信息',
          btn: ['确定','取消'] //按钮
      }, function(){
          top.layer.closeAll();
          var url = post_url;
          var fun = function(data){
            if(data.status == 'success'){
                  location.reload();
            }
                alertbox(data);
            }
        $.post(url,data,fun,'json');
          
      }, function(){
         
      });
       
}


function confirm(msg,data,post_url){
  top.layer.confirm('<i class="confirm_icon"></i>'+msg, {
          title:'提示信息',
          btn: ['确定','取消'] //按钮
      }, function(){
          top.layer.closeAll();
          var url = post_url;
          var fun = function(data){
            if(data.status == 'success'){
                  location.reload();
            }
                alertbox(data);
            }
        $.post(url,data,fun,'json');
          
      }, function(){
         
      });
}

function confirm_alert(msg,data,post_url){
  top.layer.confirm('<i class="confirm_icon"></i>'+msg, {
          title:'提示信息',
          btn: ['确定','取消'] //按钮
      }, function(){
          top.layer.closeAll();
          var url = post_url;
          var fun = function(data){
            if(data.status == 'success'){
              top.layer.alert(data.data,{title:'提示信息'});
              //location.reload();
            }
                alertbox(data);
            }
        $.post(url,data,fun,'json');
          
      }, function(){
         
      });
}