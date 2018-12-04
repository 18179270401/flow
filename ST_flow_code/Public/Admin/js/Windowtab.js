$.extend({
	Window : function(options) {
		//默认配置
		var defaults = {
			id : '_w_',
	        title: '标题', //标题
	        customTitle : '', //自定义标题
	        tools : [], //工具栏
	        height : '100%', //默认高
	        width : '100%', //默认宽
	        close : true, //是否可关闭
			onClose : function(){}, //关闭回调
			onMin: function(){}, //缩小回调
			manager : null,
			url : '',
			fix : false,
			zIndex : 1
	    };
		//配置
		var settings = $.extend({}, defaults, options);		
		var tpl = '<div class="main_content">'
		    +'<iframe id="ifr_'+settings.id+'" scrolling="auto" framespacing="0" frameborder="0" border="0" style="width:100%; height: 100%;"  src="'+settings.url+'"></iframe>'
			+'</div>';
		
		
		var win = $(tpl);
		win.attr('id', settings.id)
			.css({
				'position': 'absolute',
				'z-index' : settings.zIndex,
				'height':settings.height,
				'width':settings.width,
			});	
		win.settings = settings;
		win.zIndex = settings.zIndex
		$.extend(win, {
			isMin : false,
			active : false,
			renderTo: function(e) {
				this.winProxy.appendTo(e);
				this.appendTo(e);
			},
			createTaskButton : function() {
				var _TaskButton = $('<li><span>'+this.settings.title+'</span><a href="javascript:;" class="close"><i class="close_icon"></i></a></li>');
				this.taskButton = _TaskButton;
				_TaskButton.window = this;
				_TaskButton.fix = this.isFix();
				return _TaskButton;
			},
			getId : function() {
				return this.settings.id;
			},
			setGroup : function (WinGroup) {
				this.windowGroup = WinGroup;
			},
			isFix : function() {
				return this.settings.fix;
			},
			close : function() {
				if ( this.settings.close==false ) return;
				this.remove();
				//this.winProxy.remove();
				this.windowGroup.closeWindow(this);
			},
			top : function() {
				this.windowGroup.setActiveWindow(this);
			},
			resize : function() {
				var content = this.find('iframe');
				var parent = this.parent();
				content.height(parent.height());
			},
			setzIndex : function(zIndex) {
				this.zIndex = zIndex;
				this.css({'z-index':zIndex});
				if ( this.winProxy ) {
					this.winProxy.css({'z-index':zIndex});
				}
			},
			getzIndex : function() {
				return this.zIndex;
			},
			deczIndex : function() {
				this.zIndex--;
				this.css({'z-index':this.zIndex});
				if ( this.winProxy ) {
					this.winProxy.css({'z-index':this.zIndex});
				}
			},
			setUrl : function(url) {
				var content = this.find('.main_content');
				var ifr = content.find('iframe');
				ifr[0].contentWindow.location=url;
			},
			reload : function() {
				var ifr = this.find('iframe');
				ifr.contents()[0].location.reload(true);
			}
		});
		return win;
	}
});