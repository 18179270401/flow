/*工具JS*/

/**
 * 判断是否有权限弹层
 */
function is_layer(data) {

	if(data.indexOf('div') < 0) {
		var data = eval('(' + data + ')');
		if(data.status == 'error') {
            alertbox(data);
			return false;
		}else {
			parent.page_toThousands();
			parent.mCustomScrollbar();
			parent.parent.mCustomScrollbar();
			return true;
		}
	}else {
		parent.page_toThousands();
		parent.mCustomScrollbar();
		parent.parent.mCustomScrollbar();
		return true;
	}
}



/**
 * 和PHP函数in_array一样的功能
 */
function in_array(needle, haystack, argStrict) {
	var key = '',
    strict = !! argStrict;
	if (strict) {
	    for (key in haystack) {
	      if (haystack[key] === needle) {
	        return true;
	      }
	    }
	  } else {
	    for (key in haystack) {
	      if (haystack[key] == needle) {
	        return true;
	      }
	    }
	  }

	  return false;
}

/**
 * 和PHP函数strpos一样的功能(返回 needle 在 haystack 中首次出现的数字位置)
 */
function strpos(haystack, needle, offset) {
	var i = (haystack + '').indexOf(needle, (offset || 0));
	return i === -1 ? false : i;
}


/**
 * 和PHP函数explode一样的功能
 */

function explode(delimiter, string, limit) {
	if (arguments.length < 2 || typeof delimiter === 'undefined' || typeof string === 'undefined') return null;
	  if (delimiter === '' || delimiter === false || delimiter === null) return false;
	  if (typeof delimiter === 'function' || typeof delimiter === 'object' || typeof string === 'function' || typeof string ===
	    'object') {
	    return {
	      0: ''
	    };
	  }
	  if (delimiter === true) delimiter = '1';

	  // Here we go...
	  delimiter += '';
	  string += '';

	  var s = string.split(delimiter);

	  if (typeof limit === 'undefined') return s;

	  // Support for limit
	  if (limit === 0) limit = 1;

	  // Positive limit
	  if (limit > 0) {
	    if (limit >= s.length) return s;
	    return s.slice(0, limit - 1)
	      .concat([s.slice(limit - 1)
	        .join(delimiter)
	      ]);
	  }

	  // Negative limit
	  if (-limit >= s.length) return [];

	  s.splice(s.length + limit);
	  return s;
}

/**
 *	输入框验证信息
 */
function checkform(str,level){

	var reglist = {
			user_name:{data:'/^.+$/',msg:'请输入联系人'},
			email:{data:'/^(|([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+)$/',msg:'请输入正确的邮箱'},
			mobile:{data:'/^(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/',msg:'请输入正确的手机号码'},
			contact_tel:{data:'/^(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/',msg:'请填写正确的手机号码'},
			contact_name:{data:'/^.+$/',msg:'请输入联系人'},
			login_name:{data:'/^.+$/',msg:'请输入登录名称'},
			old_password:{data:'/^[a-zA-Z0-9_]{6,20}$/',msg:'密码长度应在6位—16位之间'},
			new_password:{data:'/^[a-zA-Z0-9_]{6,20}$/',msg:'密码长度应在6位—16位之间'},
			role_name:{data:'/^.+$/',msg:'请输入角色名称'},
			menu_name:{data:'/^.+$/',msg:'请输入菜单名称'},
            function_name:{data:'/^.+$/',msg:'请输入功能名称'},
            action_url:{data:'/^.+$/',msg:'请输入功能地址'},
            depart_name:{data:'/^.+$/',msg:'请输入部门名称'},
            proxy_name:{data:'/^.+$/',msg:'请输入代理商名称'},
            enterprise_name:{data:'/^.+$/',msg:'请输入企业名称'},
            tel:'/^[0-9-]{6,20}$/',
            channel_name:{data:'/^.+$/',msg:'请输入通道名称'},
            channel_code:{data:'/^.+$/',msg:'请输入通道文件名称'},
            product_name:{data:'/^.+$/',msg:'请输入产品名称'},
			product_code:{data:'/^.+$/',msg:'请输入产品编号'},
            number:{data:'/^.+$/',msg:'请输入产品编号'},
            size:{data:'/^.+$/',msg:'请输入流量包大小'},
            price:{data:'/^.+$/',msg:'请输入产品售价'},
          /*  discount:{data:'/^.+$/',msg:'产品折扣不能为空'},*/
		    apply_money:{data:'/^.+$/',msg:'请输入付款金额'},
		    repeat_new_password:{data:'',msg:'确认密码与新密码是否一致'},
		    identity_img_num:{data:'/^(|(\\d{15}$|^\\d{18}$|^\\d{17}(\\d|X|x)))$/',msg:'请输入正确的身份证号码'},
		    icense_img_num:{data:'/^(|(\\d{10,20}))$/',msg:'请输入正确的营业执照编号'},
		    beneficiary_name:{data:'/^.+$/',msg:'请输入产品编号'},
			mobile:{data:'/^.+$/',msg:'请输入手机号'},
			card_number:{data:'/^.+$/',msg:'请输入银行卡号'},
			content:{data:'/^.+$/',msg:'请输入拜访内容'},
			bank_account:{data:'/^.+$/',msg:'请输入开户行'},
			account_opening:{data:'/^.+$/',msg:'请输入开户省市'},
			//transaction_number:{data:'/^.+$/',msg:'请输入交易号'}


	}

	var check = true;

	var inputlist = $("form[name = '"+str+"'] input[type != 'hidden'][type != 'checkbox'][type != 'radio'][type != 'file'],form[name = '"+str+"'] textarea,form[name = '"+str+"'] select",top.document);

	
	$(inputlist).each(function(i,e){
		var isempty = $(e).attr('empty');
		var reg = $(e).attr('reg');
		var field = $(e).attr('field');
		var msg = $(e).attr('msg');
		var val = $(e).val();
		var isrepeat = $(e).attr('repeat');
		var type = $(e).attr('vtype');
		if(val != null){
			val = val.replace(/(^\s*)|(\s*$)/g, "");
		}
		if(typeof(msg) == 'string'){
			msg = msg.replace(/(^\s*)|(\s*$)/g, "");
		}else{
			msg = '';
		}

		if(typeof(isrepeat) == 'string'){
			var input = $("form[name = '"+str+"'] input[name = '"+isrepeat+"']",parent.document);
			if(level == 2){
				var input = $("form[name = '"+str+"'] input[name = '"+isrepeat+"']",parent.parent.document);
			}
			var repeat_val = input.val();
			if(val != repeat_val){
				error_msg($(e),msg);
				check = false;
			}
		}

		
		if(typeof(type) == 'string' ){
			if(val != ''){
				switch(type){
					case 'tel':
					vreg = /^[0-9-]{6,20}$/;
					break;

					case 'email':
					vreg = /^(|([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+)$/;
					break;

					case 'mobile':
					vreg = /^(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/;
					break;

					case 'password':
					vreg = /^[\S]{6,20}$/;
					break;

					case 'icense':
					vreg = /^(|(\d{10,20}))$/;
					break;

					case 'identity':
					vreg = /^(|(\d{15}$|^\d{18}$|^\d{17}(\d|X|x)))$/;
					break;
                    
                    case 'money':
                    vreg = /^[0-9]{1,12}(.[0-9]{1,3})?$/;
                    break;

                    case 'trannum':
					vreg = /^[a-zA-Z0-9_]{1,30}$/;
					break;

					case 'money2':
					vreg = /^-{0,1}[0-9]{1,7}(.[0-9]{1,3})?$/;
					break;
				}

				if(!vreg.test(val)){
					if(msg != ''){
						error_msg($(e),msg);
					}else{
						error_msg($(e),'请输入正确的'+field);
					}
					check = false;
				}
			}
		}

		if(reg){
			if(val != ''){
				if(!eval(reg).test(val)){
					if(msg != ''){
						error_msg($(e),msg);
					}else{
						error_msg($(e),'请输入正确的'+field);
					}
					
					check = false;

				}
			}

		}

		if(isempty != 'true'){
			if(val == ''){
				if(msg != ''){
					error_msg($(e),msg);
				}else{
					error_msg($(e),'请输入'+field);
				}

				check = false;

			}
		}


		

		/*
		var obj_name = $(e).attr('name');
		if(obj_name == 'repeat'){
			var repeat_val = $(e).val();
			var repeat_obj_name = $(e).attr('data');
			if(eval('reglist.'+obj_name+'_'+repeat_obj_name)){
				var val = $("form[name = '"+str+"'] input[name = '"+repeat_obj_name+"']").val();
				if(!repeat_val || repeat_val != val){
					reg = eval('reglist.'+obj_name+'_'+repeat_obj_name);

					$(e).parents("div[class='add_value']").nextAll("div[class='error']").remove();
			    	$(e).parents("div[class='add_list']").append('<div class="error">'+reg.msg+'</div>');
					//匹配成功
					 check = false;
				}
			}
		}else{
			if(eval('reglist.'+obj_name)){
	    		var obj_val = $(e).val();
	  			var reg = eval('reglist.'+obj_name);
			    if(!eval(reg.data).test(obj_val)){
			    	$(e).parents("div[class='add_value']").nextAll("div[class='error']").remove();
			    	$(e).parents("div[class='add_list']").append('<div class="error">'+reg.msg+'</div>');
					//匹配成功
					 check = false;
				}
	    	}
		}
    	*/

	});
    return check;
}


function error_msg(obj,msg){
	$(obj).parents(".add_value").nextAll("div[class='error']").remove();
	$(obj).parents(".add_list").append('<div class="error">'+msg+'</div>');

}


/**
 *将数字转换为大写金额，精确至厘，最大浮点数 12位整数+3位小数
 */
function digitUppercase(num) {  
        var fraction = ['角', '分' , '厘'];  
        var digit = [  
            '零', '壹', '贰', '叁', '肆',  
            '伍', '陆', '柒', '捌', '玖'  
        ];  
        var unit = [  
            ['元', '万', '亿'],  
            ['', '拾', '佰', '仟']  
        ];  
        var head = n < 0 ? '欠' : '';  
        n = Math.abs(n);  
        var s = '';  

        for (var i = 0; i < fraction.length; i++) {

            s += (digit[Math.floor(n  * Math.pow(10, i+1)) % 10] + fraction[i]).replace(/零./, ''); 
        }  
        
        s = s || '整';  
        n = Math.floor(n);  

        for (var i = 0; i < unit[0].length && n > 0; i++) {  
            var p = '';  
            for (var j = 0; j < unit[1].length && n > 0; j++) {  
                p = digit[n % 10] + unit[1][j] + p;  
                n = Math.floor(n / 10);  
            }  

            s = p.replace(/(零.)*零$/, '').replace(/^$/, '零') + unit[0][i] + s;  
        }  

        return head + s.replace(/(零.)*零元/, '元')  
            .replace(/(零.)+/g, '零')  
            .replace(/^整$/, '零元整');  
    };  




