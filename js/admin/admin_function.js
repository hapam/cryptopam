if (typeof jQuery === "undefined") {
    throw new Error("jQuery plugins need to be before this file");
}

shop.delLoginAs = function(){
	shop.cookie.set('loginAs', ' ', 86400*30*12, '/');
	shop.reload();
}
	
shop.hover = {c_clicked:'#fff',over:function(a,bgColor){a.style.backgroundColor=bgColor},out:function(a){if(jQuery(a).hasClass('tr_clicked')){a.style.backgroundColor=shop.hover.c_clicked}else{a.style.backgroundColor=''}}};

shop.checkbox = {
	select:function(cl, ctrlChk, checkbox){
		shop.checkbox.color(checkbox); //doi mau the tr duoc check
		shop.checkbox.ctrlChkStatus(cl, ctrlChk);
	},
	selectAll: function(cl, ctrlChk, status){
		jQuery("."+cl).each(function(){
			this.checked = (status == undefined) ? !this.checked : status;
			shop.checkbox.color(this);
		});
		shop.checkbox.ctrlChkStatus(cl, ctrlChk);
	},
	ctrlChkStatus:function(cl, ctrlChk){
		var status = true;
		jQuery("."+cl).each(function(){
			if(status && !this.checked){
				status = false;
			}
		});
		jQuery('.'+ctrlChk).each(function(){
			this.checked = status;
		});
	},
	color:function(checkbox){
		var tr = jQuery(checkbox).parent().parent();
		if(checkbox.checked){
			tr.addClass('tr_clicked').css('backgroundColor', shop.hover.c_clicked);
		}else{
			tr.removeClass('tr_clicked').css('backgroundColor', '');
		}
	},
	change:function(id, checked){
		var e = shop.get_ele(id);
		if(e){
			e.checked = checked;
		}
	}
};

shop.selectBox = {
	suggest:function(objSelectbox, e){
		var key,keychar,first,selected,found;
		if (window.event){key = window.event.keyCode}
		else if (e){key = e.which}
		else{return true}
		keychar = String.fromCharCode(key);
		keychar = keychar.toLowerCase();
		if (("abcdefghijklmnopqrstvuywxyz").indexOf(keychar) > -1){
			found = false;
			selected = objSelectbox.selectedIndex;
			for(var i=selected+1;i<objSelectbox.length;i++){
				first = shop.selectBox.clean(objSelectbox.options[i].text);
				first = shop.string.stripUnicode(first[0]);
				first = first.toLowerCase();
				if(first == keychar){
					objSelectbox.selectedIndex=i;
					found = true;break;
				}
			}
			if(!found && selected > 0){
				for(var i=0;i<selected;i++){
					first = shop.selectBox.clean(objSelectbox.options[i].text);
					first = shop.string.stripUnicode(first[0]);
					first = first.toLowerCase();
					if(first == keychar){
						objSelectbox.selectedIndex=i;
						found = true;break;
					}
				}
			}
		}
		return true;
	},
	clean:function(txt){
		txt = shop.string.str_replace(['└','├','│','─ '],['','','',''],txt);
		return shop.util_trim(txt);
	}
};

shop.convertToJson = function(a){
	var arr = a.split('|'),
	temp, obj={}, need = [];
	for(var i=0;i<arr.length;i++){
	  temp = arr[i].split(': ');
	  if(temp.length > 1){
		obj = {n: temp[0], c: temp[1], t: ''};
	  }else{
		obj = {n: 'System', c: arr[i].slice(6, arr[i].length), t: ''};
	  }
	  temp = obj.c.split(' lúc ');
	  obj.c = temp[0];
	  obj.t = temp[1];
	  need[i] = obj
	}
	return need;
};

//begin my code
shop.admin = {
	system:{
		del_static_cache: function(cache_file){
			shop.ajax_popup('act=admin&code=del_static_cache','GET',{cache_file:cache_file},
			function(msg){
				var m = '';
				switch(msg){
					case 'not_login': 	m = 'Bạn phải đăng nhập mới được thực hiện chức năng này.'; break;
					case 'no_perm': 	m = 'Bạn không có quyền thực hiện chức năng này'; break;
					case 'success': 	m = 'Đã xoá cache thành công!'; break;
					default: m = 'Không thành công!';
				}
				shop.show_popup_message(m,'Thông báo lỗi',(msg=='success'?0:-1));
			});
		},
		ckEditor: function (ele,width,height,theme,toolbar, css) {
			css = css ? css : (BASE_URL + '/style/style_editor.css?v=1');
			CKEDITOR.replace(ele ,
			{
				toolbar : toolbar,
				width: width,
				height: height,
				skin : theme,
				language : 'vi',
				contentsCss: css
			});
		},
		postMethod:function(formID){
			var frm = shop.get_ele(formID);
			if(frm){
				frm.method = "POST";
				frm.submit();
			}
		},
		fetchDataToUrl: function(data){
			var url = '';
			if(data){
				for(var i in data){
					url += '&'+i+'='+data[i];
				}
			}
			return url;
		},
		delSubmit:function(frm, title){
			title = title ? title : 'item';
			var numCur = jQuery('.checkall:checked').length, msg = 'Bạn có chắc chắn muốn xoá '+ numCur + ' '+title+' không ?';
			if(numCur > 0) {
				shop.confirm(msg, function(){
					frm.method = "POST";
					frm.submit();
				});
			}
			else {
				alert('Bạn phải chọn ít nhất 1 '+title+' để xóa');
			}
		},
		submit:function(formID){
			var frm = shop.get_ele(formID);
			if(frm){
				frm.submit();
			}
		}
	},
	showLog:function(id, type, recperpage, page, total){
		recperpage = (recperpage && recperpage > 0) ? recperpage : 10;
		page = (page && page > 1) ? page : 1;
		total = (total && total > 1) ? total : 1;
		shop.ajax_popup("act=admin&code=log",'POST',{id: id, type: type, total_page: total, page: page, recperpage: recperpage},
		function(json){
			if(json.err == 0){
				var theme = shop.admin.theme.parseLog(json.data, json.users, json.show_note, id, type, recperpage, page, json.total);
				if(theme != ''){
					shop.show_overlay_popup('system-log', 'Hệ thống Log',
					theme,
					{
						content: {
						  'padding' : '0px',
						  'width' : '700px'
						}
					});
				}else{
					alert('Hệ thống không tìm thấy log');
				}
			}else{
				alert(json.msg);
			}
		});
	},
	showImage:function(obj, img){
		jQuery(obj).parent().html('<img src="'+img+'" width="70" height="70" border="0" onclick="shop.admin.hideImage(this,\''+img+'\')" alt="map" title="Click to hide" />');
	},
	hideImage:function(obj, img){
		jQuery(obj).parent().html('<a href="javascript:void(0)" onclick="shop.admin.showImage(this,\''+img+'\')" style="width:70px;height:70px;line-height:70px">show image</a>');
	},
	collapse: function(obj){
		var p = jQuery(obj).parent().parent().parent(),
		bg = shop.admin.permission.background;
		if(p.hasClass('fieldset_click')){
			p.removeClass('fieldset_click');
			jQuery('.xoay:first', p).slideDown();
		}else{
			p.addClass('fieldset_click');
			jQuery('.xoay:first', p).slideUp();
			shop.admin.permission.background = jQuery(p).css('backgroundColor');
			bg = 'transparent';
		}
		jQuery(p).css('backgroundColor', bg);
	},
	theme:{
		parseLog:function(data, users, show_note, id, type, recperpage, page, total){
			var str = "", c, pager = '';
			if(data && data.length > 0){
				str = shop.join
					('<table class="table table-bordered table-striped table-hover">')
						('<thead>')
							('<tr class="info">')
								('<th width="200">Account</th>')
								('<th>Thao tác</th>')
								('<th width="100">Thời gian</th>')
							('</tr>')
						('</thead><tbody>')();
				for(var i in data){
					c = data[i].c;
					if(data[i].f != 0 && data[i].cu){
						if(type == 'order'){
							c += ' hóa đơn con <font color="red"><b>'+data[i].f+'</b></font>';
						}else{
							c += ' <font color="red">'+users[data[i].f].username+'</font>';
						}
					}
					if(show_note && (data[i].no != '' && data[i].no != 'detail')){
						c += ' ('+data[i].no+')';
					}
					str += shop.join
						("<tr>")
							('<td><span style="color:red">'+ (data[i].n=='system'?'Hệ thống':data[i].n)+'</span></td>')
							('<td>'+ c)
							('<br /><font color="orange">('+data[i].ip+')</font></td>')
							('<td align="center">'+ data[i].t[1] + '<br /><span style="color:green">' + data[i].t[0] + '</span></td>')
						("</tr>")();
				}
				total = Math.ceil(total/recperpage);
				//pager js
				if(total > 1){
					page = parseInt(page); total = parseInt(total);
					var next = '', last = '', prev = '', first= '', left_dot  = '', right_dot = '', show_page = 3,
					from_page = page - show_page,
					to_page = page + show_page;
					//get prev & first link
					if(page > 1){
						prev = '<li class="prev"><a href="javascript:void(0)" onclick="shop.admin.showLog('+id+', \''+type+'\', '+recperpage+', '+(page-1)+', '+total+')">&nbsp;<&nbsp;</a></li>';
						//first= '<a href="javascript:void(0)" onclick="shop.admin.showLog('+id+', \''+type+'\', '+recperpage+', 1, '+total+')"><span>&laquo; Đầu</span></a>';
					}
					//get next & last link
					if(page < total){
						next = '<li class="next"><a href="javascript:void(0)" onclick="shop.admin.showLog('+id+', \''+type+'\', '+recperpage+', '+(page+1)+', '+total+')">&nbsp;>&nbsp;</a></li>';
						//last = '<a href="javascript:void(0)" onclick="shop.admin.showLog('+id+', \''+type+'\', '+recperpage+', '+total+', '+total+')" class="page-item last" title="xem trang cuối"><span>Cuối &raquo;</span></a>';
					}
					//get dots & from_page & to_page
					if(from_page > 0)	{
						if(from_page > 1){
							left_dot = '<li><a href="javascript:void(0)" onclick="shop.admin.showLog('+id+', \''+type+'\', '+recperpage+', 1, '+total+')">1</a></li><li>...</li>';
						}
					}else{
						from_page = 1;
					}
					if(to_page < total)	{
						right_dot = '<li>...</li><li><a href="javascript:void(0)" onclick="shop.admin.showLog('+id+', \''+type+'\', '+recperpage+', '+total+', '+total+')">'+total+'</a></li>';
					}else{
						to_page = total;
					}
					for(var j=from_page;j<=to_page;j++){
						pager += '<li><a href="javascript:void(0)" onclick="shop.admin.showLog('+id+', \''+type+'\', '+recperpage+', '+j+', '+total+')" class="'+(j==page?'active':'')+'">'+j+'</a></li>';
					}
					pager = ' <div class="tPages"><ul class="pages">'+first+prev+left_dot+pager+right_dot+next+last+'</ul></div>';
				}
				str = '<div>'+str+'</tbody>';
				if(pager != ''){
					str += '<tfoot><tr><td colspan="3">'+pager+'</td></tr></tfoot>';
				}
				return shop.join
				(str+'</tbody></table></div>')
				('<div class="popup-footer" align="center" id="m0">')
					('<button type="button" onclick="shop.hide_overlay_popup(\'system-log\');">Đóng</button>')
				('</div>')();
			}
			return "";
		}
	}
};

shop.admin.permission = {
	background:'',
	mousedown: false,
	mouseDIV:{
		over: function(obj){jQuery(obj).addClass('over-bg')},
		out:  function(obj){jQuery(obj).removeClass('over-bg')},
		down: function(obj){
			if(!shop.admin.permission.mousedown){
				var check = jQuery('.checkbox', obj).attr("checked");
				jQuery('.checkbox', obj).attr("checked", !check)
			}
		},
		up: function(){shop.admin.permission.mousedown = false}
	},
	mouseCheckboxDown: function(){shop.admin.permission.mousedown = true},
	collapse: function(obj){
		var p = jQuery(obj).parent().parent().parent(),
		bg = shop.admin.permission.background;
		if(p.hasClass('fieldset_click')){
			p.removeClass('fieldset_click');
			jQuery('.xoay:first', p).slideDown();
		}else{
			p.addClass('fieldset_click');
			jQuery('.xoay:first', p).slideUp();
			shop.admin.permission.background = jQuery(p).css('backgroundColor');
			bg = 'transparent';
		}
		jQuery(p).css('backgroundColor', bg);
	}
};

shop.admin.updateAdminConfig = function(type, value){
	shop.ajax_popup('act=panel&code=admin-config','POST',{type:type, value:value},
	function(msg){
		if(msg.err == 0){
			//shop.reload();
		}else{
			alert(msg.msg);
		}
	});
};

shop.openTab = function(activeTab){
	var url = document.URL;
	if(url.indexOf('tab=') > -1){
		url = url.replace(/tab=[a-z0-9]+/gi, "tab="+activeTab);
	}else{
		url += '?tab='+activeTab;
	}
	window.history.pushState({state:activeTab}, document.title, url);
}

shop.textarea = function(){
	$('textarea').each(function(){
		var data = $(this).data(), width = 700, height = 400;
		if(data.editor == 'ckeditor'){
			if(data.width){
				width = data.width;
			}
			if(data.height){
				height = data.height;
			}
			shop.admin.system.ckEditor(this.id, width, height, 'moono',[
				['Bold','Italic','Underline','Strike'],
				['Undo','Redo','-'],
				['Link','Unlink','Anchor'],['Image','Youtube','Table'],
				['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
				['Subscript','Superscript','SpecialChar'],
				'/',
				['Font','FontSize'],
				['TextColor','BGColor','SelectAll','RemoveFormat'],['PasteFromWord','PasteText'],['Templates','-','Source']
			], CSS_EDITOR_LINK);
			CKEDITOR.instances[this.id].config.filebrowserBrowseUrl = BASE_URL+'browse.php';
			shop.multiupload(this.id);
		}
	});
};

shop.inputfile = function(){
	$( 'input:file' ).each( function(){
		var $input	 = $( this ),
			$label	 = $input.next( 'label' ),
			labelVal = $label.html(),
			change = $input.attr('change');

		$input.on( 'change', function( e )
		{
			var fileName = '';

			if( this.files && this.files.length > 1 )
				fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '[count]', this.files.length );
			else if( e.target.value )
				fileName = e.target.value.split( '\\' ).pop();

			if( fileName )
				$label.find( 'span' ).html( fileName );
			else
				$label.html( labelVal );
			if(change){
				eval(change);
			}
		});

		// Firefox bug fix
		$input
		.on( 'focus', function(){ $input.addClass( 'has-focus' ); })
		.on( 'blur', function(){ $input.removeClass( 'has-focus' ); });
	});
};

shop.skinChanger = function() {
    $('.right-sidebar .demo-choose-skin li').on('click', function () {
        var $body = $('body');
        var $this = $(this);

        var existTheme = $('.right-sidebar .demo-choose-skin li.active').data('theme');
        $('.right-sidebar .demo-choose-skin li').removeClass('active');
        $body.removeClass('theme-' + existTheme);
        $this.addClass('active');

        $body.addClass('theme-' + $this.data('theme'));
        shop.cookie.set('theme-admin', $this.data('theme'), 86400 * 365);
    });
};

shop.configDatePick = function(){
	return {
		time: false,
		currentDate: null,
		lang: 'vi',
		weekStart: 0,
		nowButton: true,
		cancelText: 'Tắt',
		okText: 'Chọn',
		nowText: 'Hôm nay',
		switchOnClick: false,
		format: "DD-MM-YYYY"
	}
};

$.AdminBSB = {};
$.AdminBSB.options = {
    colors: {
        red: '#F44336',
        pink: '#E91E63',
        purple: '#9C27B0',
        deepPurple: '#673AB7',
        indigo: '#3F51B5',
        blue: '#2196F3',
        lightBlue: '#03A9F4',
        cyan: '#00BCD4',
        teal: '#009688',
        green: '#4CAF50',
        lightGreen: '#8BC34A',
        lime: '#CDDC39',
        yellow: '#ffe821',
        amber: '#FFC107',
        orange: '#FF9800',
        deepOrange: '#FF5722',
        brown: '#795548',
        grey: '#9E9E9E',
        blueGrey: '#607D8B',
        black: '#000000',
        white: '#ffffff'
    },
    leftSideBar: {
        scrollColor: 'rgba(0,0,0,0.5)',
        scrollWidth: '4px',
        scrollAlwaysVisible: false,
        scrollBorderRadius: '0',
        scrollRailBorderRadius: '0'
    },
    dropdownMenu: {
        effectIn: 'fadeIn',
        effectOut: 'fadeOut'
    }
}

/* Left Sidebar - Function =================================================================================================
*  You can manage the left sidebar menu options
*  
*/
$.AdminBSB.leftSideBar = {
    activate: function () {
        var _this = this;
        var $body = $('body');
        var $overlay = $('.overlay');

        //Close sidebar
        $(window).click(function (e) {
            var $target = $(e.target);
            if (e.target.nodeName.toLowerCase() === 'i') { $target = $(e.target).parent(); }

            if (!$target.hasClass('bars') && _this.isOpen() && $target.parents('#leftsidebar').length === 0) {
                if (!$target.hasClass('js-right-sidebar')) $overlay.fadeOut();
                $body.removeClass('overlay-open');
            }
        });

        $.each($('.menu-toggle.toggled'), function (i, val) {
            $(val).next().slideToggle(0);
        });

        //When page load
        $.each($('.menu .list li.active'), function (i, val) {
            var $activeAnchors = $(val).find('a:eq(0)');

            $activeAnchors.addClass('toggled');
            $activeAnchors.next().show();
        });

        //Collapse or Expand Menu
        $('.menu-toggle').on('click', function (e) {
            var $this = $(this);
            var $content = $this.next();

            if ($($this.parents('ul')[0]).hasClass('list')) {
                var $not = $(e.target).hasClass('menu-toggle') ? e.target : $(e.target).parents('.menu-toggle');

                $.each($('.menu-toggle.toggled').not($not).next(), function (i, val) {
                    if ($(val).is(':visible')) {
                        $(val).prev().toggleClass('toggled');
                        $(val).slideUp();
                    }
                });
            }

            $this.toggleClass('toggled');
            $content.slideToggle(320);
        });

        //Set menu height
        _this.setMenuHeight();
        _this.checkStatuForResize(true);
        $(window).resize(function () {
            _this.setMenuHeight();
            _this.checkStatuForResize(false);
        });

        //Set Waves
        //Waves.attach('.menu .list a', ['waves-block']);
        //Waves.init();
    },
    setMenuHeight: function () {
        if (typeof $.fn.slimScroll != 'undefined') {
            var configs = $.AdminBSB.options.leftSideBar;
            var height = ($(window).height() - ($('.legal').outerHeight() + $('.user-info').outerHeight() + $('.navbar').innerHeight()));
            var $el = $('.list');

            $el.slimScroll({ destroy: true }).height("auto");
            $el.parent().find('.slimScrollBar, .slimScrollRail').remove();

            $el.slimscroll({
                height: height + "px",
                color: configs.scrollColor,
                size: configs.scrollWidth,
                alwaysVisible: configs.scrollAlwaysVisible,
                borderRadius: configs.scrollBorderRadius,
                railBorderRadius: configs.scrollRailBorderRadius
            });
        }
    },
    checkStatuForResize: function (firstTime) {
        var $body = $('body');
        var $openCloseBar = $('.navbar .navbar-header .bars');
        var width = $body.width();

        if (firstTime) {
            $body.find('.content, .sidebar').addClass('no-animate').delay(1000).queue(function () {
                $(this).removeClass('no-animate').dequeue();
            });
        }

        if (width < 1170) {
            $body.addClass('ls-closed');
            $openCloseBar.fadeIn();
        }
        else {
            $body.removeClass('ls-closed');
            $openCloseBar.fadeOut();
        }
    },
    isOpen: function () {
        return $('body').hasClass('overlay-open');
    }
};
//==========================================================================================================================

/* Right Sidebar - Function ================================================================================================
*  You can manage the right sidebar menu options
*  
*/
$.AdminBSB.rightSideBar = {
    activate: function () {
        var _this = this;
        var $sidebar = $('#rightsidebar');
        var $overlay = $('.overlay');

        //Close sidebar
        $(window).click(function (e) {
            var $target = $(e.target);
            if (e.target.nodeName.toLowerCase() === 'i') { $target = $(e.target).parent(); }

            if (!$target.hasClass('js-right-sidebar') && _this.isOpen() && $target.parents('#rightsidebar').length === 0) {
                if (!$target.hasClass('bars')) $overlay.fadeOut();
                $sidebar.removeClass('open');
            }
        });

        $('.js-right-sidebar').on('click', function () {
            $sidebar.toggleClass('open');
            if (_this.isOpen()) { $overlay.fadeIn(); } else { $overlay.fadeOut(); }
        });
    },
    isOpen: function () {
        return $('.right-sidebar').hasClass('open');
    }
}
//==========================================================================================================================

/* Searchbar - Function ================================================================================================
*  You can manage the search bar
*  
*/
var $searchBar = $('.search-bar');
$.AdminBSB.search = {
    activate: function () {
        var _this = this;

        //Search button click event
        $('.js-search').on('click', function () {
            _this.showSearchBar();
        });

        //Close search click event
        $searchBar.find('.close-search').on('click', function () {
            _this.hideSearchBar();
        });

        //ESC key on pressed
        $searchBar.find('input[type="text"]').on('keyup', function (e) {
            if (e.keyCode == 27) {
                _this.hideSearchBar();
            }
        });
    },
    showSearchBar: function () {
        $searchBar.addClass('open');
        $searchBar.find('input[type="text"]').focus();
    },
    hideSearchBar: function () {
        $searchBar.removeClass('open');
        $searchBar.find('input[type="text"]').val('');
    }
}
//==========================================================================================================================

/* Navbar - Function =======================================================================================================
*  You can manage the navbar
*  
*/
$.AdminBSB.navbar = {
    activate: function () {
        var $body = $('body');
        var $overlay = $('.overlay');

        //Open left sidebar panel
        $('.bars').on('click', function () {
            $body.toggleClass('overlay-open');
            if ($body.hasClass('overlay-open')) { $overlay.fadeIn(); } else { $overlay.fadeOut(); }
        });

        //Close collapse bar on click event
        $('.nav [data-close="true"]').on('click', function () {
            var isVisible = $('.navbar-toggle').is(':visible');
            var $navbarCollapse = $('.navbar-collapse');

            if (isVisible) {
                $navbarCollapse.slideUp(function () {
                    $navbarCollapse.removeClass('in').removeAttr('style');
                });
            }
        });
    }
}
//==========================================================================================================================

/* Input - Function ========================================================================================================
*  You can manage the inputs(also textareas) with name of class 'form-control'
*  
*/
$.AdminBSB.input = {
    activate: function () {
        //On focus event
        $('.form-control').focus(function () {
            $(this).parent().addClass('focused');
        });

        //On focusout event
        $('.form-control').focusout(function () {
            var $this = $(this);
            if ($this.parents('.form-group').hasClass('form-float')) {
                if ($this.val() == '') { $this.parents('.form-line').removeClass('focused'); }
            }
            else {
                $this.parents('.form-line').removeClass('focused');
            }
        });

        //On label click
        $('body').on('click', '.form-float .form-line .form-label', function () {
            $(this).parent().find('input').focus();
        });
    }
}
//==========================================================================================================================

/* Form - Select - Function ================================================================================================
*  You can manage the 'select' of form elements
*  
*/
$.AdminBSB.select = {
    activate: function () {
        if ($.fn.selectpicker) { $('select:not(.ms)').selectpicker(); }
    }
}
//==========================================================================================================================

/* DropdownMenu - Function =================================================================================================
*  You can manage the dropdown menu
*  
*/

$.AdminBSB.dropdownMenu = {
    activate: function () {
        var _this = this;

        $('.dropdown, .dropup, .btn-group').on({
            "show.bs.dropdown": function () {
                var dropdown = _this.dropdownEffect(this);
                _this.dropdownEffectStart(dropdown, dropdown.effectIn);
            },
            "shown.bs.dropdown": function () {
                var dropdown = _this.dropdownEffect(this);
                if (dropdown.effectIn && dropdown.effectOut) {
                    _this.dropdownEffectEnd(dropdown, function () { });
                }
            },
            "hide.bs.dropdown": function (e) {
                var dropdown = _this.dropdownEffect(this);
                if (dropdown.effectOut) {
                    e.preventDefault();
                    _this.dropdownEffectStart(dropdown, dropdown.effectOut);
                    _this.dropdownEffectEnd(dropdown, function () {
                        dropdown.dropdown.removeClass('open');
                    });
                }
            }
        });

        //Set Waves
        //Waves.attach('.dropdown-menu li a', ['waves-block']);
        //Waves.init();
    },
    dropdownEffect: function (target) {
        var effectIn = $.AdminBSB.options.dropdownMenu.effectIn, effectOut = $.AdminBSB.options.dropdownMenu.effectOut;
        var dropdown = $(target), dropdownMenu = $('.dropdown-menu', target);

        if (dropdown.length > 0) {
            var udEffectIn = dropdown.data('effect-in');
            var udEffectOut = dropdown.data('effect-out');
            if (udEffectIn !== undefined) { effectIn = udEffectIn; }
            if (udEffectOut !== undefined) { effectOut = udEffectOut; }
        }

        return {
            target: target,
            dropdown: dropdown,
            dropdownMenu: dropdownMenu,
            effectIn: effectIn,
            effectOut: effectOut
        };
    },
    dropdownEffectStart: function (data, effectToStart) {
        if (effectToStart) {
            data.dropdown.addClass('dropdown-animating');
            data.dropdownMenu.addClass('animated dropdown-animated');
            data.dropdownMenu.addClass(effectToStart);
        }
    },
    dropdownEffectEnd: function (data, callback) {
        var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
        data.dropdown.one(animationEnd, function () {
            data.dropdown.removeClass('dropdown-animating');
            data.dropdownMenu.removeClass('animated dropdown-animated');
            data.dropdownMenu.removeClass(data.effectIn);
            data.dropdownMenu.removeClass(data.effectOut);

            if (typeof callback == 'function') {
                callback();
            }
        });
    }
}
//==========================================================================================================================

/* Browser - Function ======================================================================================================
*  You can manage browser
*  
*/
var edge = 'Microsoft Edge';
var ie10 = 'Internet Explorer 10';
var ie11 = 'Internet Explorer 11';
var opera = 'Opera';
var firefox = 'Mozilla Firefox';
var chrome = 'Google Chrome';
var safari = 'Safari';

$.AdminBSB.browser = {
    activate: function () {
        var _this = this;
        var className = _this.getClassName();

        if (className !== '') $('html').addClass(_this.getClassName());
    },
    getBrowser: function () {
        var userAgent = navigator.userAgent.toLowerCase();

        if (/edge/i.test(userAgent)) {
            return edge;
        } else if (/rv:11/i.test(userAgent)) {
            return ie11;
        } else if (/msie 10/i.test(userAgent)) {
            return ie10;
        } else if (/opr/i.test(userAgent)) {
            return opera;
        } else if (/chrome/i.test(userAgent)) {
            return chrome;
        } else if (/firefox/i.test(userAgent)) {
            return firefox;
        } else if (!!navigator.userAgent.match(/Version\/[\d\.]+.*Safari/)) {
            return safari;
        }

        return undefined;
    },
    getClassName: function () {
        var browser = this.getBrowser();

        if (browser === edge) {
            return 'edge';
        } else if (browser === ie11) {
            return 'ie11';
        } else if (browser === ie10) {
            return 'ie10';
        } else if (browser === opera) {
            return 'opera';
        } else if (browser === chrome) {
            return 'chrome';
        } else if (browser === firefox) {
            return 'firefox';
        } else if (browser === safari) {
            return 'safari';
        } else {
            return '';
        }
    }
}