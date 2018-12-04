
$(function(){
    //添加菜单
    $(".menu_add_btn").on('click',function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/Menu/add',function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
    			parent.layer.open({
                    type: 1,
                    title:'新增菜单',
                    area: ['410px', '390px'], //宽高
                    content: $('#menu_add_box',parent.document),
                    success:function(){
                        inputFocus("menu_add_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(){
                        if(!checkform('menu_add_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/Menu/insert';
                        var data = $("form[name='menu_add_form']",parent.document).serialize();
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
    //修改菜单
    $(".menu_edit_btn").on('click',function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/Menu/edit?menu_id='+$(this).val(),function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
    			parent.layer.open({
                    type: 1,
                    title:'编辑菜单',
                    area: ['410px', '360px'], //宽高
                    content: $('#menu_edit_box',parent.document),
                    success:function(){
                        inputFocus("menu_edit_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform('menu_edit_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/Menu/update';
                        var data = $("form[name='menu_edit_form']",parent.document).serialize();
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
    //删除菜单
    $(".menu_delete_btn").on('click',function(){
        var menu_id = $(this).val();
        var menu_name = $(this).attr("data-value");
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否删除菜单【'+menu_name+'】？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/Menu/delete';
            var data ={'menu_id':menu_id};
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
    //修改菜单状态
    $(".menu_toggle_status_btn").on('click',function(){
        var menu_id = $(this).val(); //ID号
        var menu_name = $(this).attr("data-value"); //当前菜单名称
        var menu_status_name = $(this).attr("data-original-title"); //当前菜单状态名称
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否'+menu_status_name+'菜单【'+menu_name+'】？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/Menu/toggle_status';
            var data ={'menu_id':menu_id};
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

    //添加节点
    $(".menu_add_function").on('click',function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/Right/add?menu_id='+$(this).val(),function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'新增功能',
                    area: ['410px', '290px'], //宽高
                    content: $('#function_add_box',parent.document),
                    success:function(){
                        inputFocus("function_add_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(){
                        if(!checkform('function_add_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/Right/insert';
                        var data = $("form[name='function_add_form']",parent.document).serialize();
                        var fun = function(data){
                            alertbox(data);
                            if(data.status == 'success') {
                   	            parent.layer.closeAll();
                                //location.reload();
                            }
                        }
                        $.post(url,data,fun,'json');
                    }
                });
    		}
    	});
    });
    //节点编辑
    $(".right_edit_btn").on('click',function(){
        var function_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/Right/edit?function_id='+ function_id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'编辑功能',
                    area: ['410px', '290px'], //宽高
                    content: $('#right_edit_box',parent.document),
                    success:function(){
                        inputFocus("right_edit_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform('right_edit_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/Right/update';
                        var data = $("form[name='right_edit_form']",parent.document).serialize();
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
    //节点删除
    $(".right_delete_btn").on('click',function(){
        var function_id = $(this).attr('value');
        var function_name = $(this).attr('data-value');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否删除功能【'+function_name+'】？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/Right/delete';
            var data ={'function_id':function_id};
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
    //节点状态修改
    $(".right_toggle_status_btn").on('click',function(){
        var function_id = $(this).attr('value');
        var status=$(this).attr('data-original-title');
        var function_name = $(this).attr('data-value');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否'+status+'功能【'+function_name+'】？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/Right/toggle_status';
            var data ={'function_id':function_id};
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

    //添加通道
    $(".channel_add_btn").on('click',function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/Channel/add',function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'新增通道',
                    area: ['410px', '410px'], //宽高
                    content: $('#channel_add_box',parent.document),
                    success:function(){
                        inputFocus("channel_add_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(){
                        if(!checkform('channel_add_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/Channel/insert';
                        var data = $("form[name='channel_add_form']",parent.document).serialize();
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
    //修改通道
    $(".channel_edit_btn").on('click',function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/Channel/edit?channel_id='+$(this).val(),function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'编辑通道',
                    area: ['410px', '410px'], //宽高
                    content: $('#channel_edit_box',parent.document),
                    success:function(){
                        inputFocus("channel_edit_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform('channel_edit_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/Channel/update';
                        var data = $("form[name='channel_edit_form']",parent.document).serialize();
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
    //通道状态修改
    $(".channel_toggle_status_btn").on('click',function(){
        var channel_id = $(this).attr('value');
        var status=$(this).attr('data-original-title');
        var channel_name = $(this).attr('data-value');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否'+status+'通道【'+channel_name+'】？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/Channel/toggle_status';
            var data ={'channel_id':channel_id};
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
    
    //通道发短信状态修改
    $(".channel_toggle_message_btn").on('click',function(){
        var channel_id = $(this).attr('value');
        var status=$(this).attr('data-original-title');
        var channel_name = $(this).attr('data-value');
        parent.layer.confirm('<i class="confirm_icon"></i>确定通道【'+channel_name+'】'+status+'功能？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/Channel/toggle_message';
            var data ={'channel_id':channel_id};
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
    //代理商发短信状态修改
    $(".proxy_toggle_message_btn").on('click',function(){
        var proxy_id = $(this).attr('value');
        var status=$(this).attr('data-original-title');
        var proxy_name = $(this).attr('data-value');
        parent.layer.confirm('<i class="confirm_icon"></i>确定代理商【'+proxy_name+'】'+status+'功能？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/Proxy/toggle_message';
            var data ={'proxy_id':proxy_id};
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
    //企业发短信状态修改
    $(".enterprise_toggle_message_btn").on('click',function(){
        var enterprise_id = $(this).attr('value');
        var status=$(this).attr('data-original-title');
        var enterprise_name = $(this).attr('data-value');
        parent.layer.confirm('<i class="confirm_icon"></i>确定代理商【'+enterprise_name+'】'+status+'功能？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/Enterprise/toggle_message';
            var data ={'enterprise_id':enterprise_id};
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

    //添加通道产品
    $(".channelproduct_add_btn").on('click',function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ChannelProduct/add',function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'新增通道产品',
                    area: ['570px', '450px'], //宽高
                    content: $('#channelproduct_add_box',parent.document),
                    success:function(){
                        inputFocus("channelproduct_add_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(){
                        if(!checkform('channelproduct_add_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/ChannelProduct/insert';
                        var data = $("form[name='channelproduct_add_form']",parent.document).serialize();
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

    //修改通道产品
    $(".channelproduct_edit_btn").on('click',function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ChannelProduct/edit?product_id='+$(this).val(),function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'编辑通道产品',
                    area: ['570px', '450px'], //宽高
                    content: $('#channelproduct_edit_box',parent.document),
                    success:function(){
                        inputFocus("channelproduct_edit_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform('channelproduct_edit_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/ChannelProduct/update';
                        var data = $("form[name='channelproduct_edit_form']",parent.document).serialize();
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
    //删除通道产品
    $(".channelproduct_delete_btn").on('click',function(){
        var product_id = $(this).attr('value');
        parent.layer.confirm('<i class="confirm_icon"></i>确定删除当前通道产品吗？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/ChannelProduct/delete';
            var data ={'product_id':product_id};
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

    //通道状态修改
    $(".channelproduct_toggle_status_btn").on('click',function(){
        var product_id = $(this).attr('value');
        var status=$(this).attr('data-original-title');
        var product_name = $(this).attr("data-value");
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否'+status+'通道产品【'+product_name+'】？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/ChannelProduct/toggle_status';
            var data ={'product_id':product_id};
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

    //添加流量包
    $(".flow_add_btn").on('click',function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/Flow/add',function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'新增流量包',
                    area: ['600px', '360px'], //宽高
                    content: $('#flow_add_box',parent.document),
                    success:function(){
                        inputFocus("flow_add_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(){
                        if(!checkform('flow_add_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/Flow/insert';
                        var data = $("form[name='flow_add_form']",parent.document).serialize();
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

    //修改流量包
    $(".flow_edit_btn").on('click',function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/Flow/edit?product_id='+$(this).val(),function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'编辑流量包',
                    area: ['600px', '360px'], //宽高
                    content: $('#flow_edit_box',parent.document),
                    success:function(){
                        inputFocus("flow_edit_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform('flow_edit_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/Flow/update';
                        var data = $("form[name='flow_edit_form']",parent.document).serialize();
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

    //产品状态修改
    $(".flow_toggle_status_btn").on('click',function(){
        var product_id = $(this).attr('value');
        var status=$(this).attr('data-original-title');
        var product_name = $(this).attr("data-value");
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否'+status+'流量包【'+product_name+'】？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/Flow/toggle_status';
            var data ={'product_id':product_id};
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

    //新增用户通道
    $(".channel_user_add_box").on('click',function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ChannelUser/add',function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
    			parent.layer.open({
                    type: 1,
                    title:'新增用户通道',
                    area: ['410px', '240px'], //宽高
                    content: $('#channel_user_add_box',parent.document),
                    success:function(){
                        inputFocus("channel_user_add_box");
                    },
                    btn:['保存', '取消'],
                    yes: function(){
                        if(!checkform('channel_user_add_box')){
                            return false;
                        }
                        var url = '/index.php/Admin/ChannelUser/insert';
                        var data = $("form[name='channel_user_add_box']",parent.document).serialize();
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
    //编辑用户通道
    $(".channeluser_edit_btn").on('click',function(){
        var id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ChannelUser/edit?channel_user_id='+id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
    			parent.layer.open({
                    type: 1,
                    title:'编辑用户通道',
                    area: ['410px', '260px'], //宽高
                    content: $('#channel_user_edit_box',parent.document),
                    success:function(){
                        inputFocus("channel_user_edit_box");
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform('channel_user_edit_box')){
                            return false;
                        }
                        var url = '/index.php/Admin/ChannelUser/update';
                        var data = $("form[name='channel_user_edit_box']",parent.document).serialize();
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
    //用户通道详细
    $(".channel_user_show_btn").on('click',function(){
        var id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ChannelUser/show?channel_user_id='+id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
    			parent.layer.open({
                    type: 1,
                    title:'用户通道信息',
                    area: ['350px', '240px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
    		}
    	});
    });
    //删除用户通道
    $(".channeluser_delete_btn").on('click',function(){
        var channel_user_id = $(this).attr('value');
        var channel_name = $(this).attr('data-name');
        var channel_title = $(this).attr('data-title');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否删除用户【'+channel_name+'】使用通道【'+channel_title+'】？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/ChannelUser/delete';
            var data ={'channel_user_id':channel_user_id};
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

    //新增通道折扣
    $(".channel_discount_add_btn").on('click',function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ChannelDiscount/add',function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
    			parent.layer.open({
                    type: 1,
                    title:'新增通道折扣',
                    area: ['460px', '340px'], //宽高
                    content: $('#channel_discount_add_box',parent.document),
                    success:function(){
                        inputFocus("channel_discount_add_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(){
                        if(!checkform('channel_discount_add_form')){
                            return false;
                        }
                        //var data = $("form[name='channel_discount_add_form']",parent.document).serialize();
                        var url = '/index.php/Admin/ChannelDiscount/insert';
                        var data = $("form[name='channel_discount_add_form']",parent.document).serialize();
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

    //编辑通道折扣
    $(".channel_discount_edit_btn").on('click',function(){
        var id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ChannelDiscount/edit?discount_id='+id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
    			parent.layer.open({
                    type: 1,
                    title:'编辑通道折扣',
                    area: ['420px', '330px'], //宽高
                    content: $('#channel_discount_edit_box',parent.document),
                    success:function(){
                        inputFocus("channel_discount_edit_form");
                    },
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform('channel_discount_edit_form')){
                            return false;
                        }
                        //var data = $("form[name='channel_discount_edit_form']",parent.document).serialize();
                        var url = '/index.php/Admin/ChannelDiscount/update';
                        var data = $("form[name='channel_discount_edit_form']",parent.document).serialize();
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

    //通道折扣详细
    $(".channel_discount_show_btn").on('click',function(){
        var id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/ChannelDiscount/show?discount_id='+id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
    			parent.layer.open({
                    type: 1,
                    title:'通道折扣信息',
                    area: ['350px', '300px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });
    		}
    	});
    });

    //删除通道折扣
    $(".channel_discount_delete_btn").on('click',function(){
        var id = $(this).attr('value');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否删除当前通道折扣？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/ChannelDiscount/delete';
            var data ={'id':id};
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

    //新增活动
    $(".sceneactivity_add_btn").on('click',function(){
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/SceneActivity/add',function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'新增活动',
                    area: ['460px', '520px'], //宽高
                    content: $('#sceneactivity_add_box',parent.document),
                    success:function(){
                        inputFocus("sceneactivity_add_form");
                    },
                    btn:['保存', '取消'],
                    yes: function(){
                        if(!checkform('sceneactivity_add_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/SceneActivity/insert';
                        var data = $("form[name='sceneactivity_add_form']",parent.document).serialize();
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

    //活动查看详情
    $(".scene_activity_show_btn").on('click',function(){
        var id = $(this).attr('value');
        var load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/SceneActivity/show?user_activity_id='+id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {

                parent.layer.open({
                    type: 1,
                    title:'活动信息',
                    area: ['800px', '450px'], //宽高
                    content: $('#detail_box',parent.document),
                    btn:['关闭']
                });

            }
        });
    });

    //编辑活动
    $(".scene_activity_edit_btn").on('click',function(){
        var id = $(this).attr('value');
        var load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/SceneActivity/edit?user_activity_id='+id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'编辑活动',
                    area: ['460px', '520px'], //宽高
                    content: $('#sceneactivity_edit_box',parent.document),
                    btn:['确定', '取消'],
                    yes: function(){
                        if(!checkform('sceneactivity_edit_form')){
                            return false;
                        }
                        var url = '/index.php/Admin/SceneActivity/update';
                        var data = $("form[name='sceneactivity_edit_form']",parent.document).serialize();
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
    //编辑活动状态
    $(".scene_activity_toggle_status_btn").on('click',function(){
        var id = $(this).attr('value');
        var status=$(this).attr('data-original-title');
        var name = $(this).attr('data-value');
        parent.layer.confirm('<i class="confirm_icon"></i>确定是否'+status+'活动【'+name+'】？', {
            title:"提示信息",
            btn: ['确定','取消'] //按钮
        }, function(){
            var url ='/index.php/Admin/SceneActivity/toggle_status';
            var data ={'id':id};
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

    //流量券新增活动
    $(".ticket_sceneactivity_add_btn").on('click',function(){
      var  load = parent.layer.load(0, {shade: [0.3,'#000']});
      $("#layerdivid",parent.document).load('/index.php/Admin/Flowticket/add',function(data){
          parent.layer.close(load);
          if(is_layer(data)) {
              parent.layer.open({
                  type: 1,
                  title:'新增活动',
                  area: ['460px', '520px'], //宽高
                  content: $('#sceneactivity_add_box',parent.document),
                  success:function(){
                      inputFocus("sceneactivity_add_form");
                  },
                  btn:['保存', '取消'],
                  yes: function(){
                      if(!checkform('sceneactivity_add_form')){
                          return false;
                      }
                      var url = '/index.php/Admin/Flowticket/insert';
                      var data = $("form[name='sceneactivity_add_form']",parent.document).serialize();
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

    //流量券活动查看详情
    $(".ticket_scene_activity_show_btn").on('click',function(){
      var id = $(this).attr('value');
      var load = parent.layer.load(0, {shade: [0.3,'#000']});
      $("#layerdivid",parent.document).load('/index.php/Admin/Flowticket/show?user_activity_id='+id,function(data){
          parent.layer.close(load);
          if(is_layer(data)) {

              parent.layer.open({
                  type: 1,
                  title:'活动信息',
                  area: ['800px', '450px'], //宽高
                  content: $('#detail_box',parent.document),
                  btn:['关闭']
              });

          }
      });
    });
    //流量券编辑活动
    $(".ticket_scene_activity_edit_btn").on('click',function(){
      var id = $(this).attr('value');
      var load = parent.layer.load(0, {shade: [0.3,'#000']});
      $("#layerdivid",parent.document).load('/index.php/Admin/Flowticket/edit?user_activity_id='+id,function(data){
          parent.layer.close(load);
          if(is_layer(data)) {
              parent.layer.open({
                  type: 1,
                  title:'编辑活动',
                  area: ['460px', '520px'], //宽高
                  content: $('#sceneactivity_edit_box',parent.document),
                  btn:['确定', '取消'],
                  yes: function(){
                      if(!checkform('sceneactivity_edit_form')){
                          return false;
                      }
                      var url = '/index.php/Admin/Flowticket/update';
                      var data = $("form[name='sceneactivity_edit_form']",parent.document).serialize();
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
    // 流量券活动配置流量包
    $(".ticket_scene_activity_set_btn").on('click',function(){
        var user_activity_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load("/index.php/Admin/Flowticket/set?user_activity_id="+user_activity_id+"&type=show",function(data){
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
                        var url = '/index.php/Admin/Flowticket/set?type=update';
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

    $(document).on('change',".file_edit_sence",function(){
        var id =$(this).attr("id");
        set_collection(id);
    });
    //删除流量券活动
    $(".ticket_scene_activity_delete_btn").on('click',function() {
        var user_activity_id = $(this).attr('value');
        var activity_name = $(this).attr('data-name');
        parent.layer.confirm(
            '<i class="confirm_icon"></i>确定是否删除活动名称【'+activity_name+'】？',
            {btn: ['确定','取消']},
            function(){
                parent.layer.closeAll();
                var url = '/index.php/Admin/Flowticket/delete';
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
    //流量券活动详细信息
    $(".ticket_activity_detailed_btn").on('click',function(){
        var user_activity_id = $(this).attr('value');
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load("/index.php/Admin/Flowticket/detailed?user_activity_id="+user_activity_id+"&type=show",function(data){
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
                        var url = '/index.php/Admin/Flowticket/detailed?type=operation';
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

    //查看领取流量
    $(".scenerecord_show_btn").on('click',function(){
        var id = $(this).attr('value');
        var type=$(this).data("type");
        var h="";
        if(type==3){
            h="360px";
        }else{
            h="420px";
        }
        var  load = parent.layer.load(0, {shade: [0.3,'#000']});
        $("#layerdivid",parent.document).load('/index.php/Admin/SceneRecord/show?record_id='+id,function(data){
            parent.layer.close(load);
            if(is_layer(data)) {
                parent.layer.open({
                    type: 1,
                    title:'领取流量信息',
                    area: ['360px', h], //宽高
                    content: $('#scenerecord_show_box',parent.document),
                    success:function(){
                        inputFocus("scenerecord_show_box");
                    },
                    btn:['关闭']
                });
            }
        });
    });

})

//弹出是否确定的询问框
function transfer_box(content,da,applyurl){
        parent.layer.confirm('<i class="confirm_icon"></i>' + content, {
            title: "提示信息",
            btn: ['确定', '取消'] //按钮
        }, function () {
            var url = applyurl;
            var data = da;
            var fun = function (data) {
                alertbox(data);
                if (data.status == 'success') {
                    location.reload();
                }
                channel_account_add_btn_ststus = 1;
                parent.layer.closeAll();
            }
            if(channel_account_add_btn_ststus==1) {
                channel_account_add_btn_ststus = 2;
                $.post(url, data, fun, 'json');
            }
        }, function () {
            location.reload();
        });
}

//列出当前所选运营商和地区下的产品
function product_list(){
    var operator_id = $("#operator_id").val();
    var province_id = $("#province_id").val();
    if(operator_id!="" && province_id!=""){
        $.post("/index.php/Admin/Flow/read_operation",{operator_id:operator_id,province_id:province_id,type:'channel_product_name'},function(data){
            var html = '';
            if(data.status == 'success'){
                var list = data.list;
                if(list){
                    html += '<option value="">请选择</option>';
                    for(var i = 0;i < list.length;i++){
                        html += '<option value="'+list[i].product_name+'">'+list[i].product_name+'</option>';
                    }
                }else{
                    html = '<option value="">请选择</option>';
                }
            }else{
                html = '<option value="">请选择</option>';
            }
            $("#product_name").html(html);
            $("#channel_id , #back_channel_id").html('<option value="">请选择</option>');
        },"json");
    }else{
        $("#product_name").html('<option value="">请选择</option>');
        $("#channel_id , #back_channel_id").html('<option value="">请选择</option>');
    }
}

//列出当前所选运营商、地区和产品下的通道
function channel_list(){
    var operator_id = $("#operator_id").val();
    var province_id = $("#province_id").val();
    var product_name = $("#product_name").val();
    if(operator_id!="" && province_id!="" && product_name!=""){
        $.post("/index.php/Admin/Flow/read_operation",{operator_id:operator_id,province_id:province_id,product_name:product_name,type:'channel'},function(data){
            var html = '';
            if(data.status == 'success'){
                var list = data.list;
                if(list){
                    html += '<option value="">请选择</option>';
                    for(var i = 0;i < list.length;i++){
                        html += '<option value="'+list[i].channel_id+'">'+list[i].channel_name+'</option>';
                    }
                }else{
                    html = '<option value="">请选择</option>';
                }
            }else{
                html = '<option value="">请选择</option>';
            }
            $("#channel_id , #back_channel_id").html(html);
        },"json");
    }else{
        $("#channel_id , #back_channel_id").html('<option value="">请选择</option>');
    }
}
