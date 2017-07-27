shop.admin.manage_site = {
	submit: function(frm){
		if(shop.util_trim(frm.email.value) == ''){
			shop.raiseError('#email','Chưa nhập email',true);
			return;
		}else if(!shop.is_email(frm.email.value)){
			shop.raiseError('#email','Email không hợp lệ',true);
			return;
		}
		frm.submit();
	},
	increJsVersion:function(key){
		var v = Math.round(jQuery('#'+key).val()) + 1;
		jQuery('#'+key).val(v+'.69');
	},
	delImages:function(){
		shop.confirm(shop.join
			('<div style="color:red"><b>XÓA TOÀN BỘ ẢNH!!!</b></div>')
			('<div class="m-t-10"><font color="blue">Ảnh gốc sẽ được giữ lại</font></div>')
			('<div class="m-t-10">Bạn có muốn tiếp tục?</div>')(),
		function(){
			shop.ajax_popup('act=config&code=remove-img','POST',{},
			function(j){
				if(j.err == 0){
					shop.show_popup_message('Toàn bộ ảnh đã được xóa', 'Thành Công', 1);
				}else{
					alert(j.msg);
				}
			});
		});
	},
	newImgConfig:function(name, defi, mask){
		name = name ? name : '';
		defi = defi ? defi : '';
		var pop_id = 'size-add-new',
		html = shop.join
		('<div id="popup-form">')
            ('<table id="pass-changed" border="0" cellpadding="8" cellspacing="0" align="center" width="100%">')
                ('<tr>')
                    ('<td width="100">Tên thư mục</td>')
                    ('<td><input type="text" id="name_dir" class="needRelease" value="'+name+'" /></td>')
                ('</tr>')
				('<tr>')
                    ('<td width="100">Định nghĩa</td>')
                    ('<td><input type="text" id="name_defined" class="needRelease" value="'+defi+'" /></td>')
                ('</tr>')
				('<tr>')
                    ('<td width="100">Water mask</td>')
                    ('<td><select id="mask_active" class="needRelease">')
						('<option value="0"'+(mask==0?' selected':'')+'>Tắt</option>')
						('<option value="1"'+(mask==1?' selected':'')+'>Bật</option>')
					('</select></td>')
                ('</tr>')
            ('</table>')
        ('</div>')
        ('<div class="popup-footer" align="right">')
            ('<button type="button" onclick="shop.admin.manage_site.newImgConfigSubmit(\''+pop_id+'\', \''+name+'\')">'+(name != '' ? 'Cập nhật' : 'Thêm mới')+'</button>')
            ('<button type="button" onclick="shop.hide_overlay_popup(\''+pop_id+'\');">Hủy bỏ</button>')
        ('</div>')();
		shop.show_overlay_popup(pop_id, 'Thêm cấu hình ảnh', html,
        {
            content: {
                'width' : '400px'
            },
			release:function(){
				if(shop.is_exists(jQuery.uniform)){
					jQuery('.needRelease').uniform();
				}
			}
        });
	},
	newImgConfigSubmit:function(pop_id, oldKey){
		var name = jQuery('#name_dir').val(),
		defi = jQuery('#name_defined').val(),
		mask = jQuery('#mask_active').val();
		if(name == '' || defi == ''){
			alert("Bạn chưa điền đủ thông tin");
		}else{
			shop.ajax_popup('act=config&code=add-dir','POST',{name: name, defi:defi, oldKey: oldKey, mask : mask},
			function(j){
				if(j.err == 0){
					shop.reload();
				}else{
					alert(j.msg);
				}
			});
		}
	},
	newImgSize:function(key){
		var pop_id = 'size-add-size',
		html = shop.join
		('<div id="popup-form">')
            ('<table id="pass-changed" border="0" cellpadding="8" cellspacing="0" align="center" width="100%">')
                ('<tr>')
                    ('<td width="100">Chiều rộng</td>')
                    ('<td><input type="text" id="img_width" class="needRelease" value="" /></td>')
                ('</tr>')
				('<tr>')
                    ('<td width="100">Chiều cao</td>')
                    ('<td><input type="text" id="img_height" class="needRelease" value="" /></td>')
                ('</tr>')
            ('</table>')
        ('</div>')
        ('<div class="popup-footer" align="right">')
            ('<button type="button" onclick="shop.admin.manage_site.newImgSizeSubmit(\''+pop_id+'\', \''+key+'\')">Thêm mới</button>')
            ('<button type="button" onclick="shop.hide_overlay_popup(\''+pop_id+'\');">Hủy bỏ</button>')
        ('</div>')();
		shop.show_overlay_popup(pop_id, 'Thêm kích thước ảnh', html,
        {
            content: {
                'width' : '400px'
            },
			release:function(){
				if(shop.is_exists(jQuery.uniform)){
					jQuery('.needRelease').uniform();
				}
			}
        });
	},
	newImgSizeSubmit:function(pop_id, key){
		var w = jQuery('#img_width').val(),
		h = jQuery('#img_height').val();
		if(w == '' || h == ''){
			alert("Bạn chưa điền đủ thông tin");
		}else{
			shop.ajax_popup('act=config&code=add-size','POST',{w : w, h : h, key : key},
			function(j){
				if(j.err == 0){
					shop.reload();
				}else{
					alert(j.msg);
				}
			});
		}
	},
	delImgConfig:function(key){
		shop.confirm('Bạn chắc chắn muốn xóa?', function(){
			shop.ajax_popup('act=config&code=del-dir','POST',{key : key},
			function(j){
				if(j.err == 0){
					shop.reload();
				}else{
					alert(j.msg);
				}
			});
		});
	},
	delImgSize:function(key, w){
		shop.confirm('Bạn chắc chắn muốn xóa?', function(){
			shop.ajax_popup('act=config&code=del-size','POST',{key : key, w : w},
			function(j){
				if(j.err == 0){
					shop.reload();
				}else{
					alert(j.msg);
				}
			});
		});
	},
	build:function(){
		shop.ajax_popup('act=config&code=build','GET',{},
		function(j){
			alert(j.msg);
		});
	},
	usingCGlobal:function(key){
		shop.ajax_popup('act=config&code=global-use','GET',{key:key},
		function(j){
			if(j.err == 0){
				var html = '', counter = 1;
				html = '<div style="height:580px;width:780px; overflow-x: hidden; overflow-y:scroll;padding:10px">';
				for(var i in j.data){
					html += '<div style="font-size:16px;padding:10px 0"><b>'+counter+'. '+i+'</b></div>';
					html += shop.join
						('<table style="border-collapse: collapse; width: 100%; font-size: 12px; color: rgb(34, 34, 34);">')
							('<thead>')
								('<tr>')
									('<th style="padding: 5px; background-color: #6DBD2A; color: #fff; text-align: left; border: 1px solid rgb(0, 0, 0); vertical-align: top; font-family: Consolas, &quot;Lucida Console&quot;, Courier, mono; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAAeCAYAAADtlXTHAAAASUlEQVQYVxXE4QaDABgAwC+KoigSxaKxMVEURdH7P9bc/biIiCQiIlWmXIVKVarVqFWnXoNeGjXprY+++mnWolWbdh06denW8we0DwOySWODmQAAAABJRU5ErkJggg==&quot;); background-repeat: repeat-x; text-transform: uppercase;">File Name</th>')
									('<th style="padding: 5px; background-color: #6DBD2A; color: #fff; text-align: left; border: 1px solid rgb(0, 0, 0); vertical-align: top; font-family: Consolas, &quot;Lucida Console&quot;, Courier, mono; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAAeCAYAAADtlXTHAAAASUlEQVQYVxXE4QaDABgAwC+KoigSxaKxMVEURdH7P9bc/biIiCQiIlWmXIVKVarVqFWnXoNeGjXprY+++mnWolWbdh06denW8we0DwOySWODmQAAAABJRU5ErkJggg==&quot;); background-repeat: repeat-x; text-transform: uppercase;">Line</th>')
								('</tr>')
							('</thead>')
							('<tbody>')();
					for(var e in j.data[i]){
						html += shop.join('<tr>')
							('<td colspan="1" style="padding: 5px; background-color: rgb(255, 255, 255); border: 1px solid rgb(0, 0, 0); vertical-align: top; font-family: Consolas, &quot;Lucida Console&quot;, Courier, mono; white-space: nowrap;">'+j.data[i][e].direct+'/'+e+'</td>')
							('<td colspan="1" style="padding: 5px; background-color: rgb(255, 255, 255); border: 1px solid rgb(0, 0, 0); vertical-align: top; font-family: Consolas, &quot;Lucida Console&quot;, Courier, mono; white-space: nowrap;">'+j.data[i][e].line+'</td>')
						('</tr>')();
					}
					html += '</tbody></table><div>&nbsp;</div>';
					counter++;
				}
				html += '</div>';
				shop.show_overlay_popup('pop-config', 'Biến toàn cục: <em style="font-size:14px">'+key+'</em>', html, {
					content: {
						'padding': '10px',
						'width': '800px',
						'height': '600px'
					}
				});
			}else{
				alert(j.msg);
			}
		});
	},
	getCGlobal:function(){
		shop.ajax_popup('act=config&code=global','GET',{},
		function(j){
			if(j.err == 0){
				var html = '', counter = 1, val = '';
				for(var i in j.data){
					html += '<div class="m-t-15 font-16"><b>'+counter+'. '+i+'</b></div>';
					for(var e in j.data[i]){
						html += shop.join('<div class="m-t-10 m-l-10">')
							('<table style="border-collapse: collapse; width: 100%; font-size: 12px; color: rgb(34, 34, 34);">')
								('<thead>')
									('<tr>')
										('<th colspan="3" style="padding: 5px; background-color: #D82525; color: #fff; text-align: left; border: 1px solid rgb(0, 0, 0); vertical-align: top; font-family: Consolas, &quot;Lucida Console&quot;, Courier, mono; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAAeCAYAAADtlXTHAAAASUlEQVQYVxXE4QaDABgAwC+KoigSxaKxMVEURdH7P9bc/biIiCQiIlWmXIVKVarVqFWnXoNeGjXprY+++mnWolWbdh06denW8we0DwOySWODmQAAAABJRU5ErkJggg==&quot;); background-repeat: repeat-x;">'+j.data[i][e].direct+'/'+e+'</th>')
									('</tr>')
									('<tr>')
										('<th style="padding: 5px; background-color: rgb(238, 238, 238); color: #000; text-align: left; border: 1px solid rgb(0, 0, 0); vertical-align: top; font-family: Consolas, &quot;Lucida Console&quot;, Courier, mono; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAAeCAYAAADtlXTHAAAASUlEQVQYVxXE4QaDABgAwC+KoigSxaKxMVEURdH7P9bc/biIiCQiIlWmXIVKVarVqFWnXoNeGjXprY+++mnWolWbdh06denW8we0DwOySWODmQAAAABJRU5ErkJggg==&quot;); background-repeat: repeat-x; text-transform: uppercase;">key</th>')
										('<th style="padding: 5px; background-color: rgb(238, 238, 238); color: #000; text-align: left; border: 1px solid rgb(0, 0, 0); vertical-align: top; font-family: Consolas, &quot;Lucida Console&quot;, Courier, mono; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAAeCAYAAADtlXTHAAAASUlEQVQYVxXE4QaDABgAwC+KoigSxaKxMVEURdH7P9bc/biIiCQiIlWmXIVKVarVqFWnXoNeGjXprY+++mnWolWbdh06denW8we0DwOySWODmQAAAABJRU5ErkJggg==&quot;); background-repeat: repeat-x; text-transform: uppercase;">value</th>')
										('<th style="padding: 5px; background-color: rgb(238, 238, 238); color: #000; text-align: left; border: 1px solid rgb(0, 0, 0); vertical-align: top; font-family: Consolas, &quot;Lucida Console&quot;, Courier, mono; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAAeCAYAAADtlXTHAAAASUlEQVQYVxXE4QaDABgAwC+KoigSxaKxMVEURdH7P9bc/biIiCQiIlWmXIVKVarVqFWnXoNeGjXprY+++mnWolWbdh06denW8we0DwOySWODmQAAAABJRU5ErkJggg==&quot;); background-repeat: repeat-x; text-transform: uppercase;">note</th>')
									('</tr>')
								('</thead>')
								('<tbody>')();
						for(var f in j.data[i][e].found){
							if(shop.is_obj(j.data[i][e].found[f][1])){
								val = shop.admin.manage_site.getCGlobalObject(j.data[i][e].found[f][1]);
							}else{
								val = j.data[i][e].found[f][1];
							}
							html += shop.join('<tr>')
										('<td colspan="1" style="padding: 5px; background-color: rgb(255, 255, 255); border: 1px solid rgb(0, 0, 0); vertical-align: top; font-family: Consolas, &quot;Lucida Console&quot;, Courier, mono; white-space: nowrap;"><a href="javascript:shop.admin.manage_site.usingCGlobal(\''+j.data[i][e].found[f][0]+'\')" title="Xem những nơi có sử dụng">'+j.data[i][e].found[f][0]+'</a></td>')
										('<td colspan="1" style="padding: 5px; background-color: rgb(255, 255, 255); border: 1px solid rgb(0, 0, 0); vertical-align: top; font-family: Consolas, &quot;Lucida Console&quot;, Courier, mono; white-space: nowrap;">'+val+'</td>')
										('<td colspan="1" style="padding: 5px; background-color: rgb(255, 255, 255); border: 1px solid rgb(0, 0, 0); vertical-align: top; font-family: Consolas, &quot;Lucida Console&quot;, Courier, mono; white-space: nowrap;">'+(shop.is_exists(j.data[i][e].found[f][2]) ? j.data[i][e].found[f][2] : '...')+'</td>')
									('</tr>')();
						}
						html += '</tbody></table></div>';
					}
					counter++;
				}
				jQuery('.cglobal').html(html);
			}else{
				alert(j.msg);
			}
		});
	},
	getCGlobalObject:function(obj){
		var strHTML = '', tmp = '';
		if(Object.keys(obj).length === 0){
			strHTML = 'Empty';
		}else{
			strHTML = shop.join
			('<table style="border-collapse: collapse; width: 100%; font-size: 12px; color: rgb(34, 34, 34);">')
				('<thead>')
					('<tr>')
						('<th colspan="2" style="padding: 5px; background-color: #1F96CF; color: rgb(238, 238, 238); text-align: left; border: 1px solid rgb(0, 0, 0); vertical-align: top; font-family: Consolas, &quot;Lucida Console&quot;, Courier, mono; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAAeCAYAAADtlXTHAAAASUlEQVQYVxXE4QaDABgAwC+KoigSxaKxMVEURdH7P9bc/biIiCQiIlWmXIVKVarVqFWnXoNeGjXprY+++mnWolWbdh06denW8we0DwOySWODmQAAAABJRU5ErkJggg==&quot;); background-repeat: repeat-x;">Object</th>')
					('</tr>')
					('<tr>')
						('<th style="padding: 5px; background-color: rgb(238, 238, 238); color: #000; text-align: left; border: 1px solid rgb(0, 0, 0); vertical-align: top; font-family: Consolas, &quot;Lucida Console&quot;, Courier, mono; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAAeCAYAAADtlXTHAAAASUlEQVQYVxXE4QaDABgAwC+KoigSxaKxMVEURdH7P9bc/biIiCQiIlWmXIVKVarVqFWnXoNeGjXprY+++mnWolWbdh06denW8we0DwOySWODmQAAAABJRU5ErkJggg==&quot;); background-repeat: repeat-x; text-transform: uppercase;">key</th>')
						('<th style="padding: 5px; background-color: rgb(238, 238, 238); color: #000; text-align: left; border: 1px solid rgb(0, 0, 0); vertical-align: top; font-family: Consolas, &quot;Lucida Console&quot;, Courier, mono; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAAeCAYAAADtlXTHAAAASUlEQVQYVxXE4QaDABgAwC+KoigSxaKxMVEURdH7P9bc/biIiCQiIlWmXIVKVarVqFWnXoNeGjXprY+++mnWolWbdh06denW8we0DwOySWODmQAAAABJRU5ErkJggg==&quot;); background-repeat: repeat-x; text-transform: uppercase;">value</th>')
					('</tr>')
				('</thead>')
				('<tbody>')();
			for(var o in obj){
				if(shop.is_obj(obj[o])){
					if(shop.is_arr(obj[o])){
						tmp = shop.admin.manage_site.getCGlobalArray(obj[o]);
					}else{
						tmp = shop.admin.manage_site.getCGlobalObject(obj[o]);
					}
				}else{
					tmp = obj[o];
				}
				strHTML += shop.join('<tr>')
					('<td colspan="1" style="padding: 5px; background-color: rgb(255, 255, 255); border: 1px solid rgb(0, 0, 0); vertical-align: top; font-family: Consolas, &quot;Lucida Console&quot;, Courier, mono; white-space: nowrap;">'+o+'</td>')
					('<td colspan="1" style="padding: 5px; background-color: rgb(255, 255, 255); border: 1px solid rgb(0, 0, 0); vertical-align: top; font-family: Consolas, &quot;Lucida Console&quot;, Courier, mono; white-space: nowrap;">'+tmp+'</td>')
				('</tr>')();
			}
			strHTML += '</tbody></table>';
		}
		return strHTML;
	},
	getCGlobalArray:function(obj){
		var strHTML = '', tmp = '';
		if(Object.keys(obj).length === 0){
			strHTML = 'Empty Array';
		}else{
			strHTML = shop.join
			('<table style="border-collapse: collapse; width: 100%; font-size: 12px; color: rgb(34, 34, 34);">')
				('<thead>')
					('<tr>')
						('<th colspan="2" style="padding: 5px; background-color: #6DBD2A; color: #fff; text-align: left; border: 1px solid rgb(0, 0, 0); vertical-align: top; font-family: Consolas, &quot;Lucida Console&quot;, Courier, mono; background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAAeCAYAAADtlXTHAAAASUlEQVQYVxXE4QaDABgAwC+KoigSxaKxMVEURdH7P9bc/biIiCQiIlWmXIVKVarVqFWnXoNeGjXprY+++mnWolWbdh06denW8we0DwOySWODmQAAAABJRU5ErkJggg==&quot;); background-repeat: repeat-x;">Array</th>')
					('</tr>')
				('</thead>')
				('<tbody>')();
			for(var o in obj){
				if(shop.is_arr(obj[o])){
					tmp = shop.admin.manage_site.getCGlobalArray(obj[o]);
				}else{
					tmp = obj[o];
				}
				strHTML += shop.join('<tr>')
					('<td colspan="1" style="padding: 5px; background-color: rgb(255, 255, 255); border: 1px solid rgb(0, 0, 0); vertical-align: top; font-family: Consolas, &quot;Lucida Console&quot;, Courier, mono; white-space: nowrap;">'+tmp+'</td>')
				('</tr>')();
			}
			strHTML += '</tbody></table>';
		}
		return strHTML;
	}
};
shop.openCloseStatus = function(id, ctrl){
	var panel = jQuery('#thongbaonghi');
	if(panel.hasClass('hide_me')){
		if(id == 'status_close'){
			panel.removeClass('hide_me');
		}
	}else if(id != 'status_close'){
		panel.addClass('hide_me');
	}
	if(ctrl){
		var e = shop.get_ele(ctrl);
		if(e){
			e.checked = true;
		}
	}
};