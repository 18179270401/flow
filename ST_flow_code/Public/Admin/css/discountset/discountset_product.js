/**
 * Created by Administrator on 2015/9/6.
 */
$(document).ready(function(){
    datable = $('table').dataTable({
        "autoWidth": false,
        'bDestory':true,
        "aoColumnDefs": [  //去除不要搜索的列 下面是第2,3,6列不参与搜索
            { "bSearchable": false, "aTargets": [ 1 ] },
            { "bSearchable": false, "aTargets": [ 2 ] },
            { "bSearchable": false, "aTargets": [ 5 ] }],
        "order": [[ 0, "desc" ]],
        "oLanguage": {
            "sSearch": "搜索",
            "sLengthMenu": "每页显示 _MENU_ 条记录",
            "sZeroRecords": "抱歉， 没有找到",
            "sInfo": "从 _START_ 到 _END_ /共 _TOTAL_ 条数据",
            "sInfoEmpty": "没有数据",
            "sInfoFiltered": "(从 _MAX_ 条数据中检索)",
            "oPaginate": {
                "sFirst": "首页",
                "sPrevious": "前一页",
                "sNext": "后一页",
                "sLast": "尾页"
            },
            "sZeroRecords": "没有检索到数据"
        }
    });
});



//用户头上的删除按钮 实际是隐藏
$(".userDel").click(function(){
    $(this).parents(".userBox").css("display","none");
    $(this).parents(".userBox").attr("data-user","0");
});

//重选按钮
$("#userrech").click(function(){
    $(".userBox").css("display","none");
    $(".userBox").attr("data-user","0");
    SentUserGetDic();
});

//点击加号按钮，打开模态框
$("#openModal").click(function(){
    $('#UserModal').modal('show');
});

//模态框内的全选全清按钮
function choose(obj) {
    var table = document.getElementById("example1");
    var rows = table.rows.length;
    if (obj.checked == true) {
        for (var i = 1; i < rows; i++) {
            table.rows[i].cells[0].getElementsByTagName("input")[0].checked = true;
        }
    }
    else {
        for (var j = 1; j < rows; j++) {
            table.rows[j].cells[0].getElementsByTagName("input")[0].checked = false;
        }
    }
}

//点击添加按钮，模态框内checked的用户输出uid，并将隐藏的userBox显示，修改其新增属性data-user=1;
$("#userAdd").click(function(){
    //$("#province_id").find("option:selected").attr('selected',false);//重置省
    //$("#city_id").find("option:selected").attr('selected',false);//重置市
    var proxy_ids = '';
    var enterprise_ids = '';
    var proxys= new Array;
    var enterprises=new Array;
    $("#UserModal").find("td input:checked").each(function(){
        var type = $(this).parent().parent().find("[name='uid']").attr('type');
        if(type=='proxy'){
            var proxy_id = $(this).parent().parent().find("[name='uid']").text();
            proxy_ids += proxy_id+',';
            proxys.push(proxy_id);
        }else{
            var enterprise_id = $(this).parent().parent().find("[name='uid']").text();
            enterprise_ids += enterprise_id+',';
            enterprises.push(enterprise_id);
        }

    });

    proxy_ids = proxy_ids.substr(0,proxy_ids.length-1);
    enterprise_ids = enterprise_ids.substr(0,enterprise_ids.length-1);

    if(proxy_ids == '' && enterprise_ids == ''){$('#UserModal').modal('hide');return false;}

    var url = '/index.php/Admin/ProductDiscount/check_user';
    var data= {proxy_ids:proxy_ids,enterprise_ids:enterprise_ids,type:'check'};
    var fun = function(data){
        if(data.status == 'success'){
            if(data.msg == 'no'){
                top.layer.confirm('<i class="confirm_icon"></i>您选择的用户中折扣不一致，是否重新清除以前折扣设置？', {
                    title:'提示信息',
                    btn: ['确定','取消'] //按钮
                }, function(){
                    $(".userBox").css("display","none");
                    $(".userBox").attr("data-user","0");
                    top.layer.closeAll();
                    var url = '/index.php/Admin/ProductDiscount/check_user';
                    var data = {proxy_ids:proxy_ids,enterprise_ids:enterprise_ids,type:'delete'};
                    var fun = function(data){
                        top.layer.closeAll();
                        if(data.status == 'success'){

                            //模态框中选中的用户uid组成的数组
                            var checkedUser = new Array;
                            $("#UserModal").find("td input:checked").each(function(){
                                checkedUser.push($(this).parent().parent().find("[name='uid']").text());
                            });

                            //折扣选择用户栏中所有的用户的uid组成的数组
                            var Userbox = new Array;
                            $(".userBox").find("[name='uid']").each(function(){
                                Userbox.push($(this));
                            });

                            for(var i = 0 ; i < Userbox.length ; i ++ ){
                                var uid = $(Userbox[i]).val();
                                var type = $(Userbox[i]).attr("data");
                                for( var el in checkedUser )
                                {
                                    if(uid == checkedUser[el])
                                    {
                                        if(type=="proxy"&& proxys.indexOf(uid)!=-1 || type=="enterprise" && enterprises.indexOf(uid)!=-1){
                                            $(Userbox[i]).parents(".userBox").css("display","inline");
                                            $(Userbox[i]).parents(".userBox").attr("data-user","1");
                                        }
                                    }
                                }
                            }

                            SentUserGetDic(data.data);

                            $('#UserModal').modal('hide');

                        }else{
                            top.alertbox(data);
                        }

                    }

                    $.post(url,data,fun,'json');
                      
                  }, function(){
                     
                  });
            }else{
                if(top.layer != undefined){
                    top.layer.closeAll();
                }
                $(".userBox").css("display","none");
                $(".userBox").attr("data-user","0");
                var checkedUser = new Array;
                $("#UserModal").find("td input:checked").each(function(){
                    checkedUser.push($(this).parent().parent().find("[name='uid']").text())
                });

                //折扣选择用户栏中所有的用户的uid组成的数组
                var Userbox = new Array;
                $(".userBox").find("[name='uid']").each(function(){
                    Userbox.push($(this));
                });
                
                if('' != proxy_ids) {
                	for(var i = 0 ; i < Userbox.length ; i ++ ){
                        var uid = $(Userbox[i]).val();
                        var type = $(Userbox[i]).attr("data");
                        if("proxy" == type) {
                        	for( var el in checkedUser )
                        	{
                        		if(uid == checkedUser[el] )
                        		{
                        			$(Userbox[i]).parents(".userBox").css("display","inline");
                        			$(Userbox[i]).parents(".userBox").attr("data-user","1");
                        		}
                        	}                        	
                        }
                    }
                }
                
                if('' != enterprise_ids) {
                	for(var i = 0 ; i < Userbox.length ; i ++ ){
                        var uid = $(Userbox[i]).val();
                        var type = $(Userbox[i]).attr("data");
                        if("enterprise" == type) {
                        	for( var el in checkedUser )
                        	{
                        		if(uid == checkedUser[el] )
                        		{
                        			$(Userbox[i]).parents(".userBox").css("display","inline");
                        			$(Userbox[i]).parents(".userBox").attr("data-user","1");
                        		}
                        	}
                        }
                    }
                }
                
                SentUserGetDic(data.data);

                $('#UserModal').modal('hide');
            }
        }else{
            top.alertbox(data);
        }
    }
    $.post(url,data,fun,'json');
});

function SentUserGetDic(area,type) {
   $(".yd .areacss,.lt .areacss,.dx .areacss").find("input").val("");
    //将服务器返回的数据放回格子中
    var ydarea = $(".yd .areacss").find("input");
    var ltarea = $(".lt .areacss").find("input");
    var dxarea = $(".dx .areacss").find("input");
    if(area==undefined){
        return;
    }
    if(type!=1 && area.length>0){
        $('#province_id').find('option[value="'+area[0].province_id+'"]').attr('selected',true);//获取省
        $val=$('#province_id').find('option[value="'+area[0].province_id+'"]').val();
        $("#province_id").val($val);
        ///根据省得到市区
        //$('#city_id option[value='+area[0].city_id+']').attr('selected',true);//选择市区
        get_is_filter(1,area[0].city_id);
    }
    for (var i = 0; i < area.length; i++) {
        var dt = parseFloat(area[i].discount_number * 100) / 10.00;
        var d = parseFloat(dt.toFixed(3));
        var dtm = parseFloat(area[i].mindiscount * 100) / 10.00;
        var dm = parseFloat(dtm.toFixed(3));
        if (area[i].operator_id == 1) {
            for (var j = 0; j < ydarea.length; j++) {
                if ($(ydarea[j]).attr("data-areano") == area[i].size) {
                    if(parseFloat(d)!=10){
                        $(ydarea[j]).val(parseFloat(d));
                    }
                    $(ydarea[j]).attr('mindiscount', parseFloat(dm));
                }
            }
        }else if (area[i].operator_id == 2) {
            for (var j = 0; j < ltarea.length; j++) {
                if ($(ltarea[j]).attr("data-areano") == area[i].size) {
                    if(parseFloat(d)!=10){
                        $(ltarea[j]).val(parseFloat(d));
                    }
                    $(ltarea[j]).attr('mindiscount', parseFloat(dm));
                }
            }
        }
        else if (area[i].operator_id == 3) {
            for (var j = 0; j < dxarea.length; j++) {
                if ($(dxarea[j]).attr("data-areano") == area[i].size) {
                    if(parseFloat(d)!=10){
                        $(dxarea[j]).val(parseFloat(d));
                    }
                    $(dxarea[j]).attr('mindiscount', parseFloat(dm));
                }
            }
        }
    }
}

//地区的删除按钮，点击删除按钮会删除对应的地区，并将其折扣设置为10
$("[name='areadel']").click(function(){
    $(this).parents(".areacss").css("display","none");
    $(this).parents(".areacss").find("input").val(10.00);
});

//添加地区按钮
$("[name='area'] li:not(:first)").click(function(){
    //获取选中的地区的no
    var type=$("#province_city").val();
    if(type==1) {
        var selareano = $(this).find("span").attr("data-areano");
        var name = $(this).find("span").attr("data-name");
        if(name==undefined){
            return;
        }
        //新建一个存储某个运营商下所有地区对象的数组
        var thisarea = new Array;
        $(this).parents(".oprator").find(".areacss").each(function () {
            thisarea.push($(this));
        });
        var status = 1;
        //判断地区对象数组中的元素是否等于选中的no,是的话，让其显示
        for (var i = 0; i < thisarea.length; i++) {
            var areano = $(thisarea[i]).find("input").attr("data-areano");
            if (areano == selareano) {
                status = 2;
                break;
            }
        }
        if (status == 1) {
            var obj=$(this).parents(".oprator").find(".areaall");
            $.post("/index.php/Admin/Discount/get_proxy_discount", {province_id: selareano}, function (data) {
                if (data.info) {
                    var dt = parseFloat(data.info * 100) / 10.00;
                     d = parseFloat(dt.toFixed(3));
                    var html = '<div class="col-lg-6 areacss"> <div class="input-group"> <span class="input-group-addon" style="background-color: rgb(60, 141, 188);">' + name + '</span> <input type="text" class="form-control" mindiscount="'+d+'" value="10" maxlength="5" data-city="0" data-areano="' + selareano + '" /> <span class="input-group-btn"> <button class="btn bg-gray btn-flat" name="areadel" onclick="areadel(this);" type="button">✘</button> </span> </div> </div>';
                    $(obj).find(".areaadd").before(html);
                }
            }, "json");
    }
        init_ul();
    }else {
        var city = $(this).find("span").attr("data-city");
        if (city == "" || city==undefined) {
            var selareano = $(this).find("span").attr("data-areano");
            if(selareano == undefined){
                return;
            }
            var name = $(this).find("span").attr("data-name");
            $(".dropdown-menu-title .active").val(selareano);
            var val1 = $(this).parents(".areaadd").find(".dropdown-menu-title .active").data('value');
            val = $(".dropdown-menu-title .city-q").data('value');
            $.post("/index.php/Admin/Index/ajax_city", {province_id: selareano}, function (data) {
                if (data.info) {
                    var html = '';
                    for (var i = 0; i < data.info.length; i++) {
                        html += '<li onclick="c_city(this)" class="col-md-3"><a ><i class="fa fa-fw fa-map-marker"></i><span  data-name="'+ data.info[i]["city_name"] +'" data-city="' + data.info[i]["city_id"] + '">' + data.info[i]["city_name"] + '</span></a> </li>'
                    }
                    $(".city").html(html);
                    $("." + val).show();
                    $(".dropdown-menu-title .active").html(name);
                    $(".dropdown-menu-title .active").removeClass("active");
                    $("." + val1).hide();
                    $(".dropdown-menu-title .city-q").addClass("active");
                } else {
                    $(".city").hide().html("");
                }
            }, "json");
        }else{
            /*var name = $(this).find("span").attr("data-name");
            //新建一个存储某个运营商下所有地区对象的数组
            var thisarea = new Array;
            $(this).parents(".oprator").find(".areacss").each(function () {
                thisarea.push($(this));
            });
            var status = 1;
            //判断地区对象数组中的元素是否等于选中的no,是的话，让其显示
            for (var i = 0; i < thisarea.length; i++) {
                var areano = $(thisarea[i]).find("input").attr("data-city");
                if (areano == city) {
                    status = 2;
                    break;
                }
            }
            var v =$(".dropdown-menu-title .povince-q").val();//获取省份的id
            if(v!=1){
                v=0;
            }
            if (status == 1) {
                var html = '<div class="col-lg-6 areacss"> <div class="input-group"> <span class="input-group-addon" style="background-color: rgb(60, 141, 188);">' + name + '</span> <input type="text" class="form-control" value="10" maxlength="5" data-city="'+city+'" data-areano="' + v + '" /> <span class="input-group-btn"> <button class="btn bg-gray btn-flat" name="areadel" onclick="areadel(this);" type="button">✘</button> </span> </div> </div>';
                $(this).parents(".oprator").find(".areaall").find(".areaadd").before(html);
            }
            init-ul();*/
        }
    }
});


//保存按钮 提交
$("#submit").click(function () {
    var proxy_ids = '';
    var enterprise_ids = '';
    if(!check_discount())return false;
    $(".userBox[data-user='1']").each(function(){
       var id = $(this).find("[name='uid']").val();
       var type = $(this).find("[name='uid']").attr('data');

       if(type=='proxy'){
            proxy_ids += id+',';
        }else{
            enterprise_ids += id+',';
        }

    })
    
    proxy_ids = proxy_ids.substr(0,proxy_ids.length-1);
    enterprise_ids = enterprise_ids.substr(0,enterprise_ids.length-1);
    if( proxy_ids == '' &&  enterprise_ids == '' ){
        $data={'msg':'请选择代理商或者企业!','stauts':'error'};
        top.alertbox($data);
        return false;
    }
    parent.layer.load(0, {shade: [0.3,'#000']});  //给提交添加load
    var i =0;
    discount = {};
    $(".yd input[data-areano!='all']").each(function(){
        var val = $(this).val();
        if(val != 10 && val!=""){
            var size = $(this).attr('data-areano');
            var obj = {operator_id:1,size:size,discount_number:val};
            discount[i] = obj;
            i = i + 1 ;
        }
    })
    $(".lt input[data-areano!='all']").each(function(){
        var val = $(this).val();
        if(val != 10 && val!=""){
            var size = $(this).attr('data-areano');
            var obj = {operator_id:2,size:size,discount_number:val};
            discount[i] = obj;
            i = i + 1 ;
        }
    })
    $(".dx input[data-areano!='all']").each(function(){
        var val = $(this).val();
        if(val != 10 && val!=""){
            var size= $(this).attr('data-areano');
            var obj = {operator_id:3,size:size,discount_number:val};
            discount[i] = obj;
            i = i + 1 ;
        }
    })
    var province_id=$("#province_id").val();
    var city_id=$("#city_id").val();
    var url = '/index.php/Admin/ProductDiscount/set';
    var data = {discount:discount,enterprise_ids:enterprise_ids,proxy_ids:proxy_ids,province_id:province_id,city_id:city_id};
    var fun = function(data){
        top.alertbox(data);
        top.layer.closeAll();
    }
    $.post(url,data,fun,'json');
});

 $(".yd .areacss,.lt .areacss,.dx .areacss").find("input").focus(function(){
    $(this).css('color','black');
 })

$(".yd .areacss,.lt .areacss,.dx .areacss").find("input").keypress(function(event){
    var eventObj = event || e;
        var keyCode = eventObj.keyCode || eventObj.which;
         if ((keyCode >= 48 && keyCode <= 57) || (keyCode == 46) || (keyCode == 8) || (keyCode == 13) || (keyCode == 123) || (keyCode == 46) || (keyCode == 37) || (keyCode == 39) || (keyCode == 116) || (keyCode == 9))
            return true;
        else
            return false;
})
        
        

function check_discount(){
    var create_proxy_type = $("#create_proxy_type").val();
    var is_check = true;
    var input_list = $(".yd .areacss,.lt .areacss,.dx .areacss").find("input");
    input_list.each(function(){
    	var val = $(this).val();
        var min = $(this).attr('mindiscount');
        if(create_proxy_type == 1) {
            min = -1;
        }
        if(isNaN(min)) {
            min = 20;
        }

        if(val > 20){
            $(this).css('color','red');
            is_check = false;
        } else if(val < 20 && val > 0){
        	if(min > 0){
                if( val/1 < min/1){
                    $(this).css('color','red');
                    is_check = false;
                }
            }
        }else  if( val < 0){
            $(this).css('color','red');
            is_check = false;
        }
        
        
    })
    if(!is_check){
        //top.alertbox({msg:'折扣设置低于上级或者高于10折！',status:'error'});

    }
    return is_check;
}
//--------------------------------------------------------------------------------------------------------------
//新用户折扣js效果
$(document).ready(function(){
    //点击切换省市：
    $(".dropdown-menu-title span").click(function(){
        val=$(this).data('value');
        if(!$(this).hasClass("active")&& val!="city"){
            var val= $(this).parents(".areaadd").find(".dropdown-menu-title").find(".active").data('value');
            $(this).parents(".areaadd").find(".dropdown-menu-title").find(".active").removeClass("active");
            $("."+val).hide();
            $(this).addClass("active");
            val=$(this).data('value');
            $("."+val).show();
        }
    });
    //点击加号 显示出地区选项
    $(".dropdown-toggle-new").click(function(){
        init_ul();
        $(".dropdown-toggle-new").parents(".areaadd").find("ul").hide();
        $(this).parents(".areaadd").find("ul").show();
    });

    $(document).click(function(e) {
        e = e || window.event;
        var target = $(e.target);
        if(target.closest(".areaadd ul").length == 0) {
            init_ul();
        }
    });
    $(".yd .areaall,.lt .areaall,.dx .areaall").click(function(){
        $(this).find(".areacss").find("input").css('color','#555');
    });
});
//点击关闭按钮 去去除已设置折扣
function areadel(obj){
    $(obj).parents(".areacss").remove();
}
//选择城市 设置折扣
function c_city(obj){
    var name = $(obj).find("span").attr("data-name");  //获取市的名称
    var city = $(obj).find("span").attr("data-city");  // 获取市的id
    var selareano= $(obj).find("span").attr("data-areano");
    //新建一个存储某个运营商下所有地区对象的数组
    var thisarea = new Array;
    $(obj).parents(".oprator").find(".areacss").each(function () {
        thisarea.push($(this));
    });
    var status = 1;
    //判断地区对象数组中的元素是否等于选中的no,是的话，让其显示
    for (var i = 0; i < thisarea.length; i++) {
        var areano = $(thisarea[i]).find("input").attr("data-city");
        if (areano == city) {
            status = 2;
            break;
        }
    }
    var v =$(".dropdown-menu-title .povince-q").val();//获取省份的id 如果是全国就保留 其他的省都设置0；
    if(v!=1){
        v=0;
    }
    //如何没有则添加
    if (status == 1) {
        var wz=$(obj).parents(".oprator").find(".areaall");
        $.post("/index.php/Admin/Discount/get_proxy_discount", {province_id: selareano,city_id:city}, function (data) {
            if (data.info) {
                var dt = parseFloat(data.info * 100) / 10.00;
                d = parseFloat(dt.toFixed(3));
                var html = '<div class="col-lg-6 areacss"> <div class="input-group"> <span class="input-group-addon" style="background-color: rgb(60, 141, 188);">' + name + '</span> <input type="text" class="form-control" mindiscount="'+d+'" value="10" maxlength="5" data-city="'+city+'" data-areano="' + v + '" /> <span class="input-group-btn"> <button class="btn bg-gray btn-flat" name="areadel" onclick="areadel(this);" type="button">✘</button> </span> </div> </div>';
                $(wz).find(".areaadd").before(html);
            } else {
                $(".city").hide().html("");
            }
        }, "json");
        //$(obj).parents(".oprator").find(".areaall").find(".areaadd").find("ul").hide();
        //以下代码还原省市选择
        init_ul();
    }
}

//选择完省市后 初始化选择框
function  init_ul(){
    $(".dropdown-toggle-new").parents(".areaadd").find("ul").hide();
    $(".oprator").find(".areaall").find(".areaadd").find(".province-q").addClass("active");
    $(".oprator").find(".areaall").find(".areaadd").find(".city-q").removeClass("active");
    $(".oprator").find(".areaall").find(".areaadd").find(".province-q").html("地区");
    $(".province").show();
    $(".city li").remove();
}
//省市
function get_is_filter(id,city_id){
    var province_id=$("#province_id").val();
    if(id==1&&province_id==""){
        province_id=1;
    }
    if(province_id==1&&id!=1 || province_id==""){
        $("#filter_type").hide();
        //$("#filter_city").hide();
        $("#is_filter").val(0);
        $(".filter_radio1").addClass("checked");
        $(".filter_radio2").removeClass("checked");
        var html='<option value="" >请选择</option>';
        $("#city_id").html(html);
    }else{
        $("#filter_type").show();
        $.post("/index.php/Admin/Index/ajax_city",{province_id:province_id},function(data){
            if(data.info){
                var html='<option value="" >请选择</option>';
                for(var i=0; i < data.info.length;i++){
                        html+='<option value="'+data.info[i]["city_id"]+'">'+data.info[i]["city_name"]+'</option>';
                }
                $("#city_id").html(html);
                if(city_id!="" && city_id!=undefined){
                    $('#city_id option[value='+city_id+']').attr('selected',true);
                }else{
                    get_product_discount();
                }
            }else{
                $("#city_id").hide().html("");
                get_product_discount();
            }
            $("#filter_city").show();
        },"json");
    }
}

function get_product_discount(){
    var proxy_id=-1;
    var enterprise_id=-1;
    var id = $(".userBox[data-user='1']").find("[name='uid']").val();
    var type = $(".userBox[data-user='1']").find("[name='uid']").attr('data');
    if(type=='proxy'){
        proxy_id=id;
    }else{
        enterprise_id= id;
    }
    var province_id = $("#province_id").val();
    var city_id=$("#city_id").val();
    $(".yd .areacss,.lt .areacss,.dx .areacss").find("input").val("");
    $.post("/index.php/Admin/ProductDiscount/get_proxy_discount",{proxy_id:proxy_id,enterprise_id:enterprise_id,province_id:province_id,city_id:city_id},function(data){
        if(data.info){
            SentUserGetDic(data.info,1);
        }
    },"json");
}


