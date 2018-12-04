//校验工具
function CheckTool() {
	
	this.checkEmpty=function(inStr){
		return typeof(inStr)=='undefined'||inStr==null||inStr=="";
	};
	
	this.checkPhone=function(phone){
		var checkPhonePattern = /^(130|131|132|133|134|135|136|137|138|139|145|147|150|151|152|153|155|156|157|158|159|170|176|177|178|180|181|182|183|184|185|186|187|188|189)\d{8}$/;
		return checkPhonePattern.test(phone);
	};
	
	this.checkEmail=function(email){
		var checkEmailPattern= /^(\w-*\.*)+@(\w-?)+(\.\w{2,})+$/;
		return checkEmailPattern.test(email);
	};
	
	this.checkEmoji=function(emoji){
		//var checkEmojiPattern= /^[\ud83c-\ud83c][\udc00-\udfff]|[\ud83d-\ud83d][\udc00-\udfff]|[\u2600-\u27ff]$/;
		//var checkEmojiPattern = new RegExp("[\\u4E00-\\u9FFF]+","g");
		var checkEmojiPattern = new RegExp("[\\ud83c-\ud83c][\\udc00-\\udfff]|[\\ud83d-\\ud83d][\\udc00-\\udfff]|[\\u2600-\\u27ff]+","g");
		return checkEmojiPattern.test(emoji);
	};
	
	this.checkUserName=function(userName){
		return !this.checkEmpty(userName)&&(this.checkPhone(userName)||this.checkEmail(userName));
	};
	
	this.checkPwd=function(pwd){
		return !this.checkEmpty(pwd)&&pwd.length>=6;
	};
	
	this.checkIp=function(ip){
		var checkIpPattern= /^(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])$/;
		return checkIpPattern.test(ip);
	};
	//校验特殊字符，只能输入中文、英文、数字以及下划线
	this.checkSpecialCharacter=function(str){
		var checkSpecialCharacterPattern=/^[\u4e00-\u9fa5_a-zA-Z0-9]+$/;
		return checkSpecialCharacterPattern.test(str);
	};
	
	this.checkURL=function(url){
		var checkUrlPattern= /^((http|https|ftp)\:\/\/)?([a-zA-Z0-9\.\-]+(\:[a-zA-Z0-9\.&amp;%\$\-]+)*@)*((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|localhost|([a-zA-Z0-9\-]+\.)*[a-zA-Z0-9\-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(\:[0-9]+)*$/;
		return checkUrlPattern.test(url);
	};
	
	/**
	 * 端口验证
	 */
	this.checkPort=function(port){
		var checkPortPattern= /^\d{1,6}$/;
		return checkPortPattern.test(port);
	};
	
	/**
	 * qq
	 */
	this.checkQQ=function(qq){
		var checkqqPattern= /^\d{5,10}$/;
		return checkqqPattern.test(qq);
	};
	
	/**
	 *  电话号码正则表达式（支持手机号码，3-4位区号，7-8位直播号码，1－4位分机号）
	 */
	this.checkContactTel=function(tel){
		if(!this.checkPhone(tel)){
			var reg = /^(\d{4}|\d{3})-?(\d{7,8})$/;
			return reg.test(tel);
		}
		return true;
	};
	
}

var CheckTool=new CheckTool();