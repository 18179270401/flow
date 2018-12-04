/* huiJS函数统一处理 */

$(function(){

	//删除角色
	$(".role_delete").on('click',function() {
	  var role_id = $(this).attr('value');
	  var show_name = $(this).attr('show-name');
	  var user_role_sum = $(this).attr('user-role-sum');
      var confirmdesc = '';
      if(user_role_sum > 0) {
          confirmdesc = '<i class="confirm_icon"></i>该角色下存在用户，确定是否删除角色【'+show_name+'】？';
      } else {
          confirmdesc = '<i class="confirm_icon"></i>确定是否删除角色【'+show_name+'】？';
      }
	  parent.layer.confirm(confirmdesc, {
	  btn: ['确定','取消'] //按钮
	  }, function(){
		  parent.layer.closeAll();
	      var url = '/index.php/admin/role/delete';
	      var data = {role_id:role_id};
	      var fun = function(data){
			  alertbox(data);
			  if(data.status == 'success'){
				  location.reload();
			  }
	      }
	
	      $.post(url,data,fun,'json');
	      
	  }, function(){
	     
	  });
	});
	
	//角色状态切换
	$(".role_toggle_status").on('click',function() {
		  var role_id = $(this).attr('value');
		  var show_name = $(this).attr('show-name');
		  var user_role_sum = $(this).attr('user-role-sum');
		  var role_status = $(this).attr('role-status');
		  //var sname = (role_status==0) ? '启用' : '禁用';
		  var data_original_title = $(this).attr('data-original-title');
		  var confirmdesc = '';
		  if(user_role_sum > 0 && role_status==1) {
			  confirmdesc = '<i class="confirm_icon"></i>该角色下存在用户，确定是否'+data_original_title+'角色【'+show_name+'】？';
		  } else {
			  confirmdesc = '<i class="confirm_icon"></i>确定是否'+data_original_title+'角色【'+show_name+'】？';
		  }
		  parent.layer.confirm(confirmdesc, {
		  btn: ['确定','取消'] //按钮
		  }, function(){
			  parent.layer.closeAll();
		      var url = '/index.php/admin/role/toggle_status';
		      var data = {role_id:role_id};
		      var fun = function(data){
			      if(data.status == 'success'){
			    	  location.reload();
			      }
			      alertbox(data);
		      }
		
		      $.post(url,data,fun,'json');
		      
		  }, function(){
		     
		  });
		});
	
	
    
    //给角色设置权限
    $(".role_set").on('click',function() {
    	var role_id = $(this).attr('value');
		var show_name = $(this).attr('show-name');
		var  load = parent.layer.load(0, {shade: [0.3,'#000']});
    	$("#layerdivid",parent.document).load('/index.php/Admin/role/set_role_page',function(data){
			parent.layer.close(load);
			if(is_layer(data)) {
    			var url1 = '/index.php/Admin/Role/get_role_function';
    			var data1 = {role_id:role_id};
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
    			      title:'设置权限【角色名称：'+show_name+'】',
    			      area: ['800px', '500px'], //宽高
    			      content: $('#role_set_box',parent.document),
    			      btn:['确定', '取消'],
    			      yes: function(){
    			        var url = '/index.php/Admin/role/set_role_page';
    			        var data=$("form[name = 'role_set_form']",parent.document).serialize() + '&role_id='+role_id;
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

	//选中（取消）某一类功能
    $(document).on('click',".role_check_all",function(){
    	var menu_id = $(this).attr("data-val");
    	var hasac = $(this).hasClass("checked");
    	//console.log(hasac);
    	$(".acid"+menu_id).each(function(i,n){
    		var hasc = $(this).hasClass("checked");
    		//console.log(hasc);
    		if(hasac != hasc) {
    			$(this).click();   			
    		}
    	});
    });

	//选择企业用户类型，来切换权限
	$(document).on('click',".enterprise_type",function(){
		var type=$(this).attr("data-value");//type 1:正常企业（放出所以权限），2.测试企业（默认隐藏权限）
		$("#enterprise_type").val(type);
		$(".role_check_all").each(function(i,n){
			var menu_id = $(this).attr("data-val");
			var chasc = $(this).hasClass("checked");
			if(type==2 && (menu_id==148 || menu_id==59)){
				if(false != chasc) {
					$(this).click();
					chasc=false;
				}
			}else{
				if(true != chasc) {
					$(this).click();
					chasc=true;
				}
			}
			$(".acid"+menu_id).each(function(i,n){
				var hasc = $(this).hasClass("checked");
				var menuid=$(this).attr("data-menuid");
				if(type==2 && (menuid==64 ||menuid==58 || menuid==118 || menuid==155 || menuid==136 || menuid==157)){ //默认隐藏的菜单id
					if(false != hasc) {
						$(this).click();
					}
				}else{
					if(chasc != hasc) {
						$(this).click();
					}
				}
			});
		});
	});
    
    //折扣详细查看
    $('.discount_show_btn').click(function(){
        var discount_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/Discount/show?discount_id='+discount_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'折扣信息',
                    area: ['500px', '250px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });
	//折扣详细查看
	$('.productdiscount_show_btn').click(function(){
		var discount_id = $(this).attr('value');
		var  load = parent.layer.load(0, {shade: [0.3,'#000']});
		$("#layerdivid",parent.document).load('/index.php/Admin/ProductDiscount/show?discount_id='+discount_id,function(data){
			parent.layer.close(load);
			if(is_layer(data)) {
				parent.layer.open({
					type: 1,
					title:'产品折扣信息',
					area: ['500px', '250px'], //宽高
					content: $('#detail_box',parent.document),
					btn:['关闭']
				});
			}
		})
	});
    
    //折扣设置
	$(".discount_add_btn").on('click',function(){
		var  load = parent.layer.load(0, {shade: [0.3,'#000']});
		$("#layerdivid",parent.document).load('/index.php/admin/discount/add',function(data){
			parent.layer.close(load);
			if(is_layer(data)) {
				parent.layer.open({
				      type: 1,
				      title:'折扣设置',
				      area: ['1000px', '600px'], //宽高
				      content: $('#discount_add_box',parent.document),
				      btn:['保存', '取消'],
				      yes: function(){
						/*if(!checkform('discount_add_form')){
							return false;
						}*/
						
				        var url = '/index.php/admin/discount/insert';
				        var data = $("form[name='discount_add_form']",parent.document).serialize();
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
	
	//新增折扣，选择运营商赋值
	$(document).on('click',".discount_radio_operator_id",function() {
		var discount_radio_operator_id = $(this).attr('data-value');
		$("input[name='discount_operator_id']").val(discount_radio_operator_id);
	});
	
	//新增折扣，点击左边代理商/企业
	$(document).on('click',".discount_add_proxy",function() {
		$(this).toggleClass("current");
	});
	
	//新增折扣，向右边转移代理商/企业
	$(document).on('click',".leftarrow",function() {
		$(".discount_add_proxy").each(function(i,n){
			if($(this).hasClass("current")){
				$("#rightdiscountadd").append($(this));
                $(this).removeClass("current");
				$(this).find("input").attr("checked","checked");
			}
		});
	});
	
	//新增折扣，向左边转移代理商/企业
	$(document).on('click',".rightarrow",function() {
		$(".discount_add_proxy").each(function(i,n){
			if($(this).hasClass("current")){
				$("#leftdiscountadd").append($(this));
                $(this).removeClass("current");
				$(this).find("input").removeAttr("checked");
			}
		});
	});
	
	//查询代理商/企业
    $(document).on('click',".discount_add_seape",function() {
    	var seape_kw = $(".discount_add_pekw").val();
    	if(seape_kw != '') {
    		//alert(seape_kw);
    		var url ='/index.php/Admin/Discount/searchpe';
            var data ={'seape_kw':seape_kw};
            var fun = function(data){
                if(data.status == 'success') {
                    //alert(data.data.arrproxy.length);
                	$("#leftdiscountadd").empty();
                	if(data.data.arrproxy.length > 0) {
                		var ali = '';
                		$.each(data.data.arrproxy, function(i, n){
                			ali += '<li class="discount_add_proxy"><a href="javascript:void(0);">'+n.proxy_name;
                	    	ali += '<input type="checkbox" name="proxy_ids[]" value="'+n.proxy_id+'" class="undis" /></li>';
                		});                		
                		$("#leftdiscountadd").append(ali);
                	}
                	if(data.data.arrenterpirse.length > 0) {
                		var ali = '';
                		$.each(data.data.arrenterpirse, function(i, n){
                			ali += '<li class="discount_add_proxy"><a href="javascript:void(0);">'+n.enterprise_name;
                	    	ali += '<input type="checkbox" name="enterprise_ids[]" value="'+n.enterprise_id+'" class="undis" /></li>';
                		}); 
                		$("#leftdiscountadd").append(ali);
                	}
                }
                //alertbox(data);
            }
            $.post(url,data,fun,'json');
    	} else {
    		data = {'status':'error', 'msg':'请填写要查询的用户名字!'};
    		alertbox(data);
    	}
	});
	
	//省份选择
	/*$(".discountset_operator .province_btn").click(function(event){
		alert(111);
		event.stopPropagation();
		var $top=parseInt($(this).offset().top)+30;
		var $left=$(this).offset().left;
		$("#province_box").css({position:"fixed",top:$top,left:$left,zIndex:2000}).show();
	});*/

	//编辑折扣
    $(".discount_edit").on('click',function() {
    	var discount_id = $(this).attr('value');
		var  load = parent.layer.load(0, {shade: [0.3,'#000']});
		$('#layerdivid',parent.document).load('/index.php/admin/discount/edit/discount_id/'+discount_id,function(data){
			parent.layer.close(load);
			if(is_layer(data)){
            parent.layer.open({
              type: 1,
              title:'编辑折扣',
              area: ['420px', '300px'], //宽高
              content: $('#discount_edit_box',parent.document),
              success:function(){
                    inputFocus("discount_edit_form");
                },
              btn:['确定', '取消'],
              yes: function(){
            	if(!checkform('discount_edit_form')){
					return false;
				}
                var url = '/index.php/admin/discount/update';
                var data = $("form[name='discount_edit_form']",parent.document).serialize();
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
        })
    });

	//编辑折扣
	$(".productdiscount_edit").on('click',function() {
		var discount_id = $(this).attr('value');
		var  load = parent.layer.load(0, {shade: [0.3,'#000']});
		$('#layerdivid',parent.document).load('/index.php/Admin/ProductDiscount/edit/discount_id/'+discount_id,function(data){
			parent.layer.close(load);
			if(is_layer(data)){
				parent.layer.open({
					type: 1,
					title:'编辑产品折扣',
					area: ['450px', '330px'], //宽高
					content: $('#discount_edit_box',parent.document),
					success:function(){
						inputFocus("discount_edit_form");
					},
					btn:['确定', '取消'],
					yes: function(){
						if(!checkform('discount_edit_form')){
							return false;
						}
						var url = '/index.php/Admin/ProductDiscount/update';
						var data = $("form[name='discount_edit_form']",parent.document).serialize();
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
		})
	});
	
  //公告添加
    $(".notice_add_btn").on('click',function(){
      var title = '新增公告';
      var area = ['450px', '350px'];
      var view_name = 'notice_add_box';
      var form_name = 'notice_add_form';
      var view_url = '/index.php/Admin/Sysnotices/add';
      var post_url = '/index.php/Admin/Sysnotices/insert';
      view_form(title,area,view_name,form_name,view_url,post_url,true); 

    });

  //公告状态修改
  $(".notice_toggle_status_btn").on('click',function(){
      var notice_id = $(this).attr('value');
      var status=$(this).attr('data-original-title');
      layer.confirm('<i class="confirm_icon"></i>确定是否【'+status+'】当前公告？', {
          title:"提示信息",
          btn: ['确定','取消'] //按钮
      }, function(){
          var url ='/index.php/Admin/Sysnotices/toggle_status';
          var data = {'notice_id':notice_id};
          var fun = function(data){
              if(data.status == 'success') {
                  setTimeout("location.reload()",1000);
              }
              alertbox(data);
          }
          $.post(url,data,fun,'json');
      });
  });

    //公告查看详情
    $(".sysnotices_show_btn").on('click',function(event){
		event.stopPropagation();
        var id = $(this).attr('value');
        var vtype = $(this).attr('vtype');
		var ud = $(this).attr('ud');
		//alert($("#announce").children(".tipcon").html());
		var notice_not_sum = $(".show_notice_not_sum").html();

		$(".announce_show").hide();
		var load = parent.layer.load(0, {shade: [0.3,'#000']});
		$("#layerdivid",parent.document).load('/index.php/Admin/Sysnotices/show?notice_id='+id+'&vtype='+vtype,function(data){
			parent.layer.close(load);
			if(is_layer(data)) {
				notice_notread(id);
				notice_sub1(notice_not_sum);
				if(ud == 1) {
					show_nextpre_notice(id);
				}
    			parent.layer.open({
                    type: 1,
                    title:'公告信息',
                    area: ['600px', '250px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });

    		}
    	});
    });

	//公告查看详情
	$(".sysnotices_read_btn").on('click',function(){
		var id = $(this).attr('value');
		var vtype = $(this).attr('vtype');
		var load = parent.layer.load(0, {shade: [0.3,'#000']});
		var notice_not_sum = $(".show_notice_not_sum",parent.document).html();
		$("#layerdivid",parent.document).load('/index.php/Admin/Sysnotices/me_show?notice_id='+id+'&vtype='+vtype,function(data){
			parent.layer.close(load);
			if(is_layer(data)) {
				//notice_notread1(id);
				notice_sub_notice(notice_not_sum);
				show_nextpre_notice1(id);
				parent.layer.open({
					type: 1,
					title:'公告信息',
					area: ['600px', '300px'], //宽高
					content: $('#detail_box',parent.document),
					btn:['关闭'],
					yes:function(){
						parent.layer.closeAll();
						location.reload();
					}
				});
			}
		});
	});
    
    //编辑公告
	$(".sysnotices_edit_btn").on('click',function(){
        var id = $(this).attr('value');
		var load = parent.layer.load(0, {shade: [0.3,'#000']});
		$("#layerdivid",parent.document).load('/index.php/Admin/Sysnotices/edit?notice_id='+id,function(data){
			parent.layer.close(load);
			if(is_layer(data)) {
				parent.layer.open({
			      type: 1,
			      title:'编辑公告',
			      area: ['450px', '350px'], //宽高
			      content: $('#notice_edit_box',parent.document),
			      btn:['确定', '取消'],
			      yes: function(){
					if(!checkform('notice_edit_form')){
						return false;
					}
			        var url = '/index.php/Admin/Sysnotices/update';
			        var data = $("form[name='notice_edit_form']",parent.document).serialize();
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

    //删除公告
    $(".sysnotices_delete_btn").on('click',function() {
        var notice_id = $(this).attr('value');
        var show_name = $(this).attr('show-name');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否删除公告【'+show_name+'】？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            parent.layer.closeAll();
            var url = '/index.php/admin/sysnotices/delete';
            var data = {notice_id:notice_id};
            var fun = function(data){
                if(data.status == 'success'){
                    location.reload();
                }
                alertbox(data);
            }

            $.post(url,data,fun,'json');

        }, function(){

        });
    });
	
	/*下一页*/
	
    $('.show_notice_next').on('click',function(){
		var notice_id = $(this).attr('value');
		var url = '/index.php/Admin/Index/get_notice_one';
		var data = {notice_id:notice_id,sort:1};
		var fun = function(data){
			var da = data.info;
			var info = data.res;
				if(da.status == 'success') {
					$(".show_notice_title_h5").text(da.notice_title);
					$(".show_notice_content_span").text(da.notice_content);
					$(".sysnotices_show_btn").attr('value',da.notice_id);
					$("#announce").attr('value',da.notice_id);
					$(".show_notice_pre").css('display','block');  //上一页
					$(".show_notice_next").css('display','block');
					$(".show_notice_pre").attr('value',da.notice_id);  //上一页
					$(".show_notice_next").attr('value',da.notice_id);  //下一页
					if(info==''){
						$(".show_notice_next").css('display','none');
						$(".show_notice_pre").css('display','block');  //上一页
					}
				}else{
					$(".show_notice_next").css('display','none');  //上一页
				}
		}
		$.post(url,data,fun,'json');
	});


	/*上一页*/
	$('.show_notice_pre').on('click',function(){
		var notice_id = $(this).attr('value');
		var url = '/index.php/Admin/Index/get_notice_one';
		var data = {notice_id:notice_id,sort:2};
		var fun = function(data){
			var da = data.info;
			var info = data.res;
			if(da.status == 'success') {
				$(".show_notice_title_h5").text(da.notice_title);
				$(".show_notice_content_span").text(da.notice_content);
				$(".sysnotices_show_btn").attr('value',da.notice_id);
				$("#announce").attr('value',da.notice_id);
				$(".show_notice_next").css('display','block');  //下一页
				$(".show_notice_pre").css('display','block');  //上一页
				$(".show_notice_pre").attr('value',da.notice_id);  //上一页
				$(".show_notice_next").attr('value',da.notice_id);  //下一页
				if(info==''){
					$(".show_notice_next").css('display','block'); //下一页
					$(".show_notice_pre").css('display','none');  //上一页
				}
			}else{
				$(".show_notice_pre").css('display','none');  //下一页
			}
		}
		$.post(url,data,fun,'json');
	});

	/*企业冻结新增功能*/
	$('.enterprise_freeze_add_btn').click(function(){
		var  load = parent.layer.load(0, {shade: [0.3,'#000']});
		$("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseFrozen/add ',function(data){
			if(is_layer(data)) {
				parent.layer.close(load);
				parent.layer.open({
					type: 1,
					title:'新增企业账户冻结',
					area: ['405px', '280px'], //宽高
					content: $('#add_box',parent.document),
					success:function(){
						inputFocus("enterpriseFrozen_add_form");
					},
					btn:['保存', '取消'],
					yes: function(index, layero){
						if(!checkform('enterpriseFrozen_add_form')){
							return false;
						}
						var url = '/index.php/Admin/EnterpriseFrozen/insert';
						var formData = new FormData($("form[name='enterpriseFrozen_add_form']",parent.document)[0]);

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

    /*查看企业账户冻结信息*/
    $('.enterprise_frozen_detailed_function').click(function() {
        var apply_id=$(this).attr('value');
        var load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseFrozen/show?apply_id='+apply_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'企业账户冻结解冻信息',
                    area: ['600px', '400px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
            }
        })
    });

    /** 企业账户冻结复审和初审 */
    $('.enterprise_freeze_approve_function').click(function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        var apply_id = $(this).attr('value');
        var approve_c = $(this).data('approve');
        var title='';
        if(approve_c=='freeze_approve_c'){
            title='企业账户冻结金额复审';
        }
        if(approve_c=='freeze_approve'){
            title='企业账户冻结金额初审';
        }
        if(approve_c=='relieve_approve_c'){
            title='企业账户解冻申请复审';
        }
        if(approve_c=='relieve_approve'){
            title='企业账户解冻申请初审';
        }
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseFrozen/'+approve_c+'?apply_id='+apply_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:title,
                    area: ['680px', '430px'], //宽高
                    content: $('#character_box',parent.document),
                    success:function(){
                        inputFocus("enterprise_frozen_approve_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(index, layero){
                        if(!checkform('enterprise_frozen_approve_form')){
                            return false;
                        }
                        var url='';
                        var data = $("form[name='enterprise_frozen_approve_form']",parent.document).serialize();
                        if(approve_c=='freeze_approve_c' || approve_c=='relieve_approve_c'){
                            url= '/index.php/Admin/EnterpriseFrozen/'+approve_c+'?operate=approve&tran=trans';
                        }else{
                            url = '/index.php/Admin/EnterpriseFrozen/'+approve_c+'?operate=approve';
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
                                        enterprise_transfer(data.msg,data.info,'/index.php/Admin/EnterpriseFrozen/'+approve_c+'?operate=approve');
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

    /*编辑企业账户冻结*/
    $('.enterprise_freeze_edit_function').click(function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        var apply_id = $(this).attr('value');
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseFrozen/edit?apply_id='+apply_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'编辑企业账户冻结',
                    area: ['405px', '340px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("enterprise_frozen_edit_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(index, layero){
                        if(!checkform('enterprise_frozen_edit_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/EnterpriseFrozen/update';
                        var data = $("form[name='enterprise_frozen_edit_form']",parent.document).serialize();
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

    /*删除代理商冻结*/
    $(".enterprise_freeze_delete_function").on("click",function(){
        var apply_id = $(this).attr('value');
        var deletemsg=$(this).data('deletemsg');
        var money=$(this).data('money');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否删除企业【'+deletemsg+'】账户冻结金额【'+toThousands(money)+'】元？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/EnterpriseFrozen/delete';
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

    /*企业解冻*/
    $('.enterprise_relieve_add_function').click(function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        var apply_id = $(this).attr('value');
        $("#layerdivid",parent.document).load('/index.php/Admin/EnterpriseFrozen/relieve?apply_id='+apply_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'企业账户解冻申请',
                    area: ['405px', '340px'], //宽高
                    content: $('#add_box',parent.document),
                    success:function(){
                        inputFocus("enterprise_frozen_relieve_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(index, layero){
                        if(!checkform('enterprise_frozen_relieve_form')){
                            return false;
                        }
                        var data = $("form[name='enterprise_frozen_relieve_form']",parent.document).serialize();
                        var url= '/index.php/Admin/EnterpriseFrozen/relieve?operate=approve&tran=trans';
                        var fun = function(data){
                            if(data.status == 'success') {
                                if(!data.info){
                                    alertbox(data);
                                    parent.layer.closeAll();
                                    location.reload();
                                }else{
                                    parent.layer.closeAll();
                                    enterprise_transfer(data.msg,data.info,'/index.php/Admin/EnterpriseFrozen/relieve?operate=approve');
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





})

/**
 * 选择活动 自动获取规则
 */
function selactivity() {
    var activity_id=$("#activity_id").val();
    if(activity_id==""){
        $("#activity_rule").val("");
    }else{
        $.post('/index.php/Admin/SceneActivity/add',{op:1,activity_id:activity_id},function(data){
            if(data) {
                $("#activity_rule").val(data.activity_rule);
            } else {
                $("#activity_rule").val("");
            }
        },'json');
    }
}

//html删除li
function notice_notread(id) {
	return ;
	$(".sysnotices_show_btnc"+id).remove();
	var lis = $(".index_noticelist").children();
	if(lis.length == 0) {
		$(".index_noticelistdiv").remove();
	}
}

//html删除li
function notice_notread1(id) {
	return ;
	//alert($(".sysnotices_show_btnc"+id,parent.document).html());
	$(".sysnotices_show_btnc"+id,parent.document).remove();
	var lis =$(".index_noticelist",parent.document).children();
	if(lis.length == 0) {
		//$(".index_noticelistdiv").remove();
		$(".index_noticelistdiv",parent.document).remove()
	}
}

/**
 * 未读公告数量
 */
function notice_sub1(sum) {
	var nsum = 0;
	if(sum > 1) {
		nsum = sum - 1;
	}
	$(".show_notice_not_sum").html(nsum);
}

/**
 * 未读公告数量
 */
function notice_sub_notice(sum) {
	var nsum = 0;
	if(sum > 1) {
		nsum = sum - 1;
	}
	$(".show_notice_not_sum",parent.document).html(nsum);
}

/**
 * 在右上角点击查看某公告后，此公告处应显示的内容
 */
var sorta = 1;
function show_nextpre_notice(notice_id) {
	if(sorta==""){
		sorta = 1;
	}
	var url = '/index.php/Admin/Index/get_notice_one';
	var data = {notice_id:notice_id,sort:sorta};
	var fun = function(data){
		if(data.info.status == 'success') {
			$(".show_notice_title_h5").text(data.info.notice_title);
			$(".show_notice_content_span").text(data.info.notice_content);
			$(".sysnotices_show_btn").attr('value',data.info.notice_id);
			$(".show_notice_pre").attr('value',data.info.notice_id);  //上一页
			$(".show_notice_next").attr('value',data.info.notice_id);  //下一页
			if(data.res==''){
				$(".show_notice_next").css('display','none'); //下一页
				$(".show_notice_pre").css('display','none');  //上一页
			}
		} else {
			if(sorta==1){
				sorta = 2;
				show_nextpre_notice(notice_id);
			}else {
				$(".sysnotices_show_btnc").hide();
			}
		}
	}
	$.post(url,data,fun,'json');
}

function show_nextpre_notice1(notice_id) {
	if(sorta==""){
		sorta = 1;
	}
	var url = '/index.php/Admin/Index/get_notice_one';
	var data = {notice_id:notice_id,sort:sorta};
	var fun = function(data){
		if(data.info.status == 'success') {
			$(".show_notice_title_h5",parent.document).text(data.info.notice_title);
			$(".show_notice_content_span",parent.document).text(data.info.notice_content);
			$(".sysnotices_show_btn",parent.document).attr('value',data.info.notice_id);
			$(".show_notice_pre",parent.document).attr('value',data.info.notice_id);  //上一页
			$(".show_notice_next",parent.document).attr('value',data.info.notice_id);  //下一页
			if(data.res==''){
				$(".show_notice_next",parent.document).css('display','none'); //下一页
				$(".show_notice_pre",parent.document).css('display','none');  //上一页
			}
		} else {
			if(sorta==1){
				sorta = 2;
				show_nextpre_notice1(notice_id);
			}else {
				$(".sysnotices_show_btnc",parent.document).hide();
			}
		}
	}
	$.post(url,data,fun,'json');
}


function show_next_notice(sort) {
    var url = '/index.php/Admin/Index/get_notice_one';
    var data = {notice_id:sort};
    var fun = function(data){
        //alertbox(data);
        if(data.status == 'success') {
            $(".show_notice_title_h5").text(data.info.notice_title);
            $(".show_notice_content_span").text(data.info.notice_content);
			$(".sysnotices_show_btn").val(data.info.notice_id);
        }
    }
    $.post(url,data,fun,'json');
}

