$.extend({
	TaskBar : function(taskBarEl) {
		this.taskBar = $(taskBarEl);
		this.moreButton = $('<a href="javascript:;" class="more_box"><i class="icon_more"></i></a>').appendTo(this.taskBar.parent()).hide();
		var _MoreTaskButtonContainer = 
			'<div class="more_tab">'
			+	'<div class="more_tab_top"></div>'
			+	'<div class="more_tab_mid">'
			+	'	<ul class="more_tab_list" >'
			+	'	</ul>'
			+	'</div>'
			+	'<div class="more_tab_bot"></div>'
			+'</div>';
		
		_TaskBar = this;
		this.moreTaskButtons = $(_MoreTaskButtonContainer)
			.appendTo(this.taskBar.parent())
			.mouseleave(function(){
				_TaskBar.moreTaskButtons.hide();
			})
			.hide();
		
		this.moreButton.click(function(){
			var len =  _TaskBar.moreTaskButtons.find('.more_tab_list li').length;
			if ( len >0 ) {
				_TaskBar.moreTaskButtons.toggle();
				_TaskBar.moreTaskButtons.height(32 * len + 20);
				_TaskBar.moreTaskButtons.css("margin-top",$(".tab_box").height());
			}
		});
		this.taskButtons = [];
		$.extend(this, {
			addTaskButton : function(TaskButton) {
				this.taskButtons.push(TaskButton);
				this.taskBar.append(TaskButton);
				TaskButton.buttonWidth = TaskButton.outerWidth(true) + 5;
				this.regTaskButtonEvent(TaskButton);
				while ( this.isOverFlowTaskBar() ) {
					this.addMoreTaskButton();
				}
				this.hideOrShowMoreButton();
			},
			regTaskButtonEvent : function(TaskButton) {
				var $this = this;
				TaskButton.find(".close").bind("click",function(){
				   TaskButton.window.close();
				});
				TaskButton.click(function(){
					var Window = TaskButton.window;
					Window.top();
					$this.addTaskButtonFromMore(TaskButton);
				});
			},
			setWinGroup : function(WinGroup) {
				this.windowGroup = WinGroup;
			},
			addMoreTaskButton : function() {
				var len = this.windowGroup.windows.length;
				var tabList = this.moreTaskButtons.find('.more_tab_list');
				for ( var i=0; i<len; i++ ) {
					var win = this.windowGroup.windows[i];
					var taskButton = win.taskButton;
					if ( taskButton.more!=true &&  taskButton.fix==false ) {
						taskButton.remove();
						taskButton.appendTo(tabList);
						this.regTaskButtonEvent(taskButton);
						taskButton.more = true;
						return taskButton;
					}
				}
			},
			getMoreTaskButton : function() {
				var len = this.windowGroup.windows.length;
				for ( var i=len-1; i>=0; i-- ) {
					var win = this.windowGroup.windows[i];
					var taskButton = win.taskButton;
					if ( taskButton.more==true ) {
						return taskButton;
					}
				}
			},
			removeMoreTaskButton : function() {
				var $this = this;
				var taskButton = $this.getMoreTaskButton();
				if ( taskButton ) {
					taskButton.remove();
					taskButton.more = false;
					$this.regTaskButtonEvent(taskButton);
					$this.taskBar.append(taskButton);
					return taskButton;
				}
			},
			addTaskButtonFromMoreToEnd : function(TaskButton) {
				if ( TaskButton ) {
					TaskButton.remove();
					TaskButton.more = false;
					this.regTaskButtonEvent(TaskButton);
					TaskButton.appendTo(this.taskBar);
				}
			},
			addTaskButtonFromMore : function(TaskButton) {
				if ( TaskButton.more==true ) {
					TaskButton.insertAfter(this.taskBar.find("li:first"));
					this.addMoreTaskButton();
					TaskButton.more = false;
				}
			},
			getTotalWidth : function() {
				var _totalWidth = 0;
				$.each(this.taskButtons, function(i,e){
					if ( e.more!=true ) {
						_totalWidth += e.buttonWidth;
					}
				});
				_totalWidth += 10;
				return _totalWidth;
			},
			hideOrShowMoreButton : function() {
				var len = this.moreTaskButtons.find('.more_tab_list').children().length;
				if ( len==0 ) {
					this.moreButton.hide();
				} else {
					this.moreButton.show();
				}
			},
			isOverFlowTaskBar : function() {
				var _w = this.taskBar.width();
				var _totalWidth = this.getTotalWidth();
				return _totalWidth>_w;
			},
			removeTaskButton : function(Window) {
				var $this = this;
				var index = $.inArray(Window.taskButton, this.taskButtons);
				var e = this.taskButtons.splice(index, 1)[0];
				e.remove();
				$this.removeMoreTaskButton();
				this.hideOrShowMoreButton();
			},
			resize : function() {
				this.moreButton.hide();
				this.moreTaskButtons.hide();
				var _w = this.taskBar.width();
				var _totalWidth = this.getTotalWidth();
				if ( _totalWidth>_w ) {
					this.moreButton.show();
				}
				while ( _totalWidth<_w ) {
					var taskButton = this.getMoreTaskButton();
					if ( taskButton ) {
						_totalWidth += taskButton.buttonWidth;
						if ( _totalWidth>_w ) {
							this.moreButton.show();
							_totalWidth -= taskButton.buttonWidth;
							break;
						}
						this.addTaskButtonFromMoreToEnd(taskButton);
					} else {
						break;
					}
				}
				while ( _totalWidth>_w ) {
					var taskButton = this.addMoreTaskButton();
					_totalWidth -= taskButton.buttonWidth;
				}
			}
		});
	}
});