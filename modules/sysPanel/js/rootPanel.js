shop.rootPanel = {
	conf:{start:0, page_id:0, mLink:'', page:{}},
	mode:{debug:0, edit:0, panel:0},
	init:function(pid, mLink, page){
		if(shop.rootPanel.conf.start == 0){
			//pannel mode
			shop.rootPanel.mode.panel = parseInt(shop.cookie.get('rootPanel'));
			if(isNaN(shop.rootPanel.mode.panel)){shop.rootPanel.mode.panel = 0}
			//get edit mode
			shop.rootPanel.mode.edit = parseInt(shop.cookie.get('editMode'));
			if(isNaN(shop.rootPanel.mode.edit)){shop.rootPanel.mode.edit = 0}
			//get debug mode
			shop.cookie.mode = 1;
			shop.rootPanel.mode.debug = parseInt(shop.cookie.get('debug'));
			if(isNaN(shop.rootPanel.mode.debug)){shop.rootPanel.mode.debug = 0}
			shop.cookie.mode = 0;
			//update page info
			shop.rootPanel.conf.page_id = pid;
			shop.rootPanel.conf.mLink = mLink;
			shop.rootPanel.conf.page = page;
			//started
			shop.rootPanel.conf.start = 1;
		}
	},
	go:function(pid, mLink, page_name){
		//check permission
		if(IS_LOGIN && IS_ROOT){
			//init
			shop.rootPanel.init(pid, mLink, page_name);
			//theme
			jQuery('body').prepend(shop.rootPanel.theme.panel());
			jQuery(document).ready(function(){
				//active block
				if(shop.rootPanel.mode.edit == 1 && shop.rootPanel.checkActiveUrl()){
					shop.rootPanel.editMode.lightBlock();
				}
			});
		}
	},
	checkActiveUrl:function(){
		return (document.URL.indexOf('/module') == -1) && (document.URL.indexOf('page=module') == -1);
	},
	editMode:{
		lightBlock:function(){
			jQuery('.regions').each(function(){
				var cl = this.className, i, idx;
				if(cl != ''){
					cl = cl.split(' ');
					if(cl.length > 1){
						for(i=0;i<cl.length;i++){
							if(cl[i].indexOf('region_') != -1){
								jQuery(this).addClass('editMode');
								jQuery(this).prepend(shop.rootPanel.theme.regionName(cl[i].substring(7)));
								break;
							}
						}
					}
				}
			});
		},
		listModule:function(name){
			shop.ajax_popup("act=panel&code=list-moule", "POST", {page_id:shop.rootPanel.conf.page_id},function(j){
				if(j.err == -1){
				}else{
					shop.show_overlay_popup('module-block-list', 'Danh sách module',
						shop.popupSite('module-block-list', 'Danh sách module', shop.rootPanel.theme.moduleList(j.data, name)),
						{
							background: {'background-color' : 'transparent'},
							title: {'display' : 'none'},
							border: {
								'background-color' : 'transparent',
								'padding' : '0px'
							},
							content: {
								'padding' : '0px',
								'width' : '600px'
							}
						}
					);
				}
			});
		},
		removeModule:function(block_id){
			shop.confirm("Bạn muốn xóa module khỏi trang này ?", function(){
				shop.ajax_popup("act=panel&code=remove-module", "POST", {page_id:shop.rootPanel.conf.page_id, block_id:block_id},
				function(j){
					if(j.err == -1){
						alert(j.msg);
					}else{
						jQuery('#block-'+block_id).fadeOut('fast',function(){jQuery(this).html('').remove()});
					}
				});
			});
		},
		addModule:function(region, block_id, block_name){
			shop.confirm("Bạn muốn xóa module khỏi trang này ?", function(){
				shop.ajax_popup("act=panel&code=add-module", "POST", {page_id:shop.rootPanel.conf.page_id, block_id:block_id, region: region},
				function(j){
					if(j.err == -1){
						alert(j.msg);
					}else{
						shop.reload();
					}
				});
			});
		},
		moving:function(type, block_id){
			if(type != '0'){
				shop.ajax_popup("act=panel&code=move-module", "POST", {page_id:shop.rootPanel.conf.page_id, block_id:block_id, type: type},
				function(j){
					if(j.err == -1){
						alert(j.msg);
					}else{
						shop.reload();
					}
				});
			}
		}
	},
	onOffMode:function(type, on){
		on = (on != undefined) ? on : 1;
		type = (type != undefined) ? type : 'debug';
		//set cookie
		if(type == 'debug'){shop.cookie.mode = 1}
		shop.cookie.set(type, on, 86400*365, '/');
		if(type == 'debug'){shop.cookie.mode = 0}
		//reload
		shop.reload();
	},
	panelMode:function(){
		shop.rootPanel.mode.panel = (shop.rootPanel.mode.panel <= 0) ? 1 : 0;
		shop.cookie.set('rootPanel', shop.rootPanel.mode.panel, 86400*365, '/');
		jQuery('.rootPanel .content').slideToggle();
	},
	theme:{
		panel:function(){
			var $buildPage = BASE_URL+'edit_page.html?id='+shop.rootPanel.conf.page_id,
			$editPage = BASE_URL+'page/edit.html?id='+shop.rootPanel.conf.page_id,
			$delCache = BASE_URL+'page/refresh.html?id='+shop.rootPanel.conf.page_id+'&href='+query_string;
			return shop.join
			('<div class="rootPanel">')
				('<div class="content'+(shop.rootPanel.mode.panel>0?'':' hide_me')+'">')
					('<div class="pull-left">Debug: <select id="debug_mode" onchange="shop.rootPanel.onOffMode(\'debug\', this.value)">')
						('<option value="0"'+(shop.rootPanel.mode.debug == 0 ? ' selected' : '')+'>Tắt</option>')
						('<option value="1"'+(shop.rootPanel.mode.debug == 1 ? ' selected' : '')+'>Bật</option>')
					('</select></div>')
					('<div class="pull-left m-l-20">Edit Layout: &nbsp;&nbsp;<select id="edit_mode" onchange="shop.rootPanel.onOffMode(\'editMode\', this.value)">')
						('<option value="0"'+(shop.rootPanel.mode.edit == 0 ? ' selected' : '')+'>Tắt</option>')
						('<option value="1"'+(shop.rootPanel.mode.edit == 1 ? ' selected' : '')+'>Bật</option>')
					('</select></div><div class="c"></div>')
					('<div class="m-t-10">')
						('<fieldset>')
							('<legend>Trang hiện tại</legend>')
							('<div class="p-l-20">')
								('<div>Tên trang: <b>'+shop.rootPanel.conf.page.title+'</b></div>')
								('<div class="m-t-5">URL: <b>'+shop.rootPanel.conf.page.name+'</b></div>' + ((shop.rootPanel.conf.page.rewrite && shop.rootPanel.conf.page.rewrite != '') ? '<div class="m-t-5">Rewrite: <b>'+shop.rootPanel.conf.page.rewrite+'</b></div>' : ''))
								('<div class="m-t-5">Layout: <b>'+shop.rootPanel.conf.page.layout+'</b></div>' + ((shop.rootPanel.conf.page.layout_mobile && shop.rootPanel.conf.page.layout_mobile != '') ? '<div class="m-t-5">Layout Mobile: <b>'+shop.rootPanel.conf.page.layout_mobile+'</b></div>' : ''))
								('<div class="m-t-5"><a href="'+$buildPage+'">Bố cục trang</a> | ')
								('<a href="'+$editPage+'">Sửa trang</a> | ')
								('<a href="'+$delCache+'">Xoá cache trang</a></div>')
							('</div>')
						('</fieldset>')
					('</div>')
				('</div>')
				('<div class="showArrow" onclick="shop.rootPanel.panelMode()"></div>')
			('</div>')();
		},
		regionName:function(name){
			return shop.join
			('<div class="regionName">'+name)
				(' <span>-</span> ')
				('<a href="javascript:void(0)" onclick="shop.rootPanel.editMode.listModule(\''+name+'\')">Thêm module</a>')
			('</div>')();
		},
		moduleList:function(data, name){
			var page = shop.rootPanel.conf.page_id, i, j,
			html = shop.join
			('<div class="list-module"><table class="list-panel" cellpadding="0" cellspacing="1" width="100%">')
				('<thead>')
					('<tr>')
						('<th align="center">Tên Module</th>')
						('<th align="center">Các Page đang áp dụng</th>')
					('</tr>')
				('</thead>')();
			for(i in data){
				html += shop.join
				('<tr>')
					('<td><a href="javascript:void(0)" onclick="shop.rootPanel.editMode.addModule(\''+name+'\', '+data[i].id+', \''+data[i].name+'\')">'+data[i].name+'</a></td>')
					('<td>')();
					for(j in data[i].pages){
						html += '[<a href="?q='+data[i].pages[j].name+'" target="_blank">'+data[i].pages[j].name+'</a>]&nbsp;&nbsp;&nbsp;';
					}
				html+='</td></tr>';
			}
			html += '</table></div>';
			return html;
		}
	}
};