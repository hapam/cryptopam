shop.moduleAuto = {
	loadTable:function(table){
		var edit_mode = shop.get_ele('edit_mode_fine'),
		search_form = shop.get_ele('search_form');
		table = table ? table : $('#table').val();
		shop.ajax_popup('act=sysModuleAuto&code=load-table','POST',{table:table, edit_mode: edit_mode.checked ? 1 : 0, search_form: search_form.checked ? 1 : 0},function(j){
			if(j.err == 0){
				jQuery('#formGroup-configGroup').html(j.html);
				if ($.fn.selectpicker) {
					$('select.form-control').selectpicker('refresh');
				}
			}else{
				alert(j.msg);
			}
		});
	},
	delInput:function(key, title){
		shop.confirm('Bạn có chắc chắn muốn xóa <b>'+title+'</b>', function(){
			$('#'+key).remove();
			swal('Đã xóa thành công!', 'Trường '+title+' đã bị xóa khỏi danh sách', 'success');
		});
	}
};