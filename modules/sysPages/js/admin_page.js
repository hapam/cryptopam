shop.admin.page = {
	click:function(themes,mobile){
		shop.ajax_popup('act=sysPages&code=load-layout','GET',{themes: themes ? themes : '', mobile: mobile ? mobile : ''},function(j){
			if(j.err == -1){
				shop.show_popup_message("Thao tác không thành công","Thông báo lỗi",-1);
			}else{
				var opt = '', i, id = themes != '' ? 'layout' : 'layout_mobile';
				for(i in j.layout){
					opt += '<option value="'+i+'"'+(((themes == 'sys' && i == jQuery('#old_layout').val()) || (mobile == 'no_mobile' && i == jQuery('#old_layout_mobile').val())) ? ' selected':'')+'>'+j.layout[i]+'</option>';
				}
				jQuery('#'+id).html(opt);
				//update uniform
				if(shop.is_exists(jQuery.uniform)){
					jQuery.uniform.update("#"+id);
				}
				if ($.fn.selectpicker) {
					$('#'+id).selectpicker('refresh');
				}
			}
		});
	},
	changeType:function(val){
		if(val){
			$('#formGroup-special-parent').hide();
			shop.ajax_popup('act=sysPages&code=load-layout','GET',{themes: '', mobile: '', admin: 1},function(j){
				if(j.err == -1){
					shop.show_popup_message("Thao tác không thành công","Thông báo lỗi",-1);
				}else{
					var opt = '', i, id = 'layout';
					for(i in j.layout){
						opt += '<option value="'+i+'"'+((i == jQuery('#old_layout').val()) ? ' selected':'')+'>'+j.layout[i]+'</option>';
					}
					jQuery('#'+id).html(opt);
					//update uniform
					if(shop.is_exists(jQuery.uniform)){
						jQuery.uniform.update("#"+id);
					}
					if ($.fn.selectpicker) {
						$('#'+id).selectpicker('refresh');
					}
				}
			});
		}else{
			$('#formGroup-special-parent').show();
			shop.admin.page.click('sys', '');
		}
	}
};