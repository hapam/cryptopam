shop.module = {
	install:function(){
		var checked = false;
		jQuery(".checkallnotIn").each(function(){
			if(!checked){
				checked = this.checked;
			}
		});
		if(!checked){
			alert('Vui lòng chọn Module để cài đặt');
		}else{
			jQuery('#action_form').val('install');
			document.ListModuleAdminForm.submit();
		}
	},
	uninstall:function(){
		//var checked = false;
		//jQuery(".checkRemove").each(function(){
		//	if(!checked){
		//		checked = this.checked;
		//	}
		//});
		//if(!checked){
		//	alert('Vui lòng chọn Module cần gỡ bỏ');
		//}else{
			jQuery('#action_form').val('uninstall');
			document.ListModuleAdminForm.submit();
		//}
	},
	delFile:function(module_name){
		if(module_name != ''){
			shop.confirm("Bạn có chắc chắn xóa Module khỏi hệ thống?", function(){
				jQuery('#action_form').val('del::'+module_name);
				document.ListModuleAdminForm.submit();
			});
		}
	}
};