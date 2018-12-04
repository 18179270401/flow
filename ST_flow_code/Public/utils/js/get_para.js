function getUrlParam(name) {
	try {
		var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
		var r = window.location.search.substr(1).match(reg);

		if (r != null) return unescape(r[2]);
		return null;
	} catch (e) {
		return null;
	}

}

function setCookie(name, value) {
	var Days = 30;
	var exp = new Date();
	exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);
	document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString()+";path=/"; ;
}

Array.prototype.remove=function(dx) 
{ 
    if(isNaN(dx)||dx>this.length){return false;} 
    for(var i=0,n=0;i<this.length;i++) 
    { 
        if(this[i]!=this[dx]) 
        { 
            this[n++]=this[i] 
        } 
    } 
    this.length-=1 
} 

function getCookie(name) {
	var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");
	if (arr = document.cookie.match(reg))
		return unescape(arr[2]);
	else
		return null;
}

function HashMap() {
	/** Map 大小 **/
	var size = 0;
	/** 对象 **/
	var entry = new Object();

	/** 存 **/
	this.put = function(key, value) {
		if (!this.containsKey(key)) {
			size++;
		}
		entry[key] = value;
	}

	/** 取 **/
	this.get = function(key) {
		return this.containsKey(key) ? entry[key] : null;
	}

	/** 删除 **/
	this.remove = function(key) {
		if (this.containsKey(key) && (delete entry[key])) {
			size--;
		}
	}

	/** 是否包含 Key **/
	this.containsKey = function(key) {
		return (key in entry);
	}

	/** 是否包含 Value **/
	this.containsValue = function(value) {
		for (var prop in entry) {
			if (entry[prop] == value) {
				return true;
			}
		}
		return false;
	}

	/** 所有 Value **/
	this.values = function() {
		var values = new Array();
		for (var prop in entry) {
			values.push(entry[prop]);
		}
		return values;
	}

	/** 所有 Key **/
	this.keys = function() {
		var keys = new Array();
		for (var prop in entry) {
			keys.push(prop);
		}
		return keys;
	}

	/** Map Size **/
	this.size = function() {
		return size;
	}

	/* 清空 */
	this.clear = function() {
		size = 0;
		entry = new Object();
	}
}