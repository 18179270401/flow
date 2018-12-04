$.extend({
	WindowGroup : function(deskEl, taskBarEl){
		this.desk = $(deskEl);
		this.windows = [];
		this.taskBar = new $.TaskBar(taskBarEl);
		this.taskBar.setWinGroup(this);
		$.extend(this, {
			baseZ : 100,
			activeWindow : null,
			oldActiveWindow : null,
			createWindow : function(options) {
				for ( var i=0; i<this.windows.length; i++ ) {
					var _Win = this.windows[i];
					if ( _Win.getId()==options.id ) {
						_Win.top();
						_Win.reload();
						this.taskBar.addTaskButtonFromMore(_Win.taskButton);
						return;
					}
				}
				options.zIndex = this.baseZ++;
				var _Win = $.Window(options);
				_Win.setGroup(this);
				var _TaskBtn = _Win.createTaskButton();
				this.windows.push(_Win);
				this.desk.append(_Win);
				if ( _Win.winProxy ) {
					this.desk.append(_Win.winProxy);
				};
				this.taskBar.addTaskButton(_TaskBtn);
				if(_Win.taskButton.index()==0){
					_Win.taskButton.addClass("home").find(".close").remove();
					_Win.taskButton.addClass("home").find("span").append("<i class='midicon tabhome_icon'></i>");
				};
				_Win.resize();
				this.setActiveWindow(_Win);
				return _Win;
			},
			getActiveWindow : function() {
				return this.activeWindow;
			},
			setActiveWindow : function(Window) {
				if ( Window != this.activeWindow) {
					if ( this.activeWindow!=null ) {
						this.oldActiveWindow = this.activeWindow;
						this.activeWindow.hide();
						this.activeWindow.active = false;
						this.activeWindow.taskButton.removeClass("current");
					}
					this.activeWindow = Window;
					this.activeWindow.show();
					this.activeWindow.active = true;
					this.activeWindow.taskButton.addClass("current");
				} 
			},
			closeWindow : function(Window) {
				for(i=0;i<this.windows.length;i++){
					if(Window==this.windows[i]){
						this.windows.splice(i,1);
					};
				};
				var len = this.windows.length;
				this.taskBar.removeTaskButton(Window);
				this.setActiveWindow(this.windows[len-1]);
			},
			showMaxWin : function(Window) {
				this.getMaxWin(Window).show();
			},
			getMaxWin : function(Window) {
				var len = this.windows.length;
				var _z = Window.getzIndex();
				var _i = this.getWindowIndex(Window);
				for ( var i=len-1; i>=0 ; i-- ) {
					var win = this.windows[i];
					if ( win != Window && win.isMin==false ) {
						return win;
					}
				}
			},
			hideOldActiveWindow : function() {
				if ( this.oldActiveWindow!=null ) {
					this.oldActiveWindow.hide();
				}
			},
			showOldActiveWindow : function() {
				if ( this.oldActiveWindow!=null ) {
					this.oldActiveWindow.show();
				}
			},
			minWindow : function(Window) {
				var len = this.windows.length;
				var _z = Window.getzIndex();
				var _i = this.getWindowIndex(Window);
				for ( var i=len-1; i>=0 ; i-- ) {
					var win = this.windows[i];
					if ( win != Window && win.isMin==false ) {
						var _z1 = win.getzIndex();
						Window.setzIndex(_z1);
						win.setzIndex(_z);
						this.windows[i] = Window;
						this.windows[_i] = win;
						this.setActiveWindow(win);
						return;
					}
				}
			},
			getWindowIndex : function(Window) {
				var len = this.windows.length;
				for ( var i=0; i<len ; i++ ) {
					var win = this.windows[i];
					if ( win == Window ) {
						return i;
					}
				}
			},
			getMaxZ : function() {
				var len = this.windows.length;
				return this.windows[len-1].getzIndex();
			},
			maxWindow : function(Window) {
				var len = this.windows.length;
				var _z = Window.getzIndex();
				var _max = this.getMaxZ();
				if ( _max==_z ) return;
				for ( var i=0; i<len ; i++ ) {
					var win = this.windows[i];
					var _z1 = win.getzIndex();
					if ( _z1>_z && _z1<=_max ) {
						this.windows[i-1] = win;
						win.deczIndex();
					}
				}
				Window.setzIndex(_max);
				this.windows[len-1] = Window;
				this.setActiveWindow(Window);
			},
			resize : function() {
				var len = this.windows.length;
				for ( var i=len-1; i>=0 ; i-- ) {
					var win = this.windows[i];
					win.resize();
				}
				this.taskBar.resize();
			},
			flen:function(){
				var len = this.windows.length;
				return len;
				},
			windowd:function(){
				var winwindow=this.windows;
				return winwindow;
				}	
		});	
	}
});